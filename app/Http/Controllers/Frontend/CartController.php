<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Services\CartService;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\PaymentGateway\App\Services\PaymentManager;
use Modules\Product\App\Models\Product;

class CartController extends Controller
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

    public function index(Request $request): View
    {
        $rawItems = $this->cart->items();
        $items = $rawItems->map(function ($item) {
            $unitPrice = (isset($item['discount_price']) && $item['discount_price'] !== null && $item['discount_price'] > 0 && $item['discount_price'] < $item['price'])
                ? (float) $item['discount_price']
                : (float) $item['price'];
            $item['unit_price'] = $unitPrice;
            $item['line_total'] = $unitPrice * (int) $item['quantity'];

            return $item;
        });
        $subtotal = $this->cart->subtotal();
        $addresses = collect();

        if ($request->user()) {
            $addresses = Address::where('user_id', $request->user()->id)
                ->orderByDesc('is_default')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $gateways = $this->payments->getEnabledGateways();
        $codEnabled = isset($gateways['cod']);
        $stripeEnabled = isset($gateways['stripe']);
        $walletEnabled = isset($gateways['wallet']);

        $defaultGateway = null;
        if ($walletEnabled && $request->user()?->wallet_balance >= $subtotal) {
            $defaultGateway = 'wallet';
        } elseif ($codEnabled) {
            $defaultGateway = 'cod';
        } elseif ($stripeEnabled) {
            $defaultGateway = 'stripe';
        }

        $cartBreadcrumb = $this->settings->get('cart_breadcrumb', __('common.cart_title'));
        $cartTitle = $this->settings->get('cart_title', __('common.shopping_cart'));
        $cartSubtitle = $this->settings->get('cart_subtitle', __('common.cart_subtitle_review'));

        return view('frontend.cart.index', compact(
            'items',
            'subtotal',
            'addresses',
            'codEnabled',
            'stripeEnabled',
            'walletEnabled',
            'defaultGateway',
            'cartBreadcrumb',
            'cartTitle',
            'cartSubtitle'
        ));
    }

    public function store(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        // Validate incoming request data
        $validated = $request->validate([
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:999'],
            'color' => ['nullable', 'string', 'max:100'],
            'size' => ['nullable', 'string', 'max:100'],
        ]);

        // Sanitize and clamp quantity
        $quantity = (int) ($validated['quantity'] ?? $request->input('quantity', 1));
        $quantity = max(1, min(999, $quantity));
        $options = [];
        if (! empty($validated['color'] ?? $request->input('color'))) {
            $options['color'] = $validated['color'] ?? $request->input('color');
        }
        if (! empty($validated['size'] ?? $request->input('size'))) {
            $options['size'] = $validated['size'] ?? $request->input('size');
        }

        // Add item to cart
        $this->cart->add($product, $quantity, $options);

        $message = __(':name has been added to your cart!', ['name' => $product->name]);

        // Return JSON or redirect response
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cartCount' => $this->cart->count(),
            ]);
        }

        return back()->with('success', $message);
    }

    public function update(Request $request, string $id): JsonResponse|RedirectResponse
    {
        // Validate quantity
        $validated = $request->validate([
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:999'],
        ]);
        $quantity = max(1, min(999, (int) ($validated['quantity'] ?? $request->input('quantity', 1))));

        // Update cart item quantity
        $this->cart->update($id, $quantity);

        $message = __('Cart updated.');

        // Return JSON or redirect response
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cartCount' => $this->cart->count(),
                'subtotal' => $this->cart->subtotal(),
            ]);
        }

        return back()->with('success', $message);
    }

    public function destroy(string $id): JsonResponse|RedirectResponse
    {
        // Remove item from cart
        $this->cart->remove($id);

        $message = __('Item removed from cart.');

        // Return JSON or redirect response
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cartCount' => $this->cart->count(),
                'subtotal' => $this->cart->subtotal(),
            ]);
        }

        return back()->with('success', $message);
    }

    public function clear(): JsonResponse|RedirectResponse
    {
        // Clear entire cart
        $this->cart->clear();

        $message = __('Cart cleared.');

        // Return JSON or redirect response
        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cartCount' => 0,
                'subtotal' => 0,
            ]);
        }

        return back()->with('success', $message);
    }

    public function checkout(Request $request): RedirectResponse
    {
        // Determine selected payment gateway
        $gateway = (string) $request->input('payment_method', 'cod');

        $user = $request->user();

        $address = null;

        // Load user address
        if ($user) {
            $addressId = $request->input('address_id');

            if ($addressId) {
                $address = Address::where('user_id', $user->id)
                    ->where('id', $addressId)
                    ->first();
            }

            if (! $address) {
                $address = Address::where('user_id', $user->id)
                    ->where('is_default', true)
                    ->first();
            }
        }

        // Build address lines for billing/shipping
        $addressLines = [];

        if ($address) {
            $addressLines[] = $address->line1;

            if ($address->line2) {
                $addressLines[] = $address->line2;
            }

            $cityLineParts = [];

            if ($address->city) {
                $cityLineParts[] = $address->city;
            }

            if ($address->state) {
                $cityLineParts[] = $address->state;
            }

            if ($address->postal_code) {
                $cityLineParts[] = $address->postal_code;
            }

            if (! empty($cityLineParts)) {
                $addressLines[] = implode(', ', $cityLineParts);
            }

            if ($address->country) {
                $addressLines[] = $address->country;
            }
        }

        $addressString = empty($addressLines) ? null : implode("\n", $addressLines);

        // Prepare customer data for payment
        $customerData = [
            'name' => $user?->name,
            'email' => $user?->email,
            'phone' => $address?->phone,
            'billing_address' => $addressString,
            'shipping_address' => $addressString,
        ];

        // Initiate checkout via payment manager
        return $this->payments->checkout($gateway, $user, $customerData);
    }
}
