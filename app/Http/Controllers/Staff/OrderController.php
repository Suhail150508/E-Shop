<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('staff_id', auth()->id())
            ->with(['user'])
            ->latest()
            ->paginate(10);

        return view('staff.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Policy check: Ensure order belongs to staff
        if ($order->staff_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['items.product', 'user']);

        return view('staff.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        if ($order->staff_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $request->validate([
            'status' => 'required|in:'.implode(',', [
                Order::STATUS_PENDING,
                Order::STATUS_PROCESSING,
                Order::STATUS_SHIPPED,
                Order::STATUS_DELIVERED,
                Order::STATUS_CANCELLED,
            ]),
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Order status updated successfully.');
    }
}
