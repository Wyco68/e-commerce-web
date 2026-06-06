<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly SecureUploadService $secureUpload,
    ) {}

    public function initiatePayment(Order $order, ?string $provider = null): Payment
    {
        $provider ??= $order->paymentMethod?->code ?? 'transfer';

        return Payment::create([
            'order_id' => $order->id,
            'provider' => $provider,
            'status'   => Payment::STATUS_PENDING,
            'amount'   => $order->total,
            'currency' => $order->currency ?? 'USD',
        ]);
    }

    public function uploadProof(Payment $payment, UploadedFile $file): Payment
    {
        $hash = hash_file('sha256', $file->getRealPath());

        $duplicate = Payment::where('proof_hash', $hash)
            ->where('id', '!=', $payment->id)
            ->exists();

        if ($duplicate) {
            throw new \RuntimeException('This payment proof has already been submitted for another order.');
        }

        $path = $this->secureUpload->storeImage(
            $file,
            'payment-proofs',
            SecureUploadService::paymentProofMimes(),
            5120,
            'private',
        );

        $payment->update([
            'proof_path' => $path,
            'proof_hash' => $hash,
        ]);

        return $payment->fresh();
    }

    public function verifyPayment(Payment $payment, ?string $note = null): void
    {
        $order = Order::lockForUpdate()->findOrFail($payment->order_id);

        if ($order->status !== Order::STATUS_PENDING_PAYMENT) {
            return;
        }

        DB::transaction(function () use ($payment, $order, $note) {
            $payment->update([
                'status'  => Payment::STATUS_VERIFIED,
                'paid_at' => now(),
            ]);

            $this->orderService->markAsPaid($order, $payment, $note);
        });
    }

    public function rejectPayment(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
            ]);

            $this->orderService->cancelOrder($payment->order);
        });
    }
}
