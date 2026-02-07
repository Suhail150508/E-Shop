# CodeCanyon Review Report – Single-Vendor eCommerce (Laravel)

This document summarizes the review performed against CodeCanyon standards and the fixes applied.

---

## 1. Code Quality & Structure

### Findings
- **Debug / console:** Removed `console.log` from admin layout (notifications). Removed `console.error` from toaster partial and brands index catch block. `tryon.js` already guards `console.error` with `typeof console !== 'undefined'`.
- **Structure:** Controllers follow Laravel conventions; Try-On logic is in `TryOnController` with private helpers. Refund logic in `RefundController` uses DB transaction.
- **Naming:** Consistent (PascalCase controllers, camelCase JS).

### Applied Fixes
- Admin layout: removed two `console.log` calls.
- Toaster: removed `console.error('Toastr not loaded')`.
- Brands index: removed `console.error` in catch; kept user-facing toastr/alert.

---

## 2. Localization & Static Text

### Findings
- **TryOnController:** All user-facing messages now use `__('common.xxx')` keys.
- **Blade:** Virtual Try-On modal and product cards use `{{ __('common.xxx') }}`.
- **Toaster:** Titles use `__('common.success')`, `__('common.error')`, etc.; messages passed via `json_encode()` for safe JS output.

### Applied Fixes
- Added to `resources/lang/en/common.php`:
  - `tryon_product_not_available`
  - `tryon_product_image_not_found`
  - `tryon_processing_unavailable`
  - `tryon_preview_success`
  - `tryon_preview_failed`
  - `validation_error`
- TryOnController: all 5 response messages switched to `__('common.tryon_xxx')`.
- Toaster partial: flash messages and validation errors output with `json_encode()`; toast titles use `__('common.success')`, `__('common.error')`, `__('common.info')`, `__('common.warning')`, `__('common.validation_error')`.

### Recommendation
- Run a project-wide search for hardcoded user-visible strings in Blade/JS and move them to lang files where missing.

---

## 3. Error Handling & Data Safety

### Try-On
- Validation: `image` (required, image, mimes:jpeg,jpg,png, max:5120), `product_id` (required, exists).
- `findOrFail` for product; null checks for `$productImagePath` and `file_exists`; fallback to GD if Intervention missing; GD checks extension and `getimagesize`; `cleanOldFiles` uses `glob() ?: []` to avoid null iteration.

### Refund
- Refund row is locked with `lockForUpdate()` before approval to prevent double wallet credit on duplicate requests.
- Wallet credit + `WalletTransaction` creation and order `payment_status` update run inside the same DB transaction.

### General
- Controllers use try/catch where appropriate; TryOnController logs and returns JSON with generic message on exception.

---

## 4. Security

### Virtual Try-On
- **Input:** Server-side validation: `image` (required|image|mimes:jpeg,jpg,png|max:5120), `product_id` (required|exists:products,id).
- **CSRF:** Route is in `web.php` (web middleware group), so CSRF is applied to POST `/virtual-try`.
- **File upload:** Type and size restricted; product image path resolved via `Storage::disk('public')` or `public_path()` only (no user-controlled path).
- **XSS:** API returns JSON; frontend uses response data without injecting raw HTML.

### Toaster
- Session flash and validation messages are output with `json_encode()` in the toaster partial, avoiding XSS when messages contain quotes or backslashes.

### Admin
- Admin routes are under auth middleware; RefundController uses validated `status` and `admin_note`.

### Recommendation
- Ensure all admin routes are behind auth and role middleware where required.
- Keep file upload validation (type, size, and optionally image dimensions) on every upload endpoint.

---

## 5. Database & Refund / Wallet Logic

### Refunds Table
- `refunds` has `order_id`, `user_id`, `reason`, `details`, `amount`, `status`, `admin_note`, `images` (json), timestamps; FKs with cascade.

