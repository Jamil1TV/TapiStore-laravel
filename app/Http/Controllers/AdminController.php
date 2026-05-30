<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $db = getDB();
        $pageTitle = 'Admin Dashboard';
        $adminPage = 'dashboard';
        $totalProducts = $db->query('SELECT COUNT(*) FROM products')->fetchColumn();
        $totalOrders = $db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
        $totalUsers = $db->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn();
        $totalRevenue = $db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE order_status != 'cancelled'")->fetchColumn();
        $recentOrders = $db->query("
            SELECT o.*, (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
            FROM orders o ORDER BY o.created_at DESC LIMIT 10
        ")->fetchAll();
        $lowStock = $db->query("SELECT * FROM products WHERE stock_quantity < 5 AND status = 'active' ORDER BY stock_quantity ASC")->fetchAll();
        $chartData = $db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count
            FROM orders
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date
        ")->fetchAll();

        return view('admin.index', compact('pageTitle', 'adminPage', 'totalProducts', 'totalOrders', 'totalUsers', 'totalRevenue', 'recentOrders', 'lowStock', 'chartData'));
    }

    public function products(Request $request)
    {
        $db = getDB();
        $pageTitle = 'Manage Products';
        $adminPage = 'products';

        if ($request->isMethod('post')) {
            $action = $request->input('action', '');

            if ($action === 'add' || $action === 'edit') {
                $id = (int)$request->input('id', 0);
                $name = trim($request->input('name', ''));
                $slug = slugify($name);
                $categoryId = (int)$request->input('category_id', 0);
                $description = trim($request->input('description', ''));
                $price = (float)$request->input('price', 0);
                $discountPrice = $request->filled('discount_price') ? (float)$request->input('discount_price') : null;
                $stockQty = (int)$request->input('stock_quantity', 0);
                $isFeatured = $request->has('is_featured') ? 1 : 0;
                $status = $request->input('status', 'active');
                $imageName = $request->input('existing_image', '');

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    if (! in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'])) {
                        setFlash('error', 'Invalid file type. Only JPG, PNG, WEBP allowed.');
                        return redirect(SITE_URL . '/admin/products.php');
                    }
                    if ($file->getSize() > 2 * 1024 * 1024) {
                        setFlash('error', 'File too large. Max 2MB.');
                        return redirect(SITE_URL . '/admin/products.php');
                    }
                    $imageName = uniqid('prod_') . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/products'), $imageName);
                }

                if ($action === 'add') {
                    $stmt = $db->prepare('INSERT INTO products (name, slug, category_id, description, price, discount_price, stock_quantity, image, is_featured, status) VALUES (?,?,?,?,?,?,?,?,?,?)');
                    $stmt->execute([$name, $slug, $categoryId, $description, $price, $discountPrice, $stockQty, $imageName, $isFeatured, $status]);
                    setFlash('success', 'Product added successfully.');
                } else {
                    $stmt = $db->prepare('UPDATE products SET name=?, slug=?, category_id=?, description=?, price=?, discount_price=?, stock_quantity=?, image=?, is_featured=?, status=? WHERE id=?');
                    $stmt->execute([$name, $slug, $categoryId, $description, $price, $discountPrice, $stockQty, $imageName, $isFeatured, $status, $id]);
                    setFlash('success', 'Product updated successfully.');
                }

                return redirect(SITE_URL . '/admin/products.php');
            }

            if ($action === 'delete') {
                $db->prepare('DELETE FROM products WHERE id = ?')->execute([(int)$request->input('id', 0)]);
                setFlash('success', 'Product deleted.');

                return redirect(SITE_URL . '/admin/products.php');
            }
        }

        $search = trim($request->query('search', ''));
        $catFilter = $request->query('category', '');
        $where = [];
        $params = [];
        if ($search) {
            $where[] = 'p.name LIKE ?';
            $params[] = "%{$search}%";
        }
        if ($catFilter) {
            $where[] = 'c.slug = ?';
            $params[] = $catFilter;
        }
        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $products = $db->prepare("
            SELECT p.*, c.name as category_name FROM products p
            JOIN categories c ON p.category_id = c.id
            {$whereClause} ORDER BY p.created_at DESC
        ");
        $products->execute($params);
        $products = $products->fetchAll();
        $categories = $db->query('SELECT * FROM categories ORDER BY name')->fetchAll();
        $editProduct = null;
        if ($request->has('edit')) {
            $es = $db->prepare('SELECT * FROM products WHERE id = ?');
            $es->execute([(int)$request->query('edit')]);
            $editProduct = $es->fetch();
        }
        $showForm = $request->has('add') || $editProduct;

        return view('admin.products', compact('pageTitle', 'adminPage', 'search', 'catFilter', 'products', 'categories', 'editProduct', 'showForm'));
    }

    public function categories(Request $request)
    {
        $db = getDB();
        $pageTitle = 'Manage Categories';
        $adminPage = 'categories';

        if ($request->isMethod('post')) {
            $action = $request->input('action', '');

            if ($action === 'add' || $action === 'edit') {
                $id = (int)$request->input('id', 0);
                $name = trim($request->input('name', ''));
                $slug = slugify($name);
                $description = trim($request->input('description', ''));
                $imageName = $request->input('existing_image', '');

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    if (in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp']) && $file->getSize() <= 2 * 1024 * 1024) {
                        $imageName = uniqid('cat_') . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('assets/images/categories'), $imageName);
                    }
                }

                if ($action === 'add') {
                    $db->prepare('INSERT INTO categories (name, slug, description, image) VALUES (?,?,?,?)')
                        ->execute([$name, $slug, $description, $imageName]);
                    setFlash('success', 'Category added.');
                } else {
                    $db->prepare('UPDATE categories SET name=?, slug=?, description=?, image=? WHERE id=?')
                        ->execute([$name, $slug, $description, $imageName, $id]);
                    setFlash('success', 'Category updated.');
                }

                return redirect(SITE_URL . '/admin/categories.php');
            }

            if ($action === 'delete') {
                $id = (int)$request->input('id', 0);
                $productCount = $db->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
                $productCount->execute([$id]);
                if ($productCount->fetchColumn() > 0) {
                    setFlash('error', 'Cannot delete: category has associated products.');
                } else {
                    $db->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
                    setFlash('success', 'Category deleted.');
                }

                return redirect(SITE_URL . '/admin/categories.php');
            }
        }

        $categories = $db->query("
            SELECT c.*, COUNT(p.id) as product_count
            FROM categories c LEFT JOIN products p ON p.category_id = c.id
            GROUP BY c.id ORDER BY c.name
        ")->fetchAll();
        $editCat = null;
        if ($request->has('edit')) {
            $es = $db->prepare('SELECT * FROM categories WHERE id = ?');
            $es->execute([(int)$request->query('edit')]);
            $editCat = $es->fetch();
        }
        $showForm = $request->has('add') || $editCat;

        return view('admin.categories', compact('pageTitle', 'adminPage', 'categories', 'editCat', 'showForm'));
    }

    public function orders(Request $request)
    {
        $db = getDB();
        $pageTitle = 'Manage Orders';
        $adminPage = 'orders';

        if ($request->isMethod('post') && $request->has('update_status')) {
            $orderId = (int)$request->input('order_id');
            $newStatus = $request->input('order_status');
            if (in_array($newStatus, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'], true)) {
                $db->prepare('UPDATE orders SET order_status = ? WHERE id = ?')->execute([$newStatus, $orderId]);
                if ($newStatus === 'delivered') {
                    $db->prepare("UPDATE orders SET payment_status = 'paid' WHERE id = ?")->execute([$orderId]);
                }
                setFlash('success', 'Order status updated.');
            }

            return redirect(SITE_URL . '/admin/orders.php' . ($request->has('view') ? '?view=' . $orderId : ''));
        }

        $statusFilter = $request->query('status', '');
        $dateFrom = $request->query('date_from', '');
        $dateTo = $request->query('date_to', '');
        $where = [];
        $params = [];
        if ($statusFilter) {
            $where[] = 'o.order_status = ?';
            $params[] = $statusFilter;
        }
        if ($dateFrom) {
            $where[] = 'DATE(o.created_at) >= ?';
            $params[] = $dateFrom;
        }
        if ($dateTo) {
            $where[] = 'DATE(o.created_at) <= ?';
            $params[] = $dateTo;
        }
        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $orders = $db->prepare("SELECT o.* FROM orders o {$whereClause} ORDER BY o.created_at DESC");
        $orders->execute($params);
        $orders = $orders->fetchAll();

        $viewOrder = null;
        $viewItems = [];
        if ($request->has('view')) {
            $vs = $db->prepare('SELECT * FROM orders WHERE id = ?');
            $vs->execute([(int)$request->query('view')]);
            $viewOrder = $vs->fetch();
            if ($viewOrder) {
                $is = $db->prepare('SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?');
                $is->execute([$viewOrder['id']]);
                $viewItems = $is->fetchAll();
            }
        }

        return view('admin.orders', compact('pageTitle', 'adminPage', 'statusFilter', 'dateFrom', 'dateTo', 'orders', 'viewOrder', 'viewItems'));
    }

    public function users(Request $request)
    {
        $db = getDB();
        $pageTitle = 'Manage Users';
        $adminPage = 'users';

        if ($request->isMethod('post')) {
            $action = $request->input('action', '');
            $userId = (int)$request->input('user_id', 0);
            if ($action === 'change_role' && $userId !== (int)session('user_id')) {
                $newRole = $request->input('role') === 'admin' ? 'admin' : 'customer';
                $db->prepare('UPDATE users SET role = ? WHERE id = ?')->execute([$newRole, $userId]);
                setFlash('success', 'User role updated.');
            }
            if ($action === 'delete' && $userId !== (int)session('user_id')) {
                $db->prepare('DELETE FROM users WHERE id = ?')->execute([$userId]);
                setFlash('success', 'User deleted.');
            }

            return redirect(SITE_URL . '/admin/users.php');
        }

        $users = $db->query("
            SELECT u.*,
            (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count
            FROM users u ORDER BY u.created_at DESC
        ")->fetchAll();

        return view('admin.users', compact('pageTitle', 'adminPage', 'users'));
    }

    public function messages(Request $request)
    {
        $db = getDB();
        $pageTitle = 'Contact Messages';
        $adminPage = 'messages';

        if ($request->isMethod('post')) {
            $action = $request->input('action', '');
            $id = (int)$request->input('id', 0);
            if ($action === 'mark_read') {
                $db->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = ?')->execute([$id]);
                setFlash('success', 'Message marked as read.');
            } elseif ($action === 'delete') {
                $db->prepare('DELETE FROM contact_messages WHERE id = ?')->execute([$id]);
                setFlash('success', 'Message deleted.');
            }

            return redirect(SITE_URL . '/admin/messages.php');
        }

        $messages = $db->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();

        return view('admin.messages', compact('pageTitle', 'adminPage', 'messages'));
    }
}