Product Module
==============

Overview
--------
The Product module provides full product management features including images, gallery, filtering, and admin CRUD.

Features
--------
- Product create/edit/delete with gallery images
- Search, filtering (category, brand, color), and pagination
- Admin forms and validations
- Migrations included

Requirements
------------
- PHP 8.x
- Laravel 9/10

Installation
------------
1. Copy `Modules/Product` to your project's `Modules/` folder.
2. From project root run:

```powershell
composer dump-autoload;
php artisan migrate;
php artisan storage:link;
```

3. Build frontend assets if you integrate with Echo/Pusher or additional JS libs.

Customizing validation
----------------------
Colors and tags are validated as strings by default. If you want a fixed set of colors, implement a config or database-backed validation and update the form request accordingly.

Demo data
---------
A sample seeder is available at `Modules/Product/Database/Seeders/ProductDemoSeeder.php`.

Notes
-----
- Product slugs are generated and ensured unique by `ProductService`.
- Use `storage:link` to ensure product images are accessible via the `public` disk.
