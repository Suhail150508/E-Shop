# 3. Product Management

This section covers Categories, Subcategories, Brands, Units, Colors, Products, and Product Reviews.

---

## Categories

**Where:** **Admin → Product Manage → Categories** (`/admin/categories`).

**What you can do:**

- **List:** View all categories (name, image, status, order).
- **Create:** Add a new category (name, slug, image, parent “None” for top-level, order, active/inactive).
- **Edit:** Change name, slug, image, parent, order, status.
- **Delete:** Remove a category (consider subcategories and products linked to it).

**How the admin manages it:** Create parent categories first (parent = None). Then create subcategories by choosing the parent. Use order to control display sequence on the frontend.

**What happens:** Frontend Shop and Home use these categories. Category page shows products of that category (and optionally subcategories). Changing order/status affects how and where they appear.

---

## Subcategories

**Where:** **Admin → Product Manage → Sub Categories** (`/admin/subcategories`).

**What you can do:** Create, edit, delete subcategories; each has a **parent category**.

**What happens:** Products can be assigned to a subcategory (or category). Shop filters and category pages reflect this structure.

---

## Brands

**Where:** **Admin → Product Manage → Brands** (`/admin/brands`).

**What you can do:** Add, edit, delete brands (name, logo/image if available, status). Bulk delete may be available.

**What happens:** Products can be linked to a brand. Frontend can show brand name/logo on product and in filters.

---

## Units

**Where:** **Admin → Product Manage → Units** (`/admin/units`).

**What you can do:** Add, edit, delete units (e.g. Piece, Kg, L, Dozen).

**What happens:** When creating/editing a product, you choose a unit. It is shown on the product page (e.g. “Price per Kg”).

---

## Colors

**Where:** **Admin → Product Manage → Colors** (`/admin/colors`).

**What you can do:** Add, edit, delete colors (name, optional hex/code).

**What happens:** Products can have color options. Customers may select color when adding to cart; admin assigns colors to the product in the product form.

---

## Products

**Where:** **Admin → Product Manage → Products** (`/admin/products`).

**What you can do:**

- **List:** View products; filter/search if the UI provides it.
- **Create:** Set name, slug, description, category/subcategory, brand, unit, price, discount price, stock, status, **Virtual Try-On** (enable/disable), SEO meta title/description, multiple images, color/size if applicable.
- **Edit:** Same fields as create.
- **Delete:** Remove product (consider orders and cart).

**How the admin manages it:** Create categories, brands, units, and colors first. Then add products and assign them. For items like sunglasses, enable **Virtual Try-On** so the “Try On” button appears on the product page.

**What happens:**

- Product appears on Shop and Category pages and in search.
- If Virtual Try-On is enabled, the customer sees a “Virtual Try-On” (or similar) button and can open the try-on modal, upload a photo, and get a merged result.
- Price, discount, and stock are used in cart and checkout; stock is decreased when an order is placed.

---

## Product Reviews

**Where:** **Admin → Reviews** (`/admin/reviews`).

**What you can do:** List customer reviews (product, customer, rating, comment, date). **Approve** or **Reject** (and optionally delete).

**What happens:** Only approved reviews are shown on the product page. Rejected or pending reviews are not displayed. This is how the admin controls what customers see.

---

## Summary Table

| Task                 | Where           | What happens                          |
|----------------------|-----------------|----------------------------------------|
| Add category         | Categories      | Category appears on shop/home         |
| Add subcategory     | Sub Categories  | Products can use it                   |
| Add brand/unit/color| Brands/Units/Colors | Available in product form          |
| Add product         | Products        | Product appears in shop; can be sold  |
| Enable Try-On       | Product edit    | Try-On button on product page         |
| Approve review      | Reviews         | Review shown on product page          |
