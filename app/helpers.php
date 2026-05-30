<?php

use Illuminate\Support\Facades\Log;

if (!defined('SITE_NAME')) {
    define('SITE_NAME', config('app.name', 'TAPI Store'));
}

if (!defined('SITE_URL')) {
    define('SITE_URL', rtrim(config('app.url', 'http://127.0.0.1:8000'), '/'));
}

if (!function_exists('syncLegacySessionGlobals')) {
    function syncLegacySessionGlobals(): void
    {
        foreach (['user_id', 'user_name', 'user_email', 'user_role', 'last_order_id'] as $key) {
            if (session()->has($key)) {
                $_SESSION[$key] = session($key);
            } else {
                unset($_SESSION[$key]);
            }
        }
    }
}

if (!function_exists('getDB')) {
    function getDB(): PDO
    {
        static $pdo = null;

        if ($pdo instanceof PDO) {
            return $pdo;
        }

        $connection = config('database.connections.mysql');
        $host = $connection['host'] ?? '127.0.0.1';
        $port = $connection['port'] ?? 3306;
        $database = $connection['database'] ?? 'tapi_ecommerce';
        $charset = $connection['charset'] ?? 'utf8mb4';
        $username = $connection['username'] ?? 'root';
        $password = $connection['password'] ?? '';

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return $pdo;
    }
}

if (!function_exists('generateCSRFToken')) {
    function generateCSRFToken(): string
    {
        return csrf_token();
    }
}

if (!function_exists('verifyCSRFToken')) {
    function verifyCSRFToken(string $token): bool
    {
        return hash_equals(csrf_token(), $token);
    }
}

if (!function_exists('setFlash')) {
    function setFlash(string $type, string $message): void
    {
        session()->flash('flash', ['type' => $type, 'message' => $message]);
    }
}

if (!function_exists('getFlash')) {
    function getFlash(): ?array
    {
        return session()->pull('flash');
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9-]/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);

        return trim($text, '-') ?: 'item-' . time();
    }
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn(): bool
    {
        return session()->has('user_id');
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return session('user_role') === 'admin';
    }
}

if (!function_exists('getCurrentUser')) {
    function getCurrentUser(): ?array
    {
        if (!isLoggedIn()) {
            return null;
        }

        return [
            'id' => session('user_id'),
            'full_name' => session('user_name', ''),
            'email' => session('user_email', ''),
            'role' => session('user_role', 'customer'),
        ];
    }
}

if (!function_exists('loginUser')) {
    function loginUser(array $user): void
    {
        session()->regenerate();
        session([
            'user_id' => $user['id'],
            'user_name' => $user['full_name'],
            'user_email' => $user['email'],
            'user_role' => $user['role'],
        ]);
        syncLegacySessionGlobals();
    }
}

if (!function_exists('logoutUser')) {
    function logoutUser(): void
    {
        session()->forget(['user_id', 'user_name', 'user_email', 'user_role', 'last_order_id']);
        syncLegacySessionGlobals();
    }
}

if (!function_exists('getCartCount')) {
    function getCartCount(): int
    {
        if (!isLoggedIn()) {
            return 0;
        }

        try {
            $stmt = getDB()->prepare('SELECT SUM(quantity) as total FROM cart WHERE user_id = ?');
            $stmt->execute([session('user_id')]);
            $result = $stmt->fetch();

            return (int)($result['total'] ?? 0);
        } catch (Throwable $e) {
            Log::warning('Cart count failed: ' . $e->getMessage());
            return 0;
        }
    }
}

if (!function_exists('productImage')) {
    function productImage(?string $image): string
    {
        if ($image && file_exists(public_path('uploads/products/' . $image))) {
            return SITE_URL . '/uploads/products/' . rawurlencode($image);
        }

        return SITE_URL . '/assets/images/no-image.png';
    }
}

if (!function_exists('categoryImage')) {
    function categoryImage(?string $image): string
    {
        if ($image && file_exists(public_path('assets/images/categories/' . $image))) {
            return SITE_URL . '/assets/images/categories/' . rawurlencode($image);
        }

        return SITE_URL . '/assets/images/no-image.png';
    }
}

if (!function_exists('formatPrice')) {
    function formatPrice(float|int|string|null $price): string
    {
        return '$' . number_format((float)$price, 2);
    }
}

if (!function_exists('renderStars')) {
    function renderStars(float|int|string|null $rating): string
    {
        $rating = (float)$rating;
        $html = '<div class="stars">';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= floor($rating)) {
                $html .= '<span class="star filled">&#9733;</span>';
            } elseif ($i - 0.5 <= $rating) {
                $html .= '<span class="star half">&#9733;</span>';
            } else {
                $html .= '<span class="star">&#9734;</span>';
            }
        }
        $html .= '</div>';

        return $html;
    }
}