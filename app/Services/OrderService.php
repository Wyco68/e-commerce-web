<?php

namespace App\Services;

use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Exceptions\InvalidOrderTransitionException;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Strict state machine transition map.
     * Key = current status, Value = allowed next statuses.
     *
     * Note: transitions to STATUS_PAID must go through markAsPaid(), not updateStatus().
     */
    private const TRANSITIONS = [
        Order::STATUS_PENDING_PAYMENT => [
            Order::STATUS_PENDING,
            Order::STATUS_CANCELLED,
        ],
        Order::STATUS_PENDING => [
            Order::STATUS_CANCELLED,
        ],
        Order::STATUS_PAID => [
            Order::STATUS_PROCESSING,
            Order::STATUS_SHIPPED,
            Order::STATUS_CANCELLED,
        ],
        Order::STATUS_PROCESSING => [
            Order::STATUS_SHIPPED,
            Order::STATUS_CANCELLED,
        ],
        Order::STATUS_SHIPPED => [
            Order::STATUS_COMPLETED,
            Order::STATUS_RETURN_REQUESTED,
        ],
        Order::STATUS_COMPLETED => [
            Order::STATUS_RETURN_REQUESTED,
        ],
        Order::STATUS_RETURN_REQUESTED => [
            Order::STATUS_RETURNED,
            Order::STATUS_REFUNDED,
            Order::STATUS_COMPLETED,
        ],
        Order::STATUS_CANCELLED  => [],
        Order::STATUS_REFUNDED   => [],
        Order::STATUS_RETURNED   => [],
    ];

    public function __construct(
        private readonly InventoryService $inventoryService,
        private readonly CartService $cartService,
    ) {}

    public function createFromCart(
        User $user,
        Cart $cart,
        int $paymentMethodId,
        ?string $notes = null,
    ): Order {
        $order = DB::transaction(function () use ($user, $cart, $paymentMethodId, $notes) {
            $summary = $this->cartService->getCartSummary($cart);

            if (empty($summary['items'])) {
                throw new \RuntimeException('Cart is empty.');
            }

            foreach ($summary['items'] as $item) {
                if (! $this->inventoryService->checkAvailability($item['variant']->id, $item['quantity'])) {
                    throw new \RuntimeException("Insufficient stock for {$item['product']->name} ({$item['variant']->sku})");
                }
            }

            $order = Order::create([
                'user_id'           => $user->id,
                'payment_method_id' => $paymentMethodId,
                'status'            => Order::STATUS_PENDING_PAYMENT,
                'subtotal'          => $summary['subtotal'],
                'discount_total'    => $summary['discount_total'],
                'total'             => $summary['total'],
                'currency'          => 'USD',
                'notes'             => $notes,
            ]);

            OrderStatusHistory::create([
                'order_id'    => $order->id,
                'from_status' => null,
                'to_status'   => $order->status,
                'note'        => 'Order created.',
            ]);

            foreach ($summary['items'] as $item) {
                $order->orderItems()->create([
                    'product_id'             => $item['product']->id,
                    'variant_id'             => $item['variant']->id,
                    'product_name_snapshot'  => $item['product']->name,
                    'sku_snapshot'           => $item['variant']->sku,
                    'unit_price'             => $item['unit_price'],
                    'discount_amount'        => $item['discount_amount'] / max($item['quantity'], 1),
                    'final_price'            => ($item['final_line_total']) / max($item['quantity'], 1),
                    'quantity'               => $item['quantity'],
                ]);

                $this->inventoryService->reserveStock(
                    $item['variant']->id,
                    $item['quantity'],
                    $order->id
                );
            }

            $this->cartService->clearCart($cart);

            return $order->load('orderItems');
        });

        event(new OrderPlaced($order));

        return $order;
    }

    public function cancelOrder(Order $order): void
    {
        if (! $order->isCancellable()) {
            throw new \RuntimeException('Order cannot be cancelled in its current state.');
        }

        $fromStatus = $order->status;

        DB::transaction(function () use ($order, $fromStatus) {
            foreach ($order->orderItems as $item) {
                if ($item->variant_id) {
                    $this->inventoryService->releaseStock(
                        $item->variant_id,
                        $item->quantity,
                        $order->id
                    );
                }
            }

            $order->update(['status' => Order::STATUS_CANCELLED]);

            OrderStatusHistory::create([
                'order_id'    => $order->id,
                'from_status' => $fromStatus,
                'to_status'   => Order::STATUS_CANCELLED,
                'note'        => 'Order cancelled.',
            ]);
        });

        event(new OrderStatusUpdated($order->fresh(), $fromStatus, Order::STATUS_CANCELLED));
    }

    /**
     * Verify payment and mark order paid. Requires proof unless admin_note is provided.
     *
     * @throws \RuntimeException
     */
    public function processPayment(Order $order, PaymentService $paymentService, ?string $adminNote = null): void
    {
        $payment = $order->latestPayment;
        $hasProof = $payment && $payment->proof_path;

        if ($hasProof) {
            $paymentService->verifyPayment($payment);

            return;
        }

        if (empty(trim($adminNote ?? ''))) {
            throw new \RuntimeException(
                'Payment proof is required. To override manually, provide an admin note explaining why.'
            );
        }

        $payment = $payment ?? $paymentService->initiatePayment($order);
        $paymentService->verifyPayment($payment, 'Manual override: '.trim($adminNote));
    }

    public function markAsPaid(Order $order, Payment $payment, ?string $note = null): void
    {
        DB::transaction(function () use ($order, $note) {
            $fresh = Order::lockForUpdate()->findOrFail($order->id);

            if ($fresh->status !== Order::STATUS_PENDING_PAYMENT) {
                return;
            }

            foreach ($fresh->orderItems as $item) {
                if ($item->variant_id) {
                    $this->inventoryService->deductStock(
                        $item->variant_id,
                        $item->quantity,
                        $fresh->id
                    );
                }
            }

            $fromStatus = $fresh->status;
            $fresh->update(['status' => Order::STATUS_PAID]);

            OrderStatusHistory::create([
                'order_id'    => $fresh->id,
                'from_status' => $fromStatus,
                'to_status'   => Order::STATUS_PAID,
                'note'        => $note ?? 'Payment verified.',
            ]);

            $order->status = $fresh->status;
        });

        event(new OrderStatusUpdated($order->fresh(), Order::STATUS_PENDING_PAYMENT, Order::STATUS_PAID));
    }

    /**
     * @throws InvalidOrderTransitionException
     */
    public function updateStatus(Order $order, string $newStatus, ?string $note = null): void
    {
        if ($newStatus === Order::STATUS_PAID) {
            throw new InvalidOrderTransitionException($order->status, $newStatus);
        }

        $fromStatus = $order->status;

        DB::transaction(function () use ($order, $newStatus, $note, &$fromStatus) {
            $fresh = Order::lockForUpdate()->with('orderItems')->findOrFail($order->id);

            $this->validateTransition($fresh, $newStatus);

            $fromStatus = $fresh->status;
            $fresh->update(['status' => $newStatus]);

            OrderStatusHistory::create([
                'order_id'    => $fresh->id,
                'from_status' => $fromStatus,
                'to_status'   => $newStatus,
                'note'        => $note,
            ]);

            if ($newStatus === Order::STATUS_REFUNDED) {
                $this->restockOrderItems($fresh);
            }

            $order->status = $newStatus;
        });

        event(new OrderStatusUpdated($order->fresh(), $fromStatus, $newStatus));
    }

    /**
     * @throws InvalidOrderTransitionException
     */
    public function requestReturn(Order $order, ?string $note = null): void
    {
        $this->updateStatus($order, Order::STATUS_RETURN_REQUESTED, $note);
    }

    /**
     * @throws InvalidOrderTransitionException
     */
    private function validateTransition(Order $order, string $newStatus): void
    {
        $allowed = self::TRANSITIONS[$order->status] ?? null;

        if ($allowed === null) {
            throw new InvalidOrderTransitionException($order->status, $newStatus);
        }

        if (! in_array($newStatus, $allowed, true)) {
            throw new InvalidOrderTransitionException($order->status, $newStatus);
        }
    }

    private function restockOrderItems(Order $order): void
    {
        foreach ($order->orderItems as $item) {
            if ($item->variant_id) {
                $this->inventoryService->adjustStock(
                    $item->variant_id,
                    $item->quantity,
                    'Refund restock for order #'.$order->id
                );
            }
        }
    }
}
