<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    /**
     * Store a newly created refund request in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== Order::STATUS_DELIVERED) {
            return back()->with('error', __('Refund request can only be made for delivered orders.'));
        }
        
        // Check if refund already exists
        if ($order->refunds()->exists()) {
             return back()->with('error', __('A refund request has already been submitted for this order.'));
        }

        $validReasons = \App\Models\RefundReason::where('status', true)->pluck('reason')->all();
        $validReasons[] = 'Other';

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) use ($validReasons) {
                if (! in_array($value, $validReasons)) {
                    $fail(__('Invalid refund reason selected.'));
                }
            }],
            'details' => 'nullable|string|max:2000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('refunds', 'public');
                $imagePaths[] = $path;
            }
        }

        Refund::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'reason' => $validated['reason'],
            'details' => $validated['details'] ?? null,
            'amount' => $order->total, // Full refund by default for now
            'status' => Refund::STATUS_PENDING,
            'images' => $imagePaths,
        ]);

        return back()->with('success', __('Refund request submitted successfully.'));
    }
}
