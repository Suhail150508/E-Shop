# 9. Virtual Try-On

This section explains how Virtual Try-On works, how the admin enables it, and how the customer uses it. It is **image-based** (no AI required).

---

## How It Works

- **Concept:** The customer uploads a photo (e.g. face). The server **overlays the product image** (e.g. sunglasses) on the photo at a default position and size, then returns a merged image. The customer can **adjust position** (up/down/left/right) and **width** (scale) in the browser, then download the result.
- **Technology:** The server uses **PHP GD** or **Intervention Image** to resize the product image and composite it onto the user photo. No external AI or face-detection API is required. All processing can be done on your server.
- **Privacy:** Photos are sent to your server for processing. They can be stored temporarily (e.g. in `public/uploads/tryon/`) and cleaned up by a scheduled task or after a short time. No third-party service receives the photo unless you add one.

---

## Server Requirements

- **PHP GD** extension, or **Intervention Image** (which may use GD or Imagick). Required for image resize and merge.
- Sufficient **memory/limits** for image size (e.g. 8MB upload, 5120KB max in validation). Large photos may need higher `memory_limit` in PHP.

---

## Admin Setup

**Where:** **Admin → Product Manage → Products** → Create or **Edit** a product.

**What to do:** Find the **“Virtual Try-On”** or **“Enable Try-On”** option (e.g. a checkbox or toggle). **Enable it** for products that are suitable for try-on (e.g. sunglasses, glasses). Save the product.

**What happens:** On the **frontend product page**, a **“Virtual Try-On”** (or “Try On”) button appears. If the option is disabled, the button is hidden. So the admin controls which products offer try-on.

---

## Customer Use (Step by Step)

1. **Open product page** for an item that has Try-On enabled.
2. Click **“Virtual Try-On”**. A **modal** opens.
3. **Upload a photo:** Click or drag to upload (JPEG/PNG). A clear, front-facing face photo works best for glasses/sunglasses.
4. Click **“Try”** or **“Preview”**. The app sends the photo and product ID to the server. The server merges the product image onto the photo and returns a composite image and overlay data (position, size).
5. **Adjust:** Use **position arrows** (up, down, left, right) to move the overlay. Use **Decrease width** / **Increase width** to make the overlay smaller or larger. The preview updates in real time.
6. **Download:** Click **Download** to save the final image. **Try Again** clears the result so the customer can upload a different photo.

**What happens:** The overlay (product image) is sized initially to about 42% of the photo width so it fits a “face” region. The customer can scale it down to about 15% or up to 200% of that size (exact limits are in the frontend JS). Position and scale are applied only in the browser for the preview and download; the server does one initial merge and returns overlay dimensions so the client can redraw with adjustments.

---

## Files and Routes (for developers)

- **Route:** `POST /virtual-try` (expects `image` file and `product_id`).
- **Controller:** `App\Http\Controllers\TryOnController` → `try()` method. It validates the upload and product, loads the product image, calls a merge method (GD or Intervention), returns JSON with composite image URL and overlay position/size.
- **Frontend:** Modal and logic in `resources/views/frontend/partials/tryon-modal.blade.php` and `public/frontend/js/tryon.js`. Adjust overlay with arrows and width buttons; download uses canvas or returned image URL.

---

## Summary Table

| Who     | Action                    | What happens                                  |
|---------|---------------------------|-----------------------------------------------|
| Admin   | Enable Try-On on product  | “Virtual Try-On” button appears on product page |
| Customer| Upload photo + Try        | Server merges product onto photo; result shown |
| Customer| Adjust position/width     | Overlay moves/scales in modal                 |
| Customer| Download                  | Final image saved to device                   |
