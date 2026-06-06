<?php

namespace App\Http\Controllers;

use App\Events\RefundRequested;
use App\Models\Order;
use App\Models\RefundRequest;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    public function store(Request $request, Order $order)
    {
        $this->authorize('view', $order);

        if (! $order->isRefundable()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'This order is not eligible for a refund.');
        }

        if ($order->refundRequest()->exists()) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'A refund request already exists for this order.');
        }

        $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $refundRequest = DB::transaction(function () use ($request, $order) {
            $refund = RefundRequest::create([
                'order_id' => $order->id,
                'user_id'  => $request->user()->id,
                'reason'   => $request->input('reason'),
                'status'   => RefundRequest::STATUS_PENDING,
            ]);

            $this->orderService->requestReturn(
                $order,
                'Refund requested: '.$request->input('reason'),
            );

            return $refund;
        });

        event(new RefundRequested($refundRequest));

        return redirect()->route('orders.show', $order)
            ->with('success', 'Your refund request has been submitted and is under review.');
    }
}
