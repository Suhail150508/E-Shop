# 8. Multi-Currency & Multi-Language

This section covers Currencies, Languages, RTL support, and content translation.

---

## Multi-Currency

**Where:** **Admin → International → Multi Currency** (`/admin/currency`).

**What you can do:**

- **Add currency:** Code (e.g. USD, EUR, BDT), name, symbol, exchange rate (relative to default), status (active/inactive).
- **Set default:** One currency is the store default (prices in DB are usually in default currency).
- **Edit / Delete:** Change rate or symbol; remove if not used.

**What happens:** On the frontend, the customer can **switch currency** (e.g. from a dropdown or footer). Product prices and cart totals are converted using the exchange rate. Admin must keep rates updated if you want accurate conversions. Order may store the selected currency and converted amount (implementation may vary).

---

## Multi-Language

**Where:** **Admin → International → Multi Language** (`/admin/language`).

**What you can do:**

- **Add language:** Code (e.g. `en`, `ar`, `bn`), name, set as default or not. Optionally enable **RTL** for that language.
- **Set default:** The default language is used when the user has not chosen a language.
- **Edit / Delete:** Change name or RTL; remove if not used.

**What happens:** Frontend can show a **language switcher**. When the user selects a language, the app locale changes. Static strings use Laravel lang files (`resources/lang/{code}/`). **Page content** (Manage Pages) can have translations: when editing a page, you switch language tabs and enter title/content per language. So the same About Us page can show English or Arabic content depending on the selected language.

---

## RTL (Right-to-Left)

**Where:** When adding or editing a language, there is an **RTL** option (e.g. for Arabic).

**What happens:** If RTL is enabled for that language, the layout uses `dir="rtl"` and the app loads RTL-specific CSS (e.g. `app-rtl.css`). The header, footer, and content align for right-to-left reading. This is automatic when the user selects an RTL language.

---

## Content Translation (Pages)

**Where:** **Admin → Manage Pages** → open a page (e.g. About Us, Terms) → you see **language tabs** (e.g. English, Arabic, Bengali).

**What you do:** For the **default language**, edit title and content in the main form. For **other languages**, switch to that language’s tab and fill in the translated title and content (and for About Us, the section fields like hero title, story blocks, etc.).

**What happens:** When a customer visits the site in Arabic, the frontend loads the Arabic translation for that page. If a translation is missing, the app may fall back to the default language. So the admin is responsible for adding and maintaining translations per page and per language.

---

## Summary Table

| Task              | Where              | What happens                          |
|-------------------|--------------------|----------------------------------------|
| Add currency      | Multi Currency     | Option in frontend switcher; prices converted |
| Set default currency | Multi Currency   | Base for exchange rates                |
| Add language      | Multi Language     | Option in language switcher            |
| Enable RTL        | Language edit      | RTL layout for that language           |
| Translate page    | Manage Pages → tab | Localized content on frontend          |
