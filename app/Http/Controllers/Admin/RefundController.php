<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\PaymentGateway\App\Models\WalletTransaction;

class RefundController extends Controller
{
    /**
     * Display a listing of the refund requests.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Refund::with(['order', 'user']);

        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search !== null && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('order', function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%");
            });
        }

        $refunds = $query->latest()->paginate(10);
        return view('admin.refunds.index', compact('refunds'));
    }

    /**
     * Display the specified refund request.
     *
     * @param Refund $refund
     * @return \Illuminate\View\View
     */
    public function show(Refund $refund)
    {
        return view('admin.refunds.show', compact('refund'));
    }

    /**
     * Update the specified refund request status.
     *
     * @param Request $request
     * @param Refund $refund
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Refund $refund)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_note' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $refund) {
                // Check if we are approving a previously non-approved refund
                if ($request->status === 'approved' && $refund->status !== 'approved') {
                    
                    // Logic for Wallet Refund
                    // If the order was paid via Wallet, credit the amount back to the user's wallet
                    if ($refund->order->payment_method === 'wallet' && $refund->order->user) {
                        $user = $refund->order->user;
                        
                        // Credit the wallet
                        $user->increment('wallet_balance', $refund->amount);

                        // Create Wallet Transaction History
                        WalletTransaction::create([
                            'user_id' => $user->id,
                            'amount' => $refund->amount,
                            'type' => 'refund',
                            'description' => 'Refund for Order #' . $refund->order->order_number,
                            'payment_method' => 'wallet',
                            'payment_transaction_id' => $refund->order->id, // Reference: Order ID
                            'status' => 'approved',
                        ]);
                    }

                    // Update order payment status to refunded
                    $refund->order->update([
                        'payment_status' => Order::PAYMENT_REFUNDED,
                    ]);
                }

                $refund->update([
                    'status' => $request->status,
                    'admin_note' => $request->admin_note,
                ]);
            });

            return back()->with('success', __('Refund request updated successfully.'));

        } catch (\Exception $e) {
            return back()->with('error', __('Something went wrong: ') . $e->getMessage());
        }
    }
}
