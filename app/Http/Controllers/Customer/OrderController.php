<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RefundReason;
use App\Services\OrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();

        // Fetch user's orders with refunds, ordered by latest
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['refunds'])
            ->latest()
            ->paginate(10);

        return view('frontend.orders.index', compact('user', 'orders'));
    }

    public function show(Request $request, Order $order)
    {
        // Get the authenticated user
        $user = $request->user();

        // Ensure the order belongs to the user
        if ($order->user_id !== $user->id) {
            abort(403);
        }

        // Load order items and refunds
        $order->load(['items.product', 'refunds']);
        
        // Fetch active refund reasons
        $refundReasons = RefundReason::where('status', true)->get();

        return view('frontend.orders.show', compact('user', 'order', 'refundReasons'));
    }

    public function cancel(Request $request, Order $order)
    {
        // Get the authenticated user
        $user = $request->user();

        // Ensure the order belongs to the user
        if ($order->user_id !== $user->id) {
            abort(403);
        }

        // Check if order is pending
        if ($order->status !== Order::STATUS_PENDING) {
            return back()->with('error', __('common.order_cannot_be_cancelled'));
        }

        try {
            // Cancel the order
            $this->orderService->changeStatus($order, Order::STATUS_CANCELLED);
        } catch (\Exception $e) {
            return back()->with('error', __('common.error_generic') . ': ' . $e->getMessage());
        }

        return back()->with('success', __('common.order_cancelled_success'));
    }

    public function invoice(Request $request, Order $order)
    {
        // Ensure the order belongs to the user
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        // Load order items
        $order->load(['items']);

        // Generate PDF invoice
        $pdf = Pdf::loadView('orders.invoice', [
            'order' => $order,
        ]);

        $filename = 'invoice-'.$order->order_number.'.pdf';

        return $pdf->download($filename);
    }
}
