<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function home()
    {
        $db = getDB();
        $pageTitle = 'Home';
        $activePage = 'home';

        $featured = $db->query("
            SELECT p.*, c.name as category_name,
            (SELECT AVG(r.rating) FROM reviews r WHERE r.product_id = p.id) as avg_rating,
            (SELECT COUNT(r.id) FROM reviews r WHERE r.product_id = p.id) as review_count
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.is_featured = 1 AND p.status = 'active'
            ORDER BY p.created_at DESC LIMIT 8
        ")->fetchAll();

        $categories = $db->query('SELECT * FROM categories ORDER BY name')->fetchAll();

        $newArrivals = $db->query("
            SELECT p.*, c.name as category_name,
            (SELECT AVG(r.rating) FROM reviews r WHERE r.product_id = p.id) as avg_rating
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'active'
            ORDER BY p.created_at DESC LIMIT 8
        ")->fetchAll();

        return view('store.home', compact('pageTitle', 'activePage', 'featured', 'categories', 'newArrivals'));
    }

    public function products(Request $request)
    {
        $db = getDB();
        $pageTitle = 'Products';
        $activePage = 'products';
        $perPage = 8;
        $page = max(1, (int)$request->query('page', 1));
        $offset = ($page - 1) * $perPage;
        $where = ["p.status = 'active'"];
        $params = [];

        $categorySlug = $request->query('category', '');
        if ($categorySlug) {
            $where[] = 'c.slug = ?';
            $params[] = $categorySlug;
        }

        $search = trim($request->query('q', ''));
        if ($search) {
            $where[] = '(p.name LIKE ? OR p.description LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $priceMax = $request->has('price_max') ? (float)$request->query('price_max') : 0;
        if ($priceMax > 0) {
            $where[] = 'COALESCE(p.discount_price, p.price) <= ?';
            $params[] = $priceMax;
        }

        $ratingMin = $request->has('rating') ? (int)$request->query('rating') : 0;
        if ($ratingMin > 0) {
            $where[] = 'COALESCE((SELECT AVG(r.rating) FROM reviews r WHERE r.product_id = p.id), 0) >= ?';
            $params[] = $ratingMin;
        }

        $whereClause = implode(' AND ', $where);
        $sort = $request->query('sort', 'newest');
        $orderBy = match ($sort) {
            'price_asc' => 'COALESCE(p.discount_price, p.price) ASC',
            'price_desc' => 'COALESCE(p.discount_price, p.price) DESC',
            'popular' => 'review_count DESC',
            default => 'p.created_at DESC',
        };

        $countStmt = $db->prepare("SELECT COUNT(*) FROM products p JOIN categories c ON p.category_id = c.id WHERE {$whereClause}");
        $countStmt->execute($params);
        $totalProducts = (int)$countStmt->fetchColumn();
        $totalPages = max(1, (int)ceil($totalProducts / $perPage));

        $stmt = $db->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug,
            (SELECT AVG(r.rating) FROM reviews r WHERE r.product_id = p.id) as avg_rating,
            (SELECT COUNT(r.id) FROM reviews r WHERE r.product_id = p.id) as review_count
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE {$whereClause}
            ORDER BY {$orderBy}
            LIMIT {$perPage} OFFSET {$offset}
        ");
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        $allCategories = $db->query("
            SELECT c.*, COUNT(p.id) as product_count
            FROM categories c
            LEFT JOIN products p ON p.category_id = c.id AND p.status = 'active'
            GROUP BY c.id
            ORDER BY c.name
        ")->fetchAll();

        return view('store.products', compact(
            'pageTitle',
            'activePage',
            'page',
            'perPage',
            'categorySlug',
            'search',
            'priceMax',
            'ratingMin',
            'sort',
            'totalProducts',
            'totalPages',
            'products',
            'allCategories'
        ));
    }

    public function productById(int $id)
    {
        request()->query->set('id', $id);

        return $this->product(request());
    }

    public function product(Request $request)
    {
        $db = getDB();
        $productId = (int)$request->query('id', 0);

        if (! $productId) {
            return redirect(SITE_URL . '/pages/products.php');
        }

        $stmt = $db->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.id = ? AND p.status = 'active'
        ");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (! $product) {
            setFlash('error', 'Product not found.');

            return redirect(SITE_URL . '/pages/products.php');
        }

        $pageTitle = $product['name'];

        $reviewsStmt = $db->prepare("
            SELECT r.*, u.full_name FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.product_id = ? ORDER BY r.created_at DESC
        ");
        $reviewsStmt->execute([$productId]);
        $reviews = $reviewsStmt->fetchAll();
        $avgRating = $reviews ? array_sum(array_column($reviews, 'rating')) / count($reviews) : 0;

        $canReview = false;
        if (isLoggedIn()) {
            $purchaseCheck = $db->prepare("
                SELECT COUNT(*) FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                WHERE o.user_id = ? AND oi.product_id = ? AND o.order_status != 'cancelled'
            ");
            $purchaseCheck->execute([session('user_id'), $productId]);
            $canReview = $purchaseCheck->fetchColumn() > 0;
        }

        $relatedStmt = $db->prepare("
            SELECT p.*, c.name as category_name,
            (SELECT AVG(r.rating) FROM reviews r WHERE r.product_id = p.id) as avg_rating
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.category_id = ? AND p.id != ? AND p.status = 'active'
            ORDER BY RAND() LIMIT 4
        ");
        $relatedStmt->execute([$product['category_id'], $productId]);
        $related = $relatedStmt->fetchAll();

        return view('store.product', compact('pageTitle', 'productId', 'product', 'reviews', 'avgRating', 'canReview', 'related'));
    }

    public function storeReview(Request $request, ?int $id = null)
    {
        if (! isLoggedIn()) {
            return redirect(SITE_URL . '/pages/login.php');
        }

        $productId = $id ?: (int)$request->query('id', 0);
        $db = getDB();

        $purchaseCheck = $db->prepare("
            SELECT COUNT(*) FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE o.user_id = ? AND oi.product_id = ? AND o.order_status != 'cancelled'
        ");
        $purchaseCheck->execute([session('user_id'), $productId]);

        if ($purchaseCheck->fetchColumn() < 1) {
            setFlash('error', 'You can only review products you have purchased.');

            return redirect()->to(SITE_URL . '/pages/product.php?id=' . $productId);
        }

        $rating = max(1, min(5, (int)$request->input('rating', 5)));
        $comment = trim($request->input('comment', ''));
        $ins = $db->prepare('INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)');
        $ins->execute([$productId, session('user_id'), $rating, $comment]);
        setFlash('success', 'Review submitted successfully!');

        return redirect()->to(SITE_URL . '/pages/product.php?id=' . $productId);
    }

    public function search(Request $request)
    {
        $db = getDB();
        $query = trim($request->query('q', ''));
        $pageTitle = $query ? "Search: {$query}" : 'Search';
        $activePage = 'products';
        $perPage = 8;
        $page = max(1, (int)$request->query('page', 1));
        $offset = ($page - 1) * $perPage;
        $products = [];
        $totalProducts = 0;

        if ($query) {
            $searchTerm = "%{$query}%";
            $countStmt = $db->prepare("SELECT COUNT(*) FROM products p WHERE p.status = 'active' AND (p.name LIKE ? OR p.description LIKE ?)");
            $countStmt->execute([$searchTerm, $searchTerm]);
            $totalProducts = (int)$countStmt->fetchColumn();

            $stmt = $db->prepare("
                SELECT p.*, c.name as category_name,
                (SELECT AVG(r.rating) FROM reviews r WHERE r.product_id = p.id) as avg_rating
                FROM products p JOIN categories c ON p.category_id = c.id
                WHERE p.status = 'active' AND (p.name LIKE ? OR p.description LIKE ?)
                ORDER BY p.created_at DESC LIMIT {$perPage} OFFSET {$offset}
            ");
            $stmt->execute([$searchTerm, $searchTerm]);
            $products = $stmt->fetchAll();
        }
        $totalPages = max(1, (int)ceil($totalProducts / $perPage));

        return view('store.search', compact('pageTitle', 'activePage', 'query', 'page', 'perPage', 'products', 'totalProducts', 'totalPages'));
    }

    public function contact(Request $request)
    {
        $db = getDB();
        $pageTitle = 'Contact Us';
        $activePage = 'contact';

        if ($request->isMethod('post')) {
            $name = trim($request->input('name', ''));
            $email = trim($request->input('email', ''));
            $subject = trim($request->input('subject', ''));
            $message = trim($request->input('message', ''));

            if (! $name || ! $email || ! $subject || ! $message) {
                setFlash('error', 'All fields are required.');
            } elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                setFlash('error', 'Please enter a valid email.');
            } else {
                $stmt = $db->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)');
                $stmt->execute([$name, $email, $subject, $message]);
                setFlash('success', "Message sent successfully! We'll get back to you soon.");

                return redirect(SITE_URL . '/pages/contact.php');
            }
        }

        return view('store.contact', compact('pageTitle', 'activePage'));
    }

    public function profile(Request $request)
    {
        $db = getDB();
        $pageTitle = 'My Profile';

        $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([session('user_id')]);
        $user = $stmt->fetch();

        if ($request->isMethod('post')) {
            $action = $request->input('action', 'update_profile');

            if ($action === 'update_profile') {
                $fullName = trim($request->input('full_name', ''));
                $phone = trim($request->input('phone', ''));
                $address = trim($request->input('address', ''));

                if (! $fullName) {
                    setFlash('error', 'Name is required.');
                } else {
                    $db->prepare('UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?')
                        ->execute([$fullName, $phone, $address, session('user_id')]);
                    session(['user_name' => $fullName]);
                    setFlash('success', 'Profile updated successfully.');
                }
            }

            if ($action === 'change_password') {
                $currentPw = $request->input('current_password', '');
                $newPw = $request->input('new_password', '');
                $confirmPw = $request->input('confirm_password', '');

                if (! password_verify($currentPw, $user['password'])) {
                    setFlash('error', 'Current password is incorrect.');
                } elseif (strlen($newPw) < 6) {
                    setFlash('error', 'New password must be at least 6 characters.');
                } elseif ($newPw !== $confirmPw) {
                    setFlash('error', 'New passwords do not match.');
                } else {
                    $db->prepare('UPDATE users SET password = ? WHERE id = ?')
                        ->execute([password_hash($newPw, PASSWORD_DEFAULT), session('user_id')]);
                    setFlash('success', 'Password changed successfully.');
                }
            }

            return redirect(SITE_URL . '/pages/profile.php');
        }

        $orderCount = $db->prepare('SELECT COUNT(*) FROM orders WHERE user_id = ?');
        $orderCount->execute([session('user_id')]);
        $totalOrders = $orderCount->fetchColumn();

        $wishCount = $db->prepare('SELECT COUNT(*) FROM wishlist WHERE user_id = ?');
        $wishCount->execute([session('user_id')]);
        $totalWishlist = $wishCount->fetchColumn();

        return view('store.profile', compact('pageTitle', 'user', 'totalOrders', 'totalWishlist'));
    }

    public function orders(Request $request)
    {
        $db = getDB();
        $pageTitle = 'Order History';

        $stmt = $db->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([session('user_id')]);
        $orders = $stmt->fetchAll();

        $viewOrder = null;
        $viewItems = [];
        if ($request->has('view')) {
            $viewId = (int)$request->query('view');
            $vs = $db->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
            $vs->execute([$viewId, session('user_id')]);
            $viewOrder = $vs->fetch();
            if ($viewOrder) {
                $is = $db->prepare('SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?');
                $is->execute([$viewId]);
                $viewItems = $is->fetchAll();
            }
        }

        return view('store.orders', compact('pageTitle', 'orders', 'viewOrder', 'viewItems', 'db'));
    }

    public function wishlist()
    {
        $db = getDB();
        $pageTitle = 'My Wishlist';
        $stmt = $db->prepare("
            SELECT w.*, p.name, p.slug, p.price, p.discount_price, p.image, p.stock_quantity, c.name as category_name,
            (SELECT AVG(r.rating) FROM reviews r WHERE r.product_id = p.id) as avg_rating
            FROM wishlist w
            JOIN products p ON w.product_id = p.id
            JOIN categories c ON p.category_id = c.id
            WHERE w.user_id = ?
            ORDER BY w.added_at DESC
        ");
        $stmt->execute([session('user_id')]);
        $wishlistItems = $stmt->fetchAll();

        return view('store.wishlist', compact('pageTitle', 'wishlistItems'));
    }

    public function wishlistActions(Request $request)
    {
        if (! isLoggedIn()) {
            return response()->json(['success' => false, 'message' => 'Please log in first.', 'redirect' => SITE_URL . '/pages/login.php']);
        }

        $action = $request->input('action', '');
        $productId = (int)$request->input('product_id', 0);
        $db = getDB();

        if ($action === 'add') {
            $existing = $db->prepare('SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?');
            $existing->execute([session('user_id'), $productId]);
            if ($existing->fetch()) {
                return response()->json(['success' => false, 'message' => 'Already in wishlist.']);
            }
            $db->prepare('INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)')->execute([session('user_id'), $productId]);

            return response()->json(['success' => true, 'message' => 'Added to wishlist!']);
        }

        if ($action === 'remove') {
            $db->prepare('DELETE FROM wishlist WHERE user_id = ? AND product_id = ?')->execute([session('user_id'), $productId]);

            return response()->json(['success' => true, 'message' => 'Removed from wishlist.']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid action.']);
    }
}