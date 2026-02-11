<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\PaymentGateway\App\Services\WalletService;

class RefundController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

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

        if ($request->filled('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->input('search'));
            $query->whereHas('order', function ($q) use ($search) {
                $q->where('order_number', 'like', '%'.$search.'%');
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
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            DB::transaction(function () use ($request, $refund) {
                // Lock refund row to prevent double approval (e.g. duplicate request)
                $refund = Refund::where('id', $refund->id)->lockForUpdate()->firstOrFail();

                // Check if we are approving a previously non-approved refund
                if ($request->status === 'approved' && $refund->status !== 'approved') {

                    // Check if order is already refunded to prevent double refund
                    if ($refund->order->payment_status === Order::PAYMENT_REFUNDED) {
                        throw new \Exception(__('common.refund_already_processed'));
                    }

                    // Logic for Wallet Refund (Store Credit)
                    // We credit the wallet regardless of payment method (as Store Credit)
                    if ($refund->order->user) {
                        $this->walletService->credit(
                            $refund->order->user,
                            $refund->amount,
                            __('common.refund_for_order') . ' #' . $refund->order->order_number,
                            'refund', // Type
                            $refund->order->order_number // Transaction ID
                        );
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

            return back()->with('success', __('common.refund_updated_success'));

        } catch (\Exception $e) {
            Log::error('Refund update failed', ['id' => $refund->id, 'error' => $e->getMessage()]);
            return back()->with('error', __('common.error_generic'));
        }
    }
}
