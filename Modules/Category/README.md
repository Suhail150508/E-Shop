Category Module
=================

Overview
--------
The Category module provides hierarchical category and subcategory management for the store. It includes admin CRUD, bulk delete, cached category tree helpers and migrations.

Features
--------
- Create / edit / delete categories and subcategories
- Bulk delete support
- Cached category tree and featured categories service
- Admin views (publishable)
- Migrations included

Requirements
------------
- PHP 8.x
- Laravel 9/10 (confirm compatibility with your project)
- Database (MySQL, MariaDB, SQLite supported by Laravel)

Installation
------------
1. Copy the `Modules/Category` folder into your project's `Modules/` directory.
2. From your project root run:

```powershell
composer dump-autoload;
php artisan migrate;
php artisan storage:link;
```

3. (Optional) Publish config or views if you want to override the module views:

```powershell
php artisan vendor:publish --tag=category
```

Configuration
-------------
There are no module-specific env variables; the module uses the application database and storage disk. If you need to customize behavior, edit `config/category.php` after publishing the config.

Demo data
---------
A demo seeder is provided at `Modules/Category/Database/Seeders/CategoryDemoSeeder.php`. Run it with:

```powershell
php artisan db:seed --class="Modules\\Category\\Database\\Seeders\\CategoryDemoSeeder"
```

Support & Notes
---------------
- Ensure your app's `view.paths` are configured if you plan to publish views.
- Slug generation is handled by the CategoryService which ensures unique slugs. If you have a custom requirement for slugs, extend the service accordingly.
