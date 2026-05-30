<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $db = getDB();
        $pageTitle = 'Checkout';

        [$cartItems, $subtotal, $shipping, $total] = $this->cartTotals();
        if (empty($cartItems)) {
            setFlash('warning', 'Your cart is empty.');

            return redirect(SITE_URL . '/pages/cart.php');
        }

        $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([session('user_id')]);
        $user = $stmt->fetch();

        if ($request->isMethod('post')) {
            $fullName = trim($request->input('full_name', ''));
            $email = trim($request->input('email', ''));
            $phone = trim($request->input('phone', ''));
            $address = trim($request->input('address', ''));
            $city = trim($request->input('city', ''));
            $paymentMethod = $request->input('payment_method', 'cod');

            $errors = [];
            if (! $fullName) {
                $errors[] = 'Full name is required.';
            }
            if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid email is required.';
            }
            if (! $phone) {
                $errors[] = 'Phone number is required.';
            }
            if (! $address) {
                $errors[] = 'Address is required.';
            }
            if (! $city) {
                $errors[] = 'City is required.';
            }

            if ($errors) {
                setFlash('error', implode(' ', $errors));
            } else {
                try {
                    $db->beginTransaction();

                    $orderStmt = $db->prepare("
                        INSERT INTO orders (user_id, full_name, email, phone, address, city, total_amount, payment_method, payment_status, order_status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
                    ");
                    $paymentStatus = 'pending';
                    $orderStmt->execute([session('user_id'), $fullName, $email, $phone, $address, $city, $total, $paymentMethod, $paymentStatus]);
                    $orderId = $db->lastInsertId();

                    $itemStmt = $db->prepare('INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)');
                    $stockCheckStmt = $db->prepare('SELECT stock_quantity, name FROM products WHERE id = ? FOR UPDATE');
                    $stockUpdateStmt = $db->prepare('UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?');

                    foreach ($cartItems as $item) {
                        $stockCheckStmt->execute([$item['product_id']]);
                        $currentProduct = $stockCheckStmt->fetch();

                        if (! $currentProduct || $currentProduct['stock_quantity'] < $item['quantity']) {
                            throw new Exception("Sorry, '" . ($currentProduct['name'] ?? 'Unknown') . "' does not have enough stock available.");
                        }

                        $unitPrice = $item['discount_price'] ?? $item['price'];
                        $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $unitPrice]);
                        $stockUpdateStmt->execute([$item['quantity'], $item['product_id']]);
                    }

                    $db->prepare('DELETE FROM cart WHERE user_id = ?')->execute([session('user_id')]);
                    $db->commit();
                    session(['last_order_id' => $orderId]);

                    return redirect(SITE_URL . '/pages/order_confirmation.php');
                } catch (Exception $e) {
                    $db->rollBack();
                    setFlash('error', $e->getMessage() ?: 'Order placement failed. Please try again.');
                }
            }
        }

        return view('store.checkout', compact('pageTitle', 'cartItems', 'subtotal', 'shipping', 'total', 'user'));
    }

    public function confirmation()
    {
        $db = getDB();
        $pageTitle = 'Order Confirmed';
        $orderId = session('last_order_id', 0);

        if (! $orderId) {
            return redirect(SITE_URL . '/');
        }

        $orderStmt = $db->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
        $orderStmt->execute([$orderId, session('user_id')]);
        $order = $orderStmt->fetch();

        if (! $order) {
            return redirect(SITE_URL . '/');
        }

        $items = $db->prepare("
            SELECT oi.*, p.name, p.image FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $items->execute([$orderId]);
        $orderItems = $items->fetchAll();
        session()->forget('last_order_id');

        return view('store.order_confirmation', compact('pageTitle', 'orderId', 'order', 'orderItems'));
    }

    private function cartTotals(): array
    {
        $stmt = getDB()->prepare("
            SELECT c.*, p.name, p.price, p.discount_price, p.image, p.stock_quantity
            FROM cart c JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        $stmt->execute([session('user_id')]);
        $cartItems = $stmt->fetchAll();
        $subtotal = 0;

        foreach ($cartItems as $item) {
            $subtotal += ($item['discount_price'] ?? $item['price']) * $item['quantity'];
        }

        $shipping = $subtotal >= 50 ? 0 : 5.99;
        $total = $subtotal + $shipping;

        return [$cartItems, $subtotal, $shipping, $total];
    }
}