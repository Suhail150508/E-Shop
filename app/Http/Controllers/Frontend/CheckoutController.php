<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Coupon;
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
        $freeShippingMin = (float) $this->settings->get('free_shipping_min_amount', 0);
        
        $insideCityNameRaw = $this->settings->get('shipping_inside_city_name', 'Dhaka');
        $insideCityNames = array_filter(array_map(fn($item) => strtolower(trim($item)), explode(',', $insideCityNameRaw)));
        
        $insideCityCost = (float) $this->settings->get('shipping_inside_city_cost', 60);
        $outsideCityCost = (float) $this->settings->get('shipping_outside_city_cost', 120);
        $currencySymbol = $this->settings->get('app_currency', '$');

        return view('frontend.checkout.shipping', compact('addresses', 'subtotal', 'checkoutState', 'shippingRate', 'taxPercent', 'freeShippingMin', 'insideCityNames', 'insideCityCost', 'outsideCityCost', 'currencySymbol'));
    }

    public function storeShipping(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'delivery_type' => 'required|in:home_delivery,pickup',
            'address_id' => 'required_if:delivery_type,home_delivery|exists:addresses,id',
            'order_note' => 'nullable|string|max:500',
            'shipping_latitude' => 'nullable|numeric|between:-90,90',
            'shipping_longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Ensure address belongs to current user (security)
        if ($validated['delivery_type'] === 'home_delivery' && ! empty($validated['address_id'])) {
            $address = Address::where('id', $validated['address_id'])->where('user_id', $user->id)->first();
            if (! $address) {
                return redirect()->route('checkout.shipping')->withErrors(['address_id' => __('common.address_invalid')]);
            }
        }

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
        $taxPercent = (float) $this->settings->get('tax_percent', 0);
        $tax = $taxPercent > 0 ? round($subtotal * $taxPercent / 100, 2) : 0;

        $freeShippingMin = (float) $this->settings->get('free_shipping_min_amount', 0);

        $address = null;
        if (!empty($checkoutState['address_id'])) {
            $address = Address::find($checkoutState['address_id']);
        }

        $shippingCost = $this->calculateShippingCost($subtotal, $address, $checkoutState['delivery_type'] ?? 'home_delivery');

        $discount = $this->cart->discount();
        $coupon = $this->cart->getCoupon();

        $total = max(0, $subtotal + $tax + $shippingCost - $discount);

        $walletBalance = (float) ($request->user()->wallet_balance ?? 0);

        $cartItems = $this->cart->items();

        return view('frontend.checkout.payment', compact('subtotal', 'gateways', 'checkoutState', 'tax', 'shippingCost', 'total', 'walletBalance', 'taxPercent', 'cartItems', 'discount', 'coupon', 'freeShippingMin'));
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
        ]);

        $coupon = Coupon::where('code', $validated['code'])->first();

        if (! $coupon) {
            return response()->json(['success' => false, 'message' => __('common.coupon_invalid')]);
        }

        if (! $coupon->is_active) {
            return response()->json(['success' => false, 'message' => __('common.coupon_inactive')]);
        }

        if ($coupon->expiry_date && $coupon->expiry_date->isPast()) {
            return response()->json(['success' => false, 'message' => __('common.coupon_expired')]);
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json(['success' => false, 'message' => __('common.coupon_limit_reached')]);
        }

        $subtotal = $this->cart->subtotal();

        if ($coupon->min_spend && $subtotal < $coupon->min_spend) {
            return response()->json(['success' => false, 'message' => __('common.coupon_min_spend', ['amount' => number_format($coupon->min_spend, 2)])]);
        }

        $this->cart->applyCoupon($coupon);

        return response()->json(['success' => true, 'message' => __('common.coupon_applied')]);
    }

    public function removeCoupon()
    {
        $this->cart->removeCoupon();

        return response()->json(['success' => true, 'message' => __('common.coupon_removed')]);
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

        if (($checkoutState['delivery_type'] ?? '') === 'home_delivery' && ! empty($checkoutState['address_id'] ?? null)) {
            $address = Address::where('id', $checkoutState['address_id'])
                ->where('user_id', $user->id)
                ->first();
            if (! $address) {
                return redirect()->route('checkout.shipping')->withErrors(['address_id' => __('common.address_invalid')]);
            }
        }

        // Construct address string
        $addressLines = [];
        if ($address) {
            $addressLines[] = $address->line1 ?? '';
            if (! empty($address->line2)) {
                $addressLines[] = $address->line2;
            }
            $cityLine = array_filter([$address->city, $address->state, $address->postal_code]);
            if ($cityLine) {
                $addressLines[] = implode(', ', $cityLine);
            }
            if (! empty($address->country)) {
                $addressLines[] = $address->country;
            }
        }
        $addressString = $addressLines ? implode("\n", $addressLines) : '';

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
            'shipping_cost' => $this->calculateShippingCost($this->cart->subtotal(), $address, $checkoutState['delivery_type'] ?? 'home_delivery'),
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

    private function calculateShippingCost(float $subtotal, ?Address $address, string $deliveryType): float
    {
        if ($deliveryType !== 'home_delivery') {
            return 0.0;
        }

        $freeShippingMin = (float) $this->settings->get('free_shipping_min_amount', 0);
        if ($freeShippingMin > 0 && $subtotal >= $freeShippingMin) {
            return 0.0;
        }

        $insideCityNameRaw = $this->settings->get('shipping_inside_city_name', 'Dhaka');
        $insideCityNames = array_filter(array_map(fn($item) => strtolower(trim($item)), explode(',', $insideCityNameRaw)));
        
        $insideCityCost = (float) $this->settings->get('shipping_inside_city_cost', 60);
        $outsideCityCost = (float) $this->settings->get('shipping_outside_city_cost', 120);

        $isInsideCity = false;
        
        if ($address) {
            $city = strtolower($address->city ?? '');
            $state = strtolower($address->state ?? '');
            $line1 = strtolower($address->line1 ?? '');
            $line2 = strtolower($address->line2 ?? '');
            
            foreach ($insideCityNames as $name) {
                if (empty($name)) continue;
                
                if (
                    ($city && str_contains($city, $name)) || 
                    ($state && str_contains($state, $name)) ||
                    ($line1 && str_contains($line1, $name)) ||
                    ($line2 && str_contains($line2, $name))
                ) {
                    $isInsideCity = true;
                    break;
                }
            }
        }

        if ($isInsideCity) {
            return $insideCityCost;
        }

        return $outsideCityCost;
    }
}
