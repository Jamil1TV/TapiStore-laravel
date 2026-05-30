@extends('layouts.store')

@section('content')
<div class="page-header">
    <div class="container">
        <h1>Checkout</h1>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>/">Home</a><span>/</span>
            <a href="<?php echo SITE_URL; ?>/pages/cart.php">Cart</a><span>/</span>
            <span>Checkout</span>
        </div>
    </div>
</div>

<div class="container">
    <form method="POST" id="checkoutForm" onsubmit="return validateForm('checkoutForm')">
        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
        <div class="checkout-layout">
            <div>
                <!-- Shipping Info -->
                <div class="checkout-form">
                    <h2>Shipping Information</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name">Full Name *</label>
                            <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo e($user['full_name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?php echo e($user['email'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone *</label>
                            <input type="tel" name="phone" id="phone" class="form-control" value="<?php echo e($user['phone'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="city">City *</label>
                            <input type="text" name="city" id="city" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">Address *</label>
                        <textarea name="address" id="address" class="form-control" rows="3" required><?php echo e($user['address'] ?? ''); ?></textarea>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="checkout-form mt-3">
                    <h2>Payment Method</h2>
                    <div class="payment-methods">
                        <label class="payment-option selected">
                            <input type="radio" name="payment_method" value="cod" checked>
                            <div>
                                <strong>Cash on Delivery</strong>
                                <p style="font-size:.85rem;color:var(--text-muted);margin:0;">Pay when your order arrives</p>
                            </div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="credit_card">
                            <div>
                                <strong>Credit Card</strong>
                                <p style="font-size:.85rem;color:var(--text-muted);margin:0;">Secure online payment</p>
                            </div>
                        </label>
                    </div>
                    <div class="credit-card-fields" id="creditCardFields">
                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="text" class="form-control" placeholder="MM/YY" maxlength="5">
                            </div>
                            <div class="form-group">
                                <label>CVV</label>
                                <input type="text" class="form-control" placeholder="123" maxlength="3">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h3>Order Summary</h3>
                <?php foreach ($cartItems as $item):
                    $price = $item['discount_price'] ?? $item['price'];
                ?>
                <div class="summary-row" style="font-size:.85rem;">
                    <span><?php echo e($item['name']); ?> &times; <?php echo $item['quantity']; ?></span>
                    <span><?php echo formatPrice($price * $item['quantity']); ?></span>
                </div>
                <?php endforeach; ?>
                <div class="summary-row" style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border);">
                    <span>Subtotal</span>
                    <span><?php echo formatPrice($subtotal); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span><?php echo $shipping === 0 ? '<span style="color:var(--success)">Free</span>' : formatPrice($shipping); ?></span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span><?php echo formatPrice($total); ?></span>
                </div>
                <button type="submit" class="btn btn-accent btn-block btn-lg mt-2">Place Order</button>
                <p style="text-align:center;font-size:.8rem;color:var(--text-muted);margin-top:12px;">By placing this order you agree to our Terms of Service.</p>
            </div>
        </div>
    </form>
</div>

<script>
const siteUrl = '<?php echo SITE_URL; ?>';
const csrfToken = '<?php echo generateCSRFToken(); ?>';
</script>
@endsection
