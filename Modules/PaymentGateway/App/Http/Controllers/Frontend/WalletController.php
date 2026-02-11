<?php

namespace Modules\PaymentGateway\App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\PaymentGateway\App\Services\PaymentManager;

class WalletController extends Controller
{
    protected OrderService $orders;

    protected PaymentManager $payments;

    protected SettingService $settings;

    public function __construct(OrderService $orders, PaymentManager $payments, SettingService $settings)
    {
        $this->orders = $orders;
        $this->payments = $payments;
        $this->settings = $settings;
    }

    public function index(): View
    {
        $user = Auth::user();
        $transactions = $user->walletTransactions()->latest()->paginate(10);
        $depositLimit = (float) $this->settings->get('wallet_deposit_limit', 50000);

        $pendingBalance = $user->walletTransactions()
            ->where('type', 'credit')
            ->where('status', 'pending')
            ->sum('amount');

        $gateways = $this->payments->getAvailableDepositGateways();

        return view('paymentgateway::frontend.wallet.index', compact('user', 'transactions', 'depositLimit', 'pendingBalance', 'gateways'));
    }

    public function store(Request $request): RedirectResponse
    {
        $limit = (float) $this->settings->get('wallet_deposit_limit', 50000);
        $gateways = $this->payments->getAvailableDepositGateways();
        $gatewayKeys = implode(',', array_keys($gateways));

        $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:'.$limit],
            'payment_method' => ['required', 'string', 'in:'.$gatewayKeys],
        ]);

        $user = Auth::user();
        $amount = (float) $request->input('amount');
        $paymentMethod = $request->input('payment_method');

        // Check if wallet is active
        if (! $user->wallet_status) {
            return back()->with('error', __('paymentgateway::payment.wallet_inactive'));
        }

        try {
            $order = $this->orders->createDepositOrder($user, $amount, $paymentMethod);

            return $this->payments->pay($order, $paymentMethod);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Wallet deposit failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return back()->with('error', __('paymentgateway::payment.deposit_failed'));
        }
    }
}
