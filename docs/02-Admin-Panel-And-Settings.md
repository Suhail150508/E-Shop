# 2. Admin Panel & Settings

This section explains the Admin Dashboard, General Settings, Email Configuration, Payment Methods, and Website Setup.

---

## Admin Dashboard

**Where:** After login as admin, you are at **Admin → Dashboard** (`/admin`).

**What you see:** Summary cards and information such as recent orders, sales, or other KPIs (depending on the version). The sidebar gives access to all admin sections.

**How the admin uses it:** As the main landing page after login; use the sidebar to open Product Manage, Order Manage, Customers, Support, Payment Settings, System Settings, etc.

---

## General Settings

**Where:** **Admin → System Settings → General Settings** (`/admin/settings`).

**What you can do:**

- **App name:** Site title (used in header, emails, etc.).
- **Logo / Favicon:** Upload or set paths for logo and favicon.
- **Timezone, locale, maintenance mode**, and other global options if present in the form.

**How to do it:** Open the page, edit the fields, click Save/Update.

**What happens:** Values are stored in the `settings` table and cached. The frontend and emails use these values (e.g. logo in header, app name in browser tab).

---

## Email Configuration

**Where:** **Admin → System Settings → Email Configuration** (`/admin/email-configuration`).

**What you can do:**

- Set **mail driver** (SMTP, etc.), host, port, username, password, encryption.
- Edit **email templates** (e.g. order confirmation, contact reply) if the menu has a “Templates” or similar link.

**How to do it:** Fill in SMTP (or other) details, save. Edit template content in the template editor and save.

**What happens:** The app uses these settings to send emails (e.g. order placed, password reset, contact reply). Template changes apply to the next email of that type.

---

## Payment Methods

**Where:** **Admin → Payment Settings → Payment Methods** (`/admin/payment-methods`).

**What you can do:**

- **Enable/disable** each gateway: Stripe, PayPal, Razorpay, Paystack, Bank Transfer, Cash on Delivery (COD), Wallet.
- For each enabled gateway, set **API keys / credentials** (e.g. Stripe secret key, PayPal client ID/secret) and, where applicable, **webhook URL** or **sandbox/live** mode.

**How to do it:** Turn on the toggle for a method, fill in the required fields (as shown on the page), save.

**What happens:** At checkout, only enabled methods are shown. When the customer pays, the corresponding gateway is used. Wrong keys will cause payment failures; webhooks (for Stripe, PayPal, etc.) update order payment status when the gateway notifies the app.

*Full gateway details and wallet are in [10 Payment & Wallet](10-Payment-And-Wallet.md).*

---

## Website Setup

**Where:** **Admin → Website Setup** (`/admin/website-setup`).

**What you can do (by tab/section):**

- **Home / Hero:** Hero title, subtitle, gallery images, banners, testimonial names, review count, etc.
- **Product sections:** Titles/badges for Featured, Flash Sale, Latest (or similar) sections on the home page.
- **Shop / Category:** Shop page title, breadcrumb, header text.
- **Auth pages (Login, Register, Forgot Password, Reset Password):** Page title, subtitle, and side image for each. *These are edited only here, not under Manage Pages.*
- **Cart / Checkout:** Cart and checkout section titles/labels.
- **Other:** Any other labels or images exposed in the form.

**How to do it:** Open the tab, edit text or upload images, click Update/Save.

**What happens:** Values are stored in the `settings` table. The frontend (home, shop, auth pages, cart, checkout) reads these and displays your content. Auth pages (login/register/forgot/reset) use these titles and images; they do not use the “Manage Pages” content for that.

---

## Summary Table

| Task              | Where                         | What happens after save        |
|-------------------|--------------------------------|--------------------------------|
| Change site name  | General Settings               | Header, emails, browser title  |
| Change logo       | General Settings               | Header and any logo place      |
| Set up SMTP       | Email Configuration            | Emails sent via your server    |
| Enable Stripe     | Payment Methods                | Stripe appears at checkout     |
| Edit hero title   | Website Setup (Home)           | Home page hero text updates    |
| Edit login title  | Website Setup (Auth)           | Login page title/image update  |
