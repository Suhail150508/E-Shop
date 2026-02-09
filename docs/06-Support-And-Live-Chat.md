# 6. Support & Live Chat

This section covers Support Tickets, Support Departments, and the Live Chat module.

---

## Support Departments

**Where:** **Admin → Support Ticket → Departments** (`/admin/support-departments`).

**What you can do:** Create, edit, delete **departments** (e.g. “Sales,” “Technical Support,” “Returns”). Each has a name and optionally description/email.

**What happens:** When a customer opens a new support ticket on the frontend, they **choose a department**. So you organize tickets by department. The list of departments is what the customer sees in the dropdown.

---

## Support Tickets

**Where:** **Admin → Support Ticket → All Tickets** (`/admin/support-tickets`).

**What you can do:**

- **List:** View all tickets (customer, subject, department, status, date).
- **Open ticket:** See the conversation (customer messages and your replies).
- **Reply:** Type a message and send. The customer may get an email notification (if configured).
- **Update status:** e.g. Open → In Progress → Closed.

**How the admin manages it:** Check new tickets, assign or handle by department, reply with answers or instructions. When the issue is resolved, set status to Closed.

**What happens:** Customer creates a ticket from **Account → Support Tickets** (or similar). Each message is stored. Your reply is visible when they open the ticket and may be emailed to them. Status helps you and the customer track progress.

---

## Live Chat

**Where:** **Admin → Live Chat** (sidebar; route may be under a “Live Chat” or “Communication” section). The Live Chat module provides an admin view of conversations.

**What you can do:** See **conversations** with customers in real time. Reply to messages. The frontend shows a **chat widget** (e.g. bottom-right) so visitors can start a chat.

**How it works:**

- **Frontend:** Customer or visitor opens the chat widget, types a message, and sends it.
- **Admin:** You see the conversation in the admin Live Chat page and reply.
- Messages are delivered in real time (often via WebSockets or polling). No AI; it is human-to-human chat.

**What happens:** Improves support by allowing instant back-and-forth. Chat history is stored so you can refer to it later. Ensure your server supports the broadcast/driver used (e.g. Pusher or Laravel Reverb) if the module uses real-time push.

---

## Summary Table

| Task            | Where           | What happens                          |
|-----------------|-----------------|----------------------------------------|
| Add department  | Support Departments | Option when customer opens ticket   |
| Reply to ticket | All Tickets → open ticket | Customer sees reply, may get email |
| Close ticket    | Ticket detail   | Status updated                         |
| Reply in Live Chat | Live Chat    | Customer sees message in widget        |
