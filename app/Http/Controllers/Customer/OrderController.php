<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::where('user_id', $request->user()->id)
            ->with(['refunds'])
            ->latest()
            ->paginate(10);

        return view('frontend.orders.index', compact('user', 'orders'));
    }

    public function show(Request $request, Order $order)
    {
        $user = $request->user();

        if ($order->user_id !== $user->id) {
            abort(403);
        }

        $order->load(['items.product', 'refunds']);
        
        $refundReasons = \App\Models\RefundReason::where('status', true)->get();

        return view('frontend.orders.show', compact('user', 'order', 'refundReasons'));
    }

    public function invoice(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        $order->load(['items']);

        $pdf = Pdf::loadView('orders.invoice', [
            'order' => $order,
        ]);

        $filename = 'invoice-'.$order->order_number.'.pdf';

        return $pdf->download($filename);
    }
}
