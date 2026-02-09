# 7. Frontend – Customer Journey

This section describes what the customer sees and does: browsing, product page, Virtual Try-On, cart, checkout, and account.

---

## Browsing (Home, Shop, Category, Product)

**Home:** The customer lands on the home page. They see hero section, categories, featured/latest products (and possibly flash sale), banners, testimonials—all configurable from **Website Setup** and **Product Management**.

**Shop:** From the menu or a “Shop” link, the customer goes to **Shop** (`/shop`). They see a product list, often with a category sidebar, filters (price, availability), and sort options. Clicking a product opens the **Product** page.

**Category:** Clicking a category (or subcategory) shows **Category** page (`/category/{slug}`) with products in that category.

**Product page:** Shows product name, images (gallery), price, discount, stock, description, add to cart, wishlist. If the product has **Virtual Try-On** enabled, a **“Virtual Try-On”** (or “Try On”) button is shown.

**What happens:** All of this is read-only for the customer: they browse content and catalog that the admin has set up. No admin action is required during browsing except to keep products and categories up to date.

---

## Virtual Try-On (on Product Page)

**Where:** Product detail page, when the product has Try-On enabled.

**Customer steps:**

1. Click **“Virtual Try-On”** (or similar). A modal opens.
2. **Upload a photo** (face clearly visible works best for sunglasses/glasses).
3. Click **Try** or **Preview**. The app sends the photo and product image to the server; the server merges them (image-based; no AI) and returns a result image.
4. **Adjust:** Use position arrows and **Decrease/Increase width** to fit the overlay (e.g. sunglasses) on the face.
5. **Download** the final image if desired. **Try Again** to use another photo.

**What happens:** The server (PHP GD or Intervention Image) overlays the product image on the user photo and returns the composite. No third-party AI API is used. Data is processed on your server; no external service stores the photo. Admin enables this per product in **Product Manage → Products → Edit → Virtual Try-On**.

*Full details: [09 Virtual Try-On](09-Virtual-Try-On.md).*

---

## Cart

**Where:** Cart icon in header or **Cart** link → `/cart`.

**Customer can:** See items, change quantity, remove items, see subtotal. Click **Checkout** (or similar) to proceed. Guest users can add to cart; checkout usually requires login.

**What happens:** Cart is stored in session (and may sync to database for logged-in users). Quantities are validated against stock at checkout. When the customer goes to checkout, they choose shipping address and payment method.

---

## Checkout

**Where:** After clicking Checkout: **Shipping** step → **Payment** step → **Confirmation**.

**Steps:**

1. **Shipping:** Select or add a shipping address. Continue.
2. **Payment:** Choose a payment method (Stripe, PayPal, COD, Bank, Wallet, etc.—only enabled methods appear). Optionally **apply a coupon**. Place order.
3. **Confirmation:** Order is created; success page shows order number and next steps. Customer may receive an email (if email is configured).

**What happens:** Order is saved with status “Pending” or “Processing.” Stock is reduced. If payment is online (Stripe, PayPal, etc.), the customer is redirected to the gateway; on success, the app receives a callback and updates payment status. Admin sees the new order in **Admin → Orders**.

---

## Customer Account

**Where:** After login, **Account** (or “My Account”) in the menu → `/account` or similar.

**Sections (typical):**

- **Dashboard / Overview:** Short summary of orders, profile.
- **Profile:** Edit name, email, password.
- **Addresses:** Add, edit, delete shipping addresses; set default.
- **Orders:** List of orders; click to see detail and **invoice**; option to **request refund**.
- **Wishlist:** Saved products; add to cart from here.
- **Refund requests:** Status of submitted refunds.
- **Support tickets:** List of tickets; open to see conversation and reply.

**What happens:** All data is stored in the database. When the customer updates profile or address, the new data is used for the next order. Refund request creates a record for admin to approve/reject in **Admin → Refund Requests**. Support ticket replies are visible in **Admin → Support Tickets**.

---

## Summary Table

| Customer action   | What happens                                  | Admin side                          |
|------------------|-----------------------------------------------|-------------------------------------|
| Browse shop      | Sees products/categories you configured       | Keep products/categories updated    |
| Add to cart      | Session/db cart updated                        | —                                   |
| Apply coupon     | Discount applied if valid                      | Create coupons in Admin → Coupons   |
| Place order      | Order created, stock reduced, email sent       | See order in Admin → Orders         |
| Request refund   | Refund request created                         | Approve/reject in Refund Requests   |
| Open support ticket | Ticket created, department chosen          | Reply in Support Tickets            |
| Use Live Chat    | Message sent                                   | Reply in Live Chat                  |
