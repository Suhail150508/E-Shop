# 4. Order Management

This section covers Orders, Refund Requests, Refund Reasons, and Coupons.

---

## Orders

**Where:** **Admin → Order Manage → Orders** (`/admin/orders`).

**What you can do:**

- **List:** View all orders. Use filters (if available) by date range, status, payment status, customer.
- **View detail:** Open an order to see items, quantities, prices, shipping address, payment method, totals, coupon if applied.
- **Update status:** Change order status (e.g. Pending → Processing → Shipped → Delivered). Some statuses may trigger emails or stock restoration (e.g. Cancelled).
- **Invoice:** Generate or download PDF invoice for the order.

**How the admin manages it:** Check new orders from the list or dashboard. Open each order, confirm details, then update status as you process (e.g. ship the product and set “Shipped”). Use invoice for your records or to send to the customer.

**What happens:**

- When the customer places an order, a new order is created with status typically “Pending” or “Processing.” Stock is reduced.
- When you change status to “Cancelled” (or equivalent), stock can be restored. Customer may see status in their account.
- Invoice PDF is generated from order data; download or send via email as per your process.

---

## Refund Reasons

**Where:** **Admin → Order Manage → Refund Reasons** (`/admin/refund-reasons`).

**What you can do:** Add, edit, delete **refund reasons** (e.g. “Damaged product,” “Wrong item,” “Changed mind”). These are the options the customer can choose when requesting a refund.

**What happens:** On the customer’s order detail page, they can request a refund and must select a reason from this list. Admin sees the reason in the refund request.

---

## Refund Requests

**Where:** **Admin → Order Manage → Refund Requests** (`/admin/refund-requests`).

**What you can do:**

- **List:** View all refund requests (order, customer, reason, status).
- **View:** See full request and order details.
- **Approve or Reject:** Change the refund status. Optionally process the actual refund in your payment gateway or by other means (the app may only record approval/rejection).

**What happens:** Customer submits a refund request from their order page. Admin approves or rejects. Customer may see the status in their account. Approval does not automatically refund money in Stripe/PayPal; you may need to do that in the gateway dashboard or manually.

---

## Coupons

**Where:** **Admin → Order Manage → Coupons** (`/admin/coupons`).

**What you can do:** Create, edit, delete coupons.

**Typical fields:**

- **Code:** What the customer enters (e.g. `SAVE10`).
- **Type:** Percentage or fixed amount.
- **Value:** Discount percentage or amount.
- **Minimum order value** (optional).
- **Valid from / Valid to** (date range).
- **Usage limit** (total and/or per customer).

**What happens:** At checkout, the customer can enter the code. If valid (within dates, limit not exceeded, minimum met), the discount is applied to the order total. Admin can see coupon usage if the app tracks it.

---

## Summary Table

| Task              | Where          | What happens                          |
|-------------------|----------------|----------------------------------------|
| Change order status | Order detail | Customer may see update; cancel can restore stock |
| Download invoice | Order detail   | PDF generated for that order           |
| Add refund reason | Refund Reasons | Option appears when customer requests refund |
| Approve refund    | Refund Requests| Request marked approved; you process money separately if needed |
| Create coupon     | Coupons        | Code can be used at checkout if valid  |
