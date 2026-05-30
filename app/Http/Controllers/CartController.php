<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $db = getDB();
        $pageTitle = 'Shopping Cart';
        $activePage = 'cart';

        if ($request->isMethod('post') && isLoggedIn()) {
            $action = $request->input('action', '');
            $productId = (int)$request->input('product_id', 0);

            if ($action === 'update') {
                $qty = max(1, (int)$request->input('quantity', 1));
                $db->prepare('UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?')
                    ->execute([$qty, session('user_id'), $productId]);
                setFlash('success', 'Cart updated.');
            } elseif ($action === 'remove') {
                $db->prepare('DELETE FROM cart WHERE user_id = ? AND product_id = ?')
                    ->execute([session('user_id'), $productId]);
                setFlash('success', 'Item removed from cart.');
            }

            return redirect(SITE_URL . '/pages/cart.php');
        }

        $cartItems = [];
        $subtotal = 0;
        if (isLoggedIn()) {
            $stmt = $db->prepare("
                SELECT c.*, p.name, p.price, p.discount_price, p.image, p.stock_quantity
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?
                ORDER BY c.added_at DESC
            ");
            $stmt->execute([session('user_id')]);
            $cartItems = $stmt->fetchAll();
            foreach ($cartItems as $item) {
                $price = $item['discount_price'] ?? $item['price'];
                $subtotal += $price * $item['quantity'];
            }
        }

        return view('store.cart', compact('pageTitle', 'activePage', 'cartItems', 'subtotal'));
    }

    public function actions(Request $request)
    {
        if (! isLoggedIn()) {
            return response()->json(['success' => false, 'message' => 'Please log in first.', 'redirect' => SITE_URL . '/pages/login.php']);
        }

        $action = $request->input('action', '');
        $productId = (int)$request->input('product_id', 0);
        $quantity = max(1, (int)$request->input('quantity', 1));
        $db = getDB();

        switch ($action) {
            case 'add':
                $prod = $db->prepare("SELECT id, stock_quantity FROM products WHERE id = ? AND status = 'active'");
                $prod->execute([$productId]);
                $product = $prod->fetch();
                if (! $product) {
                    return response()->json(['success' => false, 'message' => 'Product not found.']);
                }
                if ($product['stock_quantity'] < 1) {
                    return response()->json(['success' => false, 'message' => 'Product is out of stock.']);
                }

                $existing = $db->prepare('SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?');
                $existing->execute([session('user_id'), $productId]);
                $cartItem = $existing->fetch();
                if ($cartItem) {
                    $newQty = min($cartItem['quantity'] + $quantity, $product['stock_quantity']);
                    $db->prepare('UPDATE cart SET quantity = ? WHERE id = ?')->execute([$newQty, $cartItem['id']]);
                } else {
                    $db->prepare('INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)')
                        ->execute([session('user_id'), $productId, min($quantity, $product['stock_quantity'])]);
                }

                return response()->json(['success' => true, 'message' => 'Added to cart!', 'cartCount' => getCartCount()]);

            case 'update':
                $db->prepare('UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?')
                    ->execute([$quantity, session('user_id'), $productId]);

                return response()->json(['success' => true, 'message' => 'Cart updated.', 'cartCount' => getCartCount()]);

            case 'remove':
                $db->prepare('DELETE FROM cart WHERE user_id = ? AND product_id = ?')
                    ->execute([session('user_id'), $productId]);

                return response()->json(['success' => true, 'message' => 'Item removed.', 'cartCount' => getCartCount()]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid action.']);
    }
}