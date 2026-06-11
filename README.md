# Laravel Commerce

Modern Laravel ecommerce application with customer storefront, persistent cart, checkout, admin dashboard, normalized database schema, seed data, and REST catalog API.

## Stack

- Laravel 12.59.0, PHP 8.2+
- MySQL for production/local setup via `.env.example`
- Blade, Bootstrap 5, custom CSS, JavaScript, Lucide icons
- Eloquent models, migrations, seeders, notifications, middleware, and MVC controllers

Laravel 13 is the current latest major line, but it requires PHP 8.3+. This machine has PHP 8.2.12, so the generated project uses the latest Laravel 12 release that runs locally. After upgrading PHP to 8.3+, change `laravel/framework` in `composer.json` to `^13.0` and run `composer update`.

## Features

- Registration, login, logout, email verification, and password reset
- Product catalog with categories, subcategories, brands, search, filters, sorting, ratings, and SEO metadata
- Product detail pages with image gallery, stock, discounts, reviews, and recommendations
- AJAX cart with guest session persistence and user cart merge on login
- Checkout with billing/shipping addresses, coupons, tax, shipping, order summary, and order notifications
- Payment method support for Cash on Delivery, Stripe-ready settings, and PayPal-ready settings
- User dashboard with order history, profile management, saved addresses, and wishlist
- Admin dashboard with analytics, products, images, inventory, categories, brands, orders, invoices, customers, coupons, banners, and review moderation
- Public REST API for products, featured products, categories, and brands
- Dark/light mode toggle and locale switch scaffold for English

## Installation

1. Install PHP dependencies:

```bash
composer install
```

2. Configure environment:

```bash
copy .env.example .env
php artisan key:generate
```

3. Create a MySQL database named `laravel_ecommerce`, then set credentials in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_ecommerce
DB_USERNAME=root
DB_PASSWORD=
```

4. Run migrations and seed data:

```bash
php artisan migrate --seed
php artisan storage:link
```

5. Start the app:

```bash
php artisan serve
```

Open `http://127.0.0.1:8000`.

### XAMPP (MySQL + phpMyAdmin)

The project `.env` is configured for XAMPP defaults (`root`, no password, database `laravel_ecommerce`). See [docs/XAMPP.md](docs/XAMPP.md) or run `scripts\xampp-setup.bat` after starting MySQL in the XAMPP Control Panel.

## Demo Accounts

- Admin: `admin@example.com` / `password`
- Customer: `customer@example.com` / `password`

Seeded users are email verified so dashboard/admin flows work immediately.

## Payments

Cash on Delivery works out of the box. Stripe and PayPal flows are wired through the checkout/payment records and ready for real credentials:

```env
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

PAYPAL_CLIENT_ID=
PAYPAL_SECRET=
PAYPAL_MODE=sandbox
```

The current provider confirmation route is a local demo handoff. Replace it with Stripe Checkout or PayPal Orders API calls before accepting live online payments.

## API

```text
GET /api/products
GET /api/products/featured
GET /api/products/{slug}
GET /api/categories
GET /api/brands
```

Filters accepted by `/api/products`: `q`, `category`, `brand`, `min_price`, `max_price`, `rating`, and `per_page`.

## Notes

- `.env.example` is configured for MySQL.
- The included local `.env` uses SQLite so the project can be smoke-tested immediately on machines without MySQL running.
- Product uploads use `storage/app/public` and are exposed through `public/storage`.

## admin_user
admin@tapi.com
Admin@123

## costumer_user
jamil@example.com
jamil@123
