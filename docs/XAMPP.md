# XAMPP setup (MySQL)

## 1. Start XAMPP

1. Open **XAMPP Control Panel**.
2. Click **Start** next to **MySQL** (Apache is optional if you use `php artisan serve`).
3. Confirm MySQL is running (green status).

## 2. Create the database (phpMyAdmin)

1. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Click **Databases** → create database name: `laravel_ecommerce`.
3. Collation: `utf8mb4_unicode_ci` → **Create**.

Or run in a terminal (XAMPP MySQL must be running):

```bat
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS laravel_ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## 3. Project `.env` (already set for XAMPP)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_ecommerce
DB_USERNAME=root
DB_PASSWORD=
```

If your MySQL `root` user has a password, set `DB_PASSWORD=` to that value.

## 4. Install tables and demo data

From the project folder:

```bat
cd C:\xampp\htdocs\laravel-ecommerce
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

Or double-click / run: `scripts\xampp-setup.bat`

## 5. Run the shop

```bat
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000).

**Demo logins:** `admin@example.com` / `password` · `customer@example.com` / `password`

## 6. View or edit data in phpMyAdmin

- Database: `laravel_ecommerce`
- Tables: `products`, `users`, `orders`, `categories`, etc.

You can add/edit/delete rows in phpMyAdmin. For day-to-day catalog and orders, prefer the **admin panel** after logging in as admin.

## Optional: Apache instead of `artisan serve`

Point your virtual host **document root** to:

`C:\xampp\htdocs\laravel-ecommerce\public`

(not the project root folder).