### Refund Flow (Wallet)
- On status change to `approved` (and previous status was not `approved`):
  - Refund row is locked with `lockForUpdate()` to prevent double processing.
  - If order was paid with `wallet` and order has a user:
    - `user->increment('wallet_balance', $refund->amount)`.
    - `WalletTransaction::create` with `type => 'refund'`, description, `payment_method => 'wallet'`, reference to order, `status => 'approved'`.
  - Order `payment_status` set to refunded.
- Refund status and `admin_note` updated in the same transaction.

### Applied Fix
- `RefundController::update`: added `Refund::where('id', $refund->id)->lockForUpdate()->firstOrFail()` at the start of the transaction so the same refund cannot be applied twice (e.g. double wallet credit) on concurrent or duplicate requests.

### Wallet Transactions
- `wallet_transactions` has type enum including `'refund'`; refunds are recorded with type `refund` for audit.

---

## 6. Virtual Try-On Section (Frontend)

- **Modal:** Rendered from `frontend/partials/tryon-modal.blade.php`; styles in `public/frontend/css/tryon-modal.css` (loaded in layout head so they apply everywhere).
- **Script:** `tryon.js` binds to `.try-on-btn`, uses `data-id` (product id) and optional `data-image`; upload, preview, adjust, download flow with state; no hardcoded user strings (messages from modal data attributes or server).
- **Product card:** Try-On only as icon in product-actions when `$product->is_tryable`; no bottom button; hover style uses `var(--rust)` for consistency.
- **i18n:** All modal and button text use `__('common.xxx')` in Blade; JS uses data attributes from the modal for messages.

---

## 7. Customer Panel Review (Step-by-Step)

### 7.1 Code Quality & Structure
- **Controllers:** `Customer\AddressController`, `ProfileController`, `DashboardController`, `OrderController`, `RefundController`, `WishlistController`; `Frontend\SupportTicketController` for tickets. No commented junk or debug statements.
- **Laravel conventions:** MVC; `WishlistService` used for wishlist; validation in Form Request / `$request->validate()`.
- **Naming:** Consistent; flash messages moved to `__('common.xxx')` keys.

### 7.2 Localization & Static Text
- **Lang keys added** in `common.php`: `address_added_success`, `address_updated_success`, `address_removed_success`, `default_address_updated`, `profile_updated_success`, `current_password_incorrect`, `password_updated_success`, `refund_only_delivered`, `refund_already_submitted`, `invalid_refund_reason`, `refund_request_submitted`, `support_ticket_created_success`, `support_ticket_reply_success`, `na`.
- **Controllers:** All customer-facing success/error messages use `__('common.xxx')`.
- **Blade:** Dashboard, profile, password, addresses, orders, wishlist, support tickets use `{{ __(...) }}` for labels and messages; payment method fallback uses `__('common.na')`.

### 7.3 Error Handling & Data Safety
- **Dashboard:** `$recentOrders ?? []`, `$totalOrders ?? 0`, etc.; `$order->created_at?->format()` in tables.
- **Orders show:** `$order->created_at?->format()`, `$order->updated_at?->format()`, `$order->payment_method ?: __('common.na')`; contact fallbacks `$order->customer_name ?? $order->user?->name`.
- **Sidebar:** `Str::limit(Auth::user()->name ?? '', 1)` for avatar initial; `Auth::user()->name ?? __('common.guest')`, `Auth::user()->email ?? ''`.
- **Support ticket show:** `$ticket->department?->name`, `$ticket->created_at?->format()`, `$message->created_at?->format()`.
- **Address index:** `$address->country ?? __('N/A')`; empty line2/city/state handled with `array_filter` and `trim(...) ?: __('N/A')`.

### 7.4 Security
- **Routes:** All customer panel routes under `Route::middleware(['auth', 'role:customer'])` in `web.php`.
- **Ownership:**  
  - Address: `edit`/`update`/`destroy`/`setDefault` check `$address->user_id === $request->user()->id`; abort 403 otherwise.  
  - Order: `show` and `invoice` check `$order->user_id === $request->user()->id`.  
  - Refund: `store` checks `$order->user_id === Auth::id()`, order status `delivered`, and no existing refund.  
  - Support ticket: `show` and `reply` use `SupportTicket::where('user_id', Auth::id())->firstOrFail()`.
