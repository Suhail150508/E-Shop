<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Order;
use App\Services\CartService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Modules\PaymentGateway\App\Services\PaymentManager;

class CheckoutController extends Controller
{
    protected CartService $cart;

    protected PaymentManager $payments;

    protected SettingService $settings;

    public function __construct(CartService $cart, PaymentManager $payments, SettingService $settings)
    {
        $this->cart = $cart;
        $this->payments = $payments;
        $this->settings = $settings;
    }

    public function shipping(Request $request): View|RedirectResponse
    {
        if ($this->cart->items()->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $addresses = Address::where('user_id', $request->user()->id)
            ->orderByDesc('is_default')
            ->get();

        $subtotal = $this->cart->subtotal();

        // Retrieve saved checkout state from session if available
        $checkoutState = Session::get('checkout_state', []);

        // Settings for display
        $shippingRate = (float) $this->settings->get('shipping_flat_rate', 0);
        $taxPercent = (float) $this->settings->get('tax_percent', 0);

        // Google Maps Settings
        $googleMapsApiKey = $this->settings->get('google_maps_api_key') ?: config('services.google.maps_api_key');
        $googleMapsEnabled = (bool) $this->settings->get('google_maps_enabled', false);

        return view('frontend.checkout.shipping', compact('addresses', 'subtotal', 'checkoutState', 'shippingRate', 'taxPercent', 'googleMapsApiKey', 'googleMapsEnabled'));
    }

    public function storeShipping(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'delivery_type' => 'required|in:home_delivery,pickup',
            'address_id' => 'required_if:delivery_type,home_delivery|exists:addresses,id',
            'order_note' => 'nullable|string|max:500',
            'shipping_latitude' => 'nullable|numeric|between:-90,90',
            'shipping_longitude' => 'nullable|numeric|between:-180,180',
        ]);

        Session::put('checkout_state', $validated);

        return redirect()->route('checkout.payment');
    }

    public function payment(Request $request): View|RedirectResponse
    {
        if ($this->cart->items()->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $checkoutState = Session::get('checkout_state');
        if (! $checkoutState) {
            return redirect()->route('checkout.shipping');
        }

        $subtotal = $this->cart->subtotal();
        $gateways = $this->payments->getEnabledGateways();

        // Calculate tax/shipping using settings to match OrderService logic
        $shippingRate = (float) $this->settings->get('shipping_flat_rate', 0);
        $taxPercent = (float) $this->settings->get('tax_percent', 0);

        $tax = $taxPercent > 0 ? round($subtotal * $taxPercent / 100, 2) : 0;
        $shippingCost = ($checkoutState['delivery_type'] === 'home_delivery') ? $shippingRate : 0;

        $discount = $this->cart->discount();
        $coupon = $this->cart->getCoupon();

        $total = max(0, $subtotal + $tax + $shippingCost - $discount);

        $walletBalance = $request->user()->wallet_balance;

        $cartItems = $this->cart->items();

        return view('frontend.checkout.payment', compact('subtotal', 'gateways', 'checkoutState', 'tax', 'shippingCost', 'total', 'walletBalance', 'taxPercent', 'cartItems', 'discount', 'coupon'));
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
        ]);

        $coupon = \App\Models\Coupon::where('code', $validated['code'])->first();

        if (! $coupon) {
            return response()->json(['success' => false, 'message' => __('Invalid coupon code.')]);
        }

        if (! $coupon->is_active) {
            return response()->json(['success' => false, 'message' => __('Coupon is inactive.')]);
        }

        if ($coupon->expiry_date && $coupon->expiry_date->isPast()) {
            return response()->json(['success' => false, 'message' => __('Coupon has expired.')]);
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json(['success' => false, 'message' => __('Coupon usage limit reached.')]);
        }

        $subtotal = $this->cart->subtotal();

        if ($coupon->min_spend && $subtotal < $coupon->min_spend) {
            return response()->json(['success' => false, 'message' => __('Minimum spend of :amount required.', ['amount' => number_format($coupon->min_spend, 2)])]);
        }

        $this->cart->applyCoupon($coupon);

        return response()->json(['success' => true, 'message' => __('Coupon applied successfully.')]);
    }

    public function removeCoupon()
    {
        $this->cart->removeCoupon();

        return response()->json(['success' => true, 'message' => __('Coupon removed.')]);
    }

    public function process(Request $request): RedirectResponse
    {
        $checkoutState = Session::get('checkout_state');
        if (! $checkoutState) {
            return redirect()->route('checkout.shipping');
        }

        $validated = $request->validate([
            'payment_method' => 'required|string',
            'terms' => 'accepted',
        ]);

        $user = $request->user();
        $address = null;

        if ($checkoutState['delivery_type'] === 'home_delivery') {
            $address = Address::find($checkoutState['address_id']);
        }

        // Construct address string
        $addressLines = [];
        if ($address) {
            $addressLines[] = $address->line1;
            if ($address->line2) {
                $addressLines[] = $address->line2;
            }
            $cityLine = array_filter([$address->city, $address->state, $address->postal_code]);
            if ($cityLine) {
                $addressLines[] = implode(', ', $cityLine);
            }
            if ($address->country) {
                $addressLines[] = $address->country;
            }
        }
        $addressString = implode("\n", $addressLines);

        $customerData = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $address?->phone,
            'billing_address' => $addressString,
            'shipping_address' => $addressString,
            'delivery_type' => $checkoutState['delivery_type'],
            'order_note' => $checkoutState['order_note'] ?? null,
            'shipping_latitude' => $checkoutState['shipping_latitude'] ?? null,
            'shipping_longitude' => $checkoutState['shipping_longitude'] ?? null,
        ];

        // Pass control to PaymentManager which handles Order creation
        return $this->payments->checkout($validated['payment_method'], $user, $customerData);
    }

    public function confirmation(Order $order): View
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('frontend.checkout.confirmation', compact('order'));
    }
}
