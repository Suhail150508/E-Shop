# 5. Customers, Staff & Content

This section covers Customers, Staff, Contact Messages, Newsletter, Manage Pages, and Menu Builder.

---

## Customers

**Where:** **Admin → Customers → Customers List** (`/admin/customers`).

**What you can do:** View list of registered customers (email, name, etc.). Open a customer to see details (profile, orders, addresses if available).

**What happens:** Customers are created when they register on the frontend. Admin does not create customers here; admin only views and manages data. Deleting or blocking a customer (if the option exists) prevents them from logging in or placing orders, depending on implementation.

---

## Staff

**Where:** **Admin → Staff Management** (`/admin/staff`).

**What you can do:** Add, edit, delete **staff** accounts. Staff have a separate role from admin and customer. Assign email and password (or invite).

**What happens:** Staff can log in and access the **Staff panel** (`/staff`). There they can typically view orders and update order status. They do **not** get full admin access (no settings, no product management, etc.). This is useful for warehouse or support staff.

---

## Contact Messages

**Where:** **Admin → Customers → Contact Messages** (`/admin/contact`).

**What you can do:** View messages sent from the frontend **Contact** form (name, email, subject, message). **Reply** (sends email to the customer). **Delete** messages.

**What happens:** When a customer submits the contact form, a record is saved and appears here. When you reply, the app sends an email to the customer’s submitted address. Reply content may be stored for your reference.

---

## Newsletter Subscribers

**Where:** **Admin → Customers → Subscriber List** (`/admin/newsletter`).

**What you can do:** View emails that subscribed via the newsletter form on the frontend. Export or delete if the UI allows.

**What happens:** Customers (or visitors) enter email in the newsletter block; the email is stored. The app does not send newsletters by itself; you use this list in your own email tool or a future newsletter feature.

---

## Manage Pages

**Where:** **Admin → Manage Pages** (sidebar). Under it you see direct links to edit each page: **About Us**, **Contact**, **Terms and Conditions**, **Privacy**, **Shipping**, **Coupons**, etc. (`/admin/pages/{id}/edit`).

**What you can do:** Edit **page content** (title, body content, breadcrumb image, SEO meta). If multiple languages are enabled, switch between **language tabs** and fill title/content (and for About Us, section fields) per language.

**Important:** Login, Register, Forgot Password, and Reset Password pages are **not** edited here. They are configured in **Website Setup → Auth** (titles and images).

**What happens:** Frontend pages (e.g. `/about`, `/terms`, `/contact`) load content from the database. Whatever you save here is shown on the site. Default language uses the main fields; other languages use translations so that when the user switches language, they see the translated content.

---

## Menu Builder

**Where:** **Admin → Menu Builder** or **Menus** (`/admin/menus`).

**What you can do:**

- Create menus (e.g. “Header”, “Footer 1”, “Footer 2”).
- Open **Builder** for a menu and **add items** (label, link URL or route, parent for submenus).
- **Reorder** items (drag or up/down).
- **Edit / Delete** items.

**What happens:** The frontend header and footer use these menus. So “Header” controls the main navigation; footer menus control footer columns. Changing items or order here changes the links on the site immediately.

---

## Summary Table

| Task           | Where          | What happens                          |
|----------------|----------------|----------------------------------------|
| View customers | Customers List | See who registered                    |
| Add staff      | Staff Management | Staff can log in to Staff panel      |
| Reply to contact | Contact Messages | Customer gets email reply           |
| Edit About Us  | Manage Pages → About Us | About page content updates    |
| Edit Terms     | Manage Pages → Terms   | Terms page content updates    |
| Add menu item  | Menus → Builder | Link appears in header/footer        |
