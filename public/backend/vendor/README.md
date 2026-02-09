# Admin Vendor Assets (Offline / CodeCanyon)

These assets are loaded locally instead of CDN for reliability and CodeCanyon compliance.

## Required files

Place the following files so admin panel works without external CDN:

### 1. Bootstrap 5.3.x
- **CSS:** `bootstrap/css/bootstrap.min.css`  
  Download: https://github.com/twbs/bootstrap/releases (e.g. v5.3.3 → dist/css/bootstrap.min.css)
- **JS:** `bootstrap/js/bootstrap.bundle.min.js`  
  Same release → dist/js/bootstrap.bundle.min.js

### 2. Font Awesome 6.4.x
- **CSS:** `fontawesome/css/all.min.css`  
  Download: https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css  
  Save to `fontawesome/css/all.min.css`
- **Webfonts:** Ensure `../webfonts/` path in CSS points to font files.  
  Either place Font Awesome webfonts in `fontawesome/webfonts/` or adjust CSS to point to `../../webfonts/` (project already has webfonts in `backend/webfonts/`).

### 3. Bootstrap Icons 1.11.x
- **CSS:** `bootstrap-icons/bootstrap-icons.min.css` (already in this folder if created)
- **Font:** `bootstrap-icons/fonts/bootstrap-icons.woff2`  
  Download from: https://github.com/twbs/icons/releases

Existing local assets used by admin: `backend/js/jquery.min.js`, `backend/js/apexcharts.min.js`, `global/toastr/*`.
