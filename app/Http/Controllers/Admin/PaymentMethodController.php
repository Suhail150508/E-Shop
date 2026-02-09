<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentMethodController extends Controller
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function index(Request $request)
    {
        $gateways = [
            'cod' => 'Cash On Delivery',
            'wallet' => 'Wallet',
            'stripe' => 'Stripe',
            'paypal' => 'Paypal',
            'paystack' => 'Paystack',
            'razorpay' => 'Razorpay',
            'bank' => 'Bank Payment',
        ];

        $currentGateway = $request->query('gateway', 'cod');

        if (! array_key_exists($currentGateway, $gateways)) {
            abort(404);
        }

        $settings = $this->settingService->all();
        $currencies = Currency::where('status', true)->get();

        return view('admin.payment-methods.index', compact('gateways', 'currentGateway', 'settings', 'currencies'));
    }

    public function update(Request $request, $gateway)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $key = 'payment_'.$gateway.'_image';
            $oldImage = $this->settingService->get($key);
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }

            $path = $request->file('image')->store('uploads/payment_gateways', 'public');
            $this->settingService->set($key, $path);
        }

        // Common Settings
        $this->settingService->set('payment_'.$gateway.'_enabled', $request->has('status'));
        $this->settingService->set('payment_'.$gateway.'_currency', $request->input('currency', 'USD'));

        // Gateway Specific Settings
        switch ($gateway) {
            case 'stripe':
                $this->settingService->set('stripe_mode', $request->input('mode'));
                $this->settingService->set('stripe_test_key', $request->input('test_key'));
                $this->settingService->set('stripe_test_secret', $request->input('test_secret'));
                $this->settingService->set('stripe_live_key', $request->input('live_key'));
                $this->settingService->set('stripe_live_secret', $request->input('live_secret'));
                // Map to existing keys used by StripePaymentService
                $this->settingService->set('stripe_test_publishable_key', $request->input('test_key'));
                $this->settingService->set('stripe_test_secret_key', $request->input('test_secret'));
                $this->settingService->set('stripe_live_publishable_key', $request->input('live_key'));
                $this->settingService->set('stripe_live_secret_key', $request->input('live_secret'));
                break;

            case 'paypal':
                $mode = $request->input('mode');
                $this->settingService->set('paypal_mode', $mode);

                if ($mode === 'live') {
                    $this->settingService->set('paypal_live_client_id', $request->input('client_id'));
                    $this->settingService->set('paypal_live_secret', $request->input('client_secret'));
                } else {
                    $this->settingService->set('paypal_sandbox_client_id', $request->input('client_id'));
                    $this->settingService->set('paypal_sandbox_secret', $request->input('client_secret'));
                }
                break;

            case 'razorpay':
                $this->settingService->set('razorpay_key', $request->input('key'));
                $this->settingService->set('razorpay_secret', $request->input('secret'));
                break;

            case 'paystack':
                $this->settingService->set('paystack_public_key', $request->input('public_key'));
                $this->settingService->set('paystack_secret_key', $request->input('secret_key'));
                $this->settingService->set('paystack_merchant_email', $request->input('merchant_email'));
                break;

            case 'bank':
                $this->settingService->set('bank_details', $request->input('bank_details'));
                break;
        }

        return redirect()->back()->with('success', __('common.payment_method_updated_success'));
    }
}
