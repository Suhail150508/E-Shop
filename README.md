# Single Vendor eCommerce

A modern, clean, and performant Single Vendor eCommerce application built with Laravel 11 and Bootstrap 5. This project is designed for easy customization and CodeCanyon compliance.

## 1. Installation

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    cd Single_Vendor_eCommerce_project
    ```

2.  **Install dependencies:**
    ```bash
    composer install
    npm install && npm run build
    ```

3.  **Environment Setup:**
    - Copy `.env.example` to `.env`.
    - Configure your database credentials in `.env`.

4.  **Database Migration & Seeding:**
    ```bash
    php artisan migrate --seed
    ```
    *This will create demo users, categories, products, and settings.*

5.  **Serve the application:**
    ```bash
    php artisan serve
    ```

---

## 2. Key Credentials (Demo)

-   **Admin:** `admin@example.com` / `password`
-   **Customer:** `customer@example.com` / `password`

If you seeded the demo admin user, log in with the credentials configured in `.env`.

---

## 3. Features by Area

### Authentication and Roles

-   Login and registration for customers
-   **Modern "Shofy-style" Authentication Pages**: Beautiful split-layout design for login and register pages with dynamic breadcrumbs and responsive illustrations.
-   Remember-me login and smart redirect: customers go to the storefront, admins to the dashboard
-   Admin, customer, and staff roles stored in the `users` table
-   Route middleware to protect admin pages

### Settings

-   Key/value settings table
-   Service to read and cache settings
-   Admin UI to edit basic configuration

### Catalog (Categories and Products)

-   Parent/child categories (nullable `parent_id`)
-   Category image and active/inactive status
-   Products with price, discount price, stock, status
-   SEO meta fields (title, description, keywords)
-   Multiple product images with gallery support

### Frontend Shop

-   Homepage hero, category chips, and featured sections powered by products and categories (with static fallbacks when the catalog is empty)
-   Shopify-style two-row header with logo, search, and navigation bar
-   Shop index with category sidebar, basic price and availability filters, and sorting
-   Category wise product listing that supports parent categories and subcategories
-   Product detail page with:
    -   Main image with clickable thumbnail gallery
    -   Discount price display logic
    -   Stock information
    -   SEO meta information block
    -   Buttons for Add to Cart, Wishlist, and AI Style Preview placeholder

### Cart and Wishlist

-   Session based cart that works for guests and logged in users
-   Add, update, remove, and clear cart items
-   Wishlist with add/remove toggle and “move to cart” action
-   Navbar badges showing cart and wishlist item counts
-   Global flash messages for success and error feedback

### Orders and Invoices

-   Order schema with `orders`, `order_items`, and optional status history table
-   Order service to create orders from the cart and manage status changes
-   Stock reduction and restoration logic when orders are placed or cancelled
-   Admin order list with filters by date, status, payment status, and customer
-   Admin order detail view with items, totals, customer, and payment summary
-   Simple customer “My Orders” page with order detail view
-   PDF invoice generation using `barryvdh/laravel-dompdf`
-   Download invoice buttons available in both admin and customer panels

### Payments

-   Pluggable payment architecture with per-gateway services
-   Cash on Delivery (COD) gateway without external API calls
-   Stripe card payments using official `stripe/stripe-php` SDK
-   Admin settings to enable/disable gateways and configure keys
-   Sandbox/live mode toggle for Stripe
-   Secure redirect flow with success, cancel, and webhook endpoints
-   On checkout: pending order is created and then updated on payment result

---

## 4. Architecture

-   Controllers are thin and delegate work to dedicated service classes in `app/Services`.
-   FormRequests handle validation for admin forms.
-   Views use Blade and Bootstrap 5 without custom JavaScript frameworks.
-   DatabaseSeeders create only safe demo content and use environment variables for credentials.

This structure is designed to be easy to extend for future phases (orders, checkout, AI features, etc.).

---

## 5. Testing

Run the PHPUnit test suite to ensure stability:

```bash
php artisan test
```

## 6. CodeCanyon Compliance

-   **No Hardcoded Credentials**: All credentials use `.env`.
-   **Static Fallbacks**: Frontend views handle empty database states gracefully.
-   **Validation**: All forms use server-side validation with `FormRequest` classes.
-   **Clean Code**: PSR-12 coding style followed.
