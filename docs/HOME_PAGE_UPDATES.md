# Home Page – Updates & CodeCanyon Checklist

This document lists how the home page works, what was updated, and what to keep in mind for CodeCanyon review.

---

## How the home page is updated from admin

- **Website Setup** (Admin → Appearance → Website Setup) controls:
  - Hero: title, subtitle, hero gallery images (3×3 grid).
  - Categories: badge, title, subtitle.
  - Flash sale: badge, title, subtitle.
  - Promo: badge, title, subtitle, image, button texts/links.
  - Banner/Testimonials: badge, title, text, image, button text/link.
  - Featured: badge, title, subtitle.
- **Products on home** come from:
  - **Featured**: products with “Featured” enabled (up to 4).
  - **Flash sale**: products with “Flash Sale” enabled (up to 16).
  - **Latest**: fallback, latest active products (up to 16).
- **Caching**: Home product data is cached for 1 hour (`home_products`). When admin saves Website Setup, this cache is cleared so hero/section text and product sections update without waiting for cache expiry.

---

## Design updates applied

1. **Product cards (home)**
   - Image size reduced (e.g. 500×450 → 400×360) and aspect ratio tightened (padding-top 115% → 108%) for a smaller, cleaner card.
   - Card: smaller padding, 12px radius, lighter shadow, subtle hover.
   - Title: 2-line clamp, slightly smaller font; category and price font sizes reduced.
   - Add to cart button: smaller, 8px radius.
   - Badge and action buttons: smaller and tighter spacing.

2. **Product card partial (global)**
   - Semantic markup: card is `<article>` with `itemscope` / `itemtype="https://schema.org/Product"` and `itemprop="name"` / `itemprop="url"` for SEO.
   - Image: `width`/`height` attributes, improved `alt`, translation-safe badge labels (e.g. `__('common.sale_badge')`).
   - Category name and product name output escaped with `e()` where needed.

3. **Home sections**
   - Sections (Categories, Flash Sale, Featured) have `aria-labelledby` and matching `id` on the section heading for accessibility.

4. **Cache**
   - Clearing `home_products` cache on Website Setup save so admin changes (hero, section titles, etc.) appear on the home page right away.

---

## Important points for CodeCanyon

- **No hardcoded user-facing text**: Home uses `setting()` with `__()` fallbacks and product card uses `__('common.xxx')` for badges/labels.
- **Escaping**: User/content output (e.g. category name, product name in alt) uses Blade `{{ }}` or `e()`.
- **Accessibility**: Hero has `role="banner"` and `aria-labelledby`; product sections have `aria-labelledby`; images have meaningful `alt`.
- **Performance**: Home products cached; image `loading="lazy"` and reasonable image dimensions (400×360) for product thumbnails.
- **Valid structure**: Product card is a single `<article>` per product with schema.org attributes; sections use proper headings (h1/h2) and landmarks.

---

## Optional future improvements

- Add “View All” link for Flash Sale if more than 4 products.
- Make testimonial (e.g. “John D.”, “Sarah J.”) content editable from admin.
- Consider clearing `home_products` cache when a product is set to featured/flash sale so home updates without waiting for Website Setup save.
