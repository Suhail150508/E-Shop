# Single Vendor eCommerce – Documentation

This documentation helps you install, configure, and manage the **Single Vendor eCommerce Platform with Virtual Try-On**. It explains **where to do what**, **how to do it**, **what happens when you do something**, and **how the admin manages every step**.

---

## Documentation Sections

### 1. [Getting Started](01-Getting-Started.md)
**Overview • Requirements • Installation • First Login**

- Project overview and features  
- Server requirements and prerequisites  
- Step-by-step installation guide  
- Environment configuration (`.env`)  
- Database migration and seeding  
- Demo credentials and first login (Admin, Customer, Staff)  

*Start here after purchasing the project.*

---

### 2. [Admin Panel & Settings](02-Admin-Panel-And-Settings.md)
**Dashboard • General Settings • Email • Payment Methods • Website Setup**

- Admin dashboard overview  
- General Settings (app name, logo, favicon, timezone, maintenance)  
- Email configuration and templates  
- Payment method enable/disable and API keys (Stripe, PayPal, Razorpay, Paystack, Bank, COD, Wallet)  
- Website Setup: Home page content, hero, product sections, auth page titles/images, shop/cart labels  

*How the admin configures the store and appearance.*

---

### 3. [Product Management](03-Product-Management.md)
**Categories • Subcategories • Brands • Units • Colors • Products • Reviews**

- Categories and subcategories (create, edit, order, image)  
- Brands, Units, Colors (for product attributes)  
- Products: create, edit, images, price, stock, SEO, **Virtual Try-On** flag  
- Product reviews: list, approve, reject, delete  

*How the admin manages the catalog and what the customer sees.*

---

### 4. [Order Management](04-Order-Management.md)
**Orders • Refunds • Coupons**

- Order list, filters (date, status, payment, customer)  
- Order detail view, status update, invoice  
- Refund reasons (admin-defined) and refund requests (approve/reject)  
- Coupons: create, discount type, validity, usage limit  

*How orders flow and how the admin handles refunds and promotions.*

---

### 5. [Customers, Staff & Content](05-Customers-Staff-And-Content.md)
**Customers • Staff • Contact Messages • Newsletter • Manage Pages • Menu Builder**

- Customers list and details  
- Staff accounts and roles (staff can manage orders from Staff panel)  
- Contact form messages and reply  
- Newsletter subscribers  
- Manage Pages: About Us, Contact, Terms, Privacy, Shipping, Coupons (edit content per language)  
- Menu Builder: header and footer menus  

*How the admin manages users and static content.*

---

### 6. [Support & Live Chat](06-Support-And-Live-Chat.md)
**Support Tickets • Departments • Live Chat**

- Support departments (create, edit)  
- Support tickets: customer opens ticket, admin replies; status and department  
- Live Chat module: real-time chat between customer and admin  

*How the admin handles customer support and live chat.*

---

### 7. [Frontend – Customer Journey](07-Frontend-Customer-Journey.md)
**Shopping • Cart • Checkout • Account**

- Home, Shop, Category, Product pages (what the customer sees)  
- Virtual Try-On on product page (upload photo, adjust, download)  
- Cart: add, update, remove; Wishlist  
- Checkout: shipping address, payment method, coupon  
- Customer account: profile, addresses, orders, wishlist, refund requests, support tickets  

*What the customer does step by step and what happens at each step.*

---

### 8. [Multi-Currency & Multi-Language](08-Multi-Currency-And-Language.md)
**Currencies • Languages • RTL**

- Add/edit currencies, set default, exchange rate  
- Add languages, set default; RTL support (e.g. Arabic)  
- Page and content translation (Manage Pages, language tabs)  

*How the store supports multiple currencies and languages.*

---

### 9. [Virtual Try-On](09-Virtual-Try-On.md)
**How It Works • Admin Setup • Customer Use**

- How Virtual Try-On works (image-based merge; no AI required)  
- Enabling Try-On for a product (admin: product edit → “Virtual Try-On” option)  
- Customer flow: open modal, upload photo, get result, adjust position/width, download  
- Server requirements (PHP GD or Intervention Image)  

*Complete guide to the Virtual Try-On feature.*

---

### 10. [Payment Gateways & Wallet](10-Payment-And-Wallet.md)
**Stripe • PayPal • Razorpay • Paystack • Bank • COD • Wallet**

- Enabling and configuring each payment method in Admin → Payment Methods  
- API keys, webhooks (where applicable), sandbox vs live  
- Wallet: admin settings, customer balance, pay with wallet at checkout  

*How payments and wallet work for the admin and customer.*

---

## Quick Reference

| I want to…                    | Go to section / Admin menu              |
|------------------------------|-----------------------------------------|
| Install the project          | [01 Getting Started](01-Getting-Started.md) |
| Change site name/logo        | [02 Admin Panel](02-Admin-Panel-And-Settings.md) → General Settings |
| Set up payments              | [02 Admin Panel](02-Admin-Panel-And-Settings.md) → Payment Methods |
| Add products/categories      | [03 Product Management](03-Product-Management.md) |
| Manage orders & refunds      | [04 Order Management](04-Order-Management.md) |
| Edit About/Contact/Terms     | [05 Customers & Content](05-Customers-Staff-And-Content.md) → Manage Pages |
| Reply to support tickets     | [06 Support & Live Chat](06-Support-And-Live-Chat.md) |
| Understand customer flow    | [07 Frontend – Customer Journey](07-Frontend-Customer-Journey.md) |
| Add currency/language        | [08 Multi-Currency & Language](08-Multi-Currency-And-Language.md) |
| Enable Try-On for a product  | [09 Virtual Try-On](09-Virtual-Try-On.md) |
| Configure Stripe/PayPal etc. | [10 Payment & Wallet](10-Payment-And-Wallet.md) |

---

## Document Information

- **Product:** Single Vendor eCommerce Platform with Virtual Try-On System  
- **Documentation version:** 1.0  
- **Audience:** Buyers (customers who purchase the item), developers, and CodeCanyon reviewers  
- All steps and screens described here match the included application. If your version differs (e.g. after an update), refer to the admin menu and on-screen labels.
