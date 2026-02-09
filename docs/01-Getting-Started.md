# 1. Getting Started

This section covers project overview, server requirements, installation steps, environment configuration, and first login.

---

## Overview

**Single Vendor eCommerce** is a Laravel-based online store where one admin runs the entire shop. Key features:

- **Frontend:** Home, Shop, Category, Product pages; Cart; Checkout; Customer account (orders, addresses, wishlist, refunds, support tickets).
- **Virtual Try-On:** Image-based try-on (e.g. sunglasses) without AI; customer uploads a photo, gets a merged result, can adjust position/size and download.
- **Admin panel:** Dashboard, product management (categories, brands, products), orders, refunds, coupons, customers, staff, contact messages, newsletter, pages, menus, support tickets, live chat, payment methods, website setup, multi-currency, multi-language.
- **Payments:** Stripe, PayPal, Razorpay, Paystack, Bank Transfer, Cash on Delivery (COD), Wallet.
- **Multi-currency & RTL:** Multiple currencies and languages; RTL layout for languages like Arabic.

**What you get after purchase:** Source code (Laravel app + modules), database migrations, seeders for demo data, and this documentation.

---

## Server Requirements & Prerequisites

- **PHP:** 8.2 or higher (8.3 supported)
- **Extensions:** BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD or Imagick (for image handling and Virtual Try-On)
- **Composer:** 2.x
- **Node.js & NPM:** For frontend assets (Vite)
- **Database:** MySQL 5.7+ / MariaDB 10.3+ or PostgreSQL or SQLite
- **Web server:** Apache or Nginx (with PHP-FPM)

---

## Installation Guide

### Step 1: Get the files

- Download the item from CodeCanyon (ZIP).
- Extract to your web server directory (e.g. `htdocs/your-store` or your server’s public folder).

### Step 2: Install PHP dependencies

```bash
cd /path/to/your-store
composer install
```

*What happens:* Laravel and all PHP packages (including those for payments, PDF invoice, etc.) are installed.

### Step 3: Environment file

```bash
cp .env.example .env
php artisan key:generate
```

- Edit `.env` and set at least:
  - `APP_NAME`, `APP_URL`
  - `DB_CONNECTION`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (for MySQL/MariaDB)
  - `MAIL_*` if you want real emails (optional for first run)

*What happens:* The app gets a secret key and uses your database and URL.

### Step 4: Database

```bash
php artisan migrate
php artisan db:seed
```

- **migrate:** Creates all tables (users, products, orders, settings, etc.).
- **db:seed:** Inserts demo data (admin user, customer, categories, products, settings, pages, currencies, languages, refund reasons, support departments, etc.).

*What happens:* You get a working store with demo content. After seeding, clear config/cache if you change env: `php artisan config:clear` and `php artisan cache:clear`.

### Step 5: Frontend assets

```bash
npm install
npm run build
```

*What happens:* CSS and JS (Bootstrap, Vite) are built. For development you can use `npm run dev` instead of `npm run build`.

### Step 6: Storage link

```bash
php artisan storage:link
```

*What happens:* The `public/storage` link points to `storage/app/public`, so uploaded images (products, logo, etc.) are visible.

### Step 7: Run the application

- **Local:** `php artisan serve` then open `http://127.0.0.1:8000`
- **Server:** Point your web server document root to the `public` folder and ensure URL matches `APP_URL`.

---

## Demo Credentials & First Login

After seeding, you can log in with:

| Role      | Email               | Password  |
|-----------|---------------------|-----------|
| Admin     | admin@example.com   | password  |
| Customer  | customer@example.com| password  |
| Staff     | (see UserSeeder)   | password  |

- **Admin:** Log in → you are redirected to **Admin Dashboard** (`/admin`). From here you manage everything (settings, products, orders, etc.).
- **Customer:** Log in → you are redirected to the **storefront**. You can shop, add to cart, checkout, and use the **Account** area.
- **Staff:** Log in → you are redirected to the **Staff** panel (`/staff`). Staff can view and update order status; they do not get full admin access.

**Important:** Change all demo passwords and remove or update demo data before going live.

---

## What to Do Next

1. **Admin → General Settings:** Set your app name, logo, favicon, timezone.
2. **Admin → Website Setup:** Configure home page content and auth page titles/images if needed.
3. **Admin → Payment Methods:** Enable and configure the payment gateways you use.
4. **Admin → Manage Pages:** Edit About Us, Contact, Terms, Privacy, Shipping (and optionally Coupons) content.
5. **Admin → Product Manage:** Add or edit categories, brands, and products; enable **Virtual Try-On** for suitable products.

For detailed steps on each area, use the **Documentation sections** in [docs/README.md](README.md).
