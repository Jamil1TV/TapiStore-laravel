@extends('layouts.store')

@section('content')
<script>
const siteUrl = '<?php echo SITE_URL; ?>';
const csrfToken = '<?php echo generateCSRFToken(); ?>';
</script>

<div class="page-header">
    <div class="container">
        <h1>Shopping Cart</h1>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>/">Home</a><span>/</span><span>Cart</span>
        </div>
    </div>
</div>

<div class="container">
    <?php if (!isLoggedIn()): ?>
        <div class="empty-cart">
            <h2>Please log in to view your cart</h2>
            <p>You need to be logged in to manage your shopping cart.</p>
            <a href="<?php echo SITE_URL; ?>/pages/login.php" class="btn btn-accent">Log In</a>
        </div>
    <?php elseif (empty($cartItems)): ?>
        <div class="empty-cart">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            <h2>Your cart is empty</h2>
            <p>Looks like you haven't added anything to your cart yet.</p>
            <a href="<?php echo SITE_URL; ?>/pages/products.php" class="btn btn-accent">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-layout">
            <div>
                <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;">
                    <?php foreach ($cartItems as $item):
                        $price = $item['discount_price'] ?? $item['price'];
                        $itemTotal = $price * $item['quantity'];
                    ?>
                    <div class="cart-item">
                        <img src="<?php echo productImage($item['image']); ?>" alt="<?php echo e($item['name']); ?>">
                        <div class="cart-item-info">
                            <h3><a href="<?php echo SITE_URL; ?>/pages/product.php?id=<?php echo $item['product_id']; ?>"><?php echo e($item['name']); ?></a></h3>
                            <p>Unit price: <?php echo formatPrice($price); ?></p>
                        </div>
                        <form method="POST" class="cart-qty">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                            <button type="button" data-action="minus">&#8722;</button>
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock_quantity']; ?>" data-max="<?php echo $item['stock_quantity']; ?>">
                            <button type="button" data-action="plus">+</button>
                            <button type="submit" class="btn btn-sm btn-outline" style="margin-left:8px;">Update</button>
                        </form>
                        <div style="font-weight:700;min-width:80px;text-align:right;"><?php echo formatPrice($itemTotal); ?></div>
                        <form method="POST">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                            <button type="submit" class="cart-remove" title="Remove">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-2">
                    <a href="<?php echo SITE_URL; ?>/pages/products.php" class="btn btn-outline">&larr; Continue Shopping</a>
                </div>
            </div>

            <div class="order-summary">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal (<?php echo count($cartItems); ?> items)</span>
                    <span><?php echo formatPrice($subtotal); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span><?php echo $subtotal >= 50 ? '<span style="color:var(--success);">Free</span>' : formatPrice(5.99); ?></span>
                </div>
                <?php $total = $subtotal + ($subtotal >= 50 ? 0 : 5.99); ?>
                <div class="summary-row total">
                    <span>Total</span>
                    <span><?php echo formatPrice($total); ?></span>
                </div>
                <a href="<?php echo SITE_URL; ?>/pages/checkout.php" class="btn btn-accent btn-block btn-lg mt-2">Proceed to Checkout</a>
            </div>
        </div>
    <?php endif; ?>
</div>
@endsection
