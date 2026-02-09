# 10. Payment Gateways & Wallet

This section describes how to enable and configure each payment method and how the Wallet works.

---

## Where to Configure

**Admin → Payment Settings → Payment Methods** (`/admin/payment-methods`).

Each gateway has an **Enable/Disable** toggle and a **Configure** or **Settings** area where you enter API keys and options.

---

## Cash on Delivery (COD)

- **What you do:** Enable COD. Optionally add a label or instructions (if the form has a field). No API keys.
- **What happens:** At checkout, the customer can choose “Cash on Delivery.” Order is created with status Pending/Processing. You fulfill the order and collect cash on delivery. No automatic payment confirmation.

---

## Bank Transfer

- **What you do:** Enable Bank Transfer. Optionally enter bank name, account number, instructions (if the form provides fields). These may be shown on the order confirmation or a “Bank details” page.
- **What happens:** Customer selects Bank Transfer at checkout. Order is created. You provide your bank details (via the app or manually). Customer transfers money; you confirm manually and update order/payment status if needed.

---

## Stripe

- **What you do:** Enable Stripe. Enter **Secret Key** and **Publishable Key** from Stripe Dashboard. Set **Sandbox/Live** mode. Optionally set **Webhook URL** in Stripe Dashboard to your app’s webhook route (e.g. `https://yourdomain.com/stripe/webhook` or as shown in the app).
- **What happens:** At checkout, the customer is redirected to Stripe (or sees Stripe Elements). After payment, Stripe redirects back and may send a webhook. The app updates the order payment status. Use sandbox keys for testing.

---

## PayPal

- **What you do:** Enable PayPal. Enter **Client ID** and **Secret** from PayPal Developer. Use sandbox credentials for testing. Optionally configure webhook in PayPal Dashboard to your app’s PayPal webhook URL.
- **What happens:** Customer chooses PayPal, is redirected to PayPal to log in and approve. After payment, PayPal redirects back and may send a webhook. The app marks the order as paid.

---

## Razorpay

- **What you do:** Enable Razorpay. Enter **Key ID** and **Key Secret** from Razorpay Dashboard. Set webhook URL in Razorpay to your app’s Razorpay webhook route if required.
- **What happens:** Checkout may redirect to Razorpay or use Razorpay’s script. On success, the app receives callback/webhook and updates the order.

---

## Paystack

- **What you do:** Enable Paystack. Enter **Public Key** and **Secret Key** from Paystack Dashboard. Configure webhook URL in Paystack to your app’s Paystack webhook route if required.
- **What happens:** Customer is redirected to Paystack (or pays via inline). On success, webhook/callback updates the order.

---

## Wallet

- **Where:** **Admin → Wallet Management** (if present) may have settings (e.g. enable/disable wallet, minimum balance). Customer wallet balance may be shown in **Admin → Customers** or a dedicated wallet list.
- **What you do:** Enable Wallet in Payment Methods. Optionally configure wallet-specific settings (e.g. top-up limits, refund to wallet).
- **What happens:** Customers may have a **wallet balance** (e.g. from refunds or top-up if the feature exists). At checkout, they can choose **Pay with Wallet**. The order total is deducted from the wallet; if balance is insufficient, they must use another method or add funds. Admin can see transactions or balance in Wallet Management.

---

## Summary Table

| Gateway   | Admin action              | Customer experience                    |
|-----------|---------------------------|----------------------------------------|
| COD       | Enable                    | Select COD → order placed; pay on delivery |
| Bank      | Enable, add details       | Select Bank → see details → transfer  |
| Stripe    | Enable, keys, webhook     | Redirect to Stripe → pay → return      |
| PayPal    | Enable, Client ID/Secret  | Redirect to PayPal → approve → return |
| Razorpay  | Enable, keys, webhook     | Pay via Razorpay flow                 |
| Paystack  | Enable, keys, webhook     | Pay via Paystack flow                  |
| Wallet    | Enable, wallet settings   | Select Wallet if balance sufficient   |

---

## Security Notes (for CodeCanyon / production)

- **Never commit** `.env` or real API keys to version control. Use `.env` for all keys.
- Use **HTTPS** in production so payment redirects and webhooks are secure.
- Use **webhook signing** (Stripe, PayPal, etc.) where supported so the app only trusts genuine gateway callbacks.
- Keep gateway SDKs and the app updated for security fixes.