- **CSRF:** All forms use `@csrf`; routes in `web` group.
- **Input validation:**  
  - Address: type, label, line1, line2, city, state, postal_code, country, phone, email, road, house, floor, lat/lng, is_default.  
  - Profile: name, email (unique ignore current user).  
  - Password: current_password, password (confirmed, Password::defaults()).  
  - Refund: reason (in allowed list + Other), details (nullable, max 2000), images (array max 5, each image mimes jpeg,png,jpg,gif max 2048).  
  - Support ticket: subject, department_id (exists), priority (in list), message (max 10000), attachment (optional, mimes, max 2048); reply: message (max 10000), attachment same.
- **File uploads:** Refund images and support ticket attachment validated (type, size); stored under `refunds/` and `support-attachments/` on public disk.
- **XSS:** Blade uses `{{ }}` for output; support ticket message shown with `{!! nl2br(e($message->message)) !!}` (escaped).

### 7.5 Database & Refund Flow (Customer Side)
- **Data access:** Orders, addresses, wishlist, support tickets scoped by `user_id` (or `Auth::user()->supportTickets()`).
- **Refund request (customer):** Customer can submit one refund per order (delivered only); reason validated against `RefundReason` (status true) + "Other"; amount set server-side to `$order->total`; images optional, validated; refund created with `STATUS_PENDING`. Actual wallet credit and transaction record happen in **Admin** RefundController on approval (with lock to prevent double credit).

---

## Summary of Files Touched

| File | Change |
|------|--------|
| `resources/lang/en/common.php` | Added tryon_* and validation_error keys |
| `app/Http/Controllers/TryOnController.php` | All messages use `__('common.xxx')` |
| `app/Http/Controllers/Admin/RefundController.php` | Lock refund row with `lockForUpdate()` before approval |
| `resources/views/layouts/admin.blade.php` | Removed console.log (notifications) |
| `resources/views/layouts/partials/toaster.blade.php` | Removed console.error; safe JS with json_encode + lang keys |
| `resources/views/admin/brands/index.blade.php` | Removed console.error in catch |
| **Customer panel** | |
| `resources/lang/en/common.php` | Added customer flash + `na`, support reply success |
| `app/Http/Controllers/Customer/AddressController.php` | Flash messages → `__('common.xxx')` |
| `app/Http/Controllers/Customer/ProfileController.php` | Flash/error → `__('common.xxx')` |
| `app/Http/Controllers/Customer/RefundController.php` | Flash/error → `__('common.xxx')` |
| `app/Http/Controllers/Frontend/SupportTicketController.php` | Flash → `__('common.xxx')`; message max 10000; store validation array form |
| `resources/views/frontend/account/partials/sidebar.blade.php` | Null-safe name/email/avatar initial |
| `resources/views/frontend/orders/show.blade.php` | Payment method → `__('common.na')`; `created_at?->`, `updated_at?->` |
| `resources/views/frontend/account/support_ticket/show.blade.php` | `department?->name`, `created_at?->format()` |

---

## Checklist for Submission

- [x] No debug logs/console in production code (or guarded)
- [x] User-facing strings via lang keys (Try-On + toaster titles)
- [x] Try-On: server-side validation, CSRF, safe file handling
- [x] Refund: lock to prevent double wallet credit; transaction history recorded
- [x] Toaster: XSS-safe output with json_encode
- [x] Customer panel: ownership checks on address, order, refund, support ticket
- [x] Customer panel: all flash messages via `__('common.xxx')`; views null-safe where needed
- [x] Customer panel: file upload validation (refund images, support attachment) and message max length
- [ ] Optional: full project scan for remaining hardcoded strings
- [ ] Optional: add rate limiting on `/virtual-try` if required by reviewer
