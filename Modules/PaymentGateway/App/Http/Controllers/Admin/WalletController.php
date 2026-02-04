<?php

namespace Modules\PaymentGateway\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Modules\PaymentGateway\App\Models\WalletTransaction;

class WalletController extends Controller
{
    protected $settingService;

    protected $orderService;

    public function __construct(SettingService $settingService, OrderService $orderService)
    {
        $this->settingService = $settingService;
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the customers with wallet status.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'customer');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('wallet_status', true);
            } elseif ($request->status === 'inactive') {
                $query->where('wallet_status', false);
            }
        }

        $users = $query->latest()->paginate(10);

        return view('paymentgateway::admin.wallet.index', compact('users'));
    }

    public function transactions(Request $request)
    {
        $query = WalletTransaction::with('user');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('date_range')) {
            // Format: YYYY-MM-DD to YYYY-MM-DD
            $dates = explode(' to ', $request->date_range);
            if (count($dates) == 2) {
                $query->whereBetween('created_at', [$dates[0].' 00:00:00', $dates[1].' 23:59:59']);
            }
        }

        $transactions = $query->latest()->paginate(10);

        return view('paymentgateway::admin.wallet.transactions', compact('transactions'));
    }

    public function settings()
    {
        $settings = [
            'wallet_deposit_limit' => $this->settingService->get('wallet_deposit_limit', 50000),
        ];

        return view('paymentgateway::admin.wallet.settings', compact('settings'));
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $user->wallet_status = $request->status;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => __('Wallet status updated successfully.'),
        ]);
    }

    public function approveTransaction(Request $request, WalletTransaction $transaction)
    {
        if ($transaction->status !== 'pending') {
            return back()->with('error', __('This transaction is not pending.'));
        }

        // If it's a deposit (credit), we need to update the associated order if exists
        if ($transaction->type === 'credit' && $transaction->payment_transaction_id) {
            $order = Order::where('order_number', $transaction->payment_transaction_id)->first();

            if ($order && $order->status === Order::STATUS_PENDING) {
                // This will handle wallet credit via OrderService logic
                $this->orderService->changeStatus($order, Order::STATUS_PROCESSING, \Auth::id());
                // The wallet transaction status update is handled in OrderService now,
                // but if we want to be sure, we can check.
                // Actually, OrderService updates it to 'approved'.

                return back()->with('success', __('Transaction approved successfully.'));
            }
        }

        // Fallback for manual transactions without order or other types
        $transaction->update(['status' => 'approved']);

        if ($transaction->type === 'credit') {
            $transaction->user->increment('wallet_balance', $transaction->amount);
        } elseif ($transaction->type === 'debit') {
            // Usually debit is already deducted, but if it was pending deduction:
            $transaction->user->decrement('wallet_balance', $transaction->amount);
        }

        return back()->with('success', __('Transaction approved successfully.'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'wallet_deposit_limit' => 'required|numeric|min:0',
        ]);

        $this->settingService->set('wallet_deposit_limit', $request->wallet_deposit_limit);

        return back()->with('success', __('Wallet settings updated successfully.'));
    }
}
