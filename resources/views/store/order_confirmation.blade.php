@extends('layouts.store')

@section('content')
<script>const siteUrl = '<?php echo SITE_URL; ?>'; const csrfToken = '<?php echo generateCSRFToken(); ?>';</script>

<div class="container">
    <div class="confirmation-card">
        <div class="confirmation-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <h1>Thank You!</h1>
        <p style="color:var(--text-light);font-size:1.1rem;">Your order has been placed successfully.</p>
        <p style="margin-top:8px;">Order ID: <strong>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></strong></p>
        <p style="color:var(--text-muted);font-size:.9rem;">Estimated delivery: 3-5 business days</p>

        <div class="order-details-summary">
            <h3 style="margin-bottom:16px;">Order Details</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderItems as $item): ?>
                        <tr>
                            <td style="display:flex;align-items:center;gap:10px;">
                                <img src="<?php echo productImage($item['image']); ?>" class="table-img" alt="">
                                <?php echo e($item['name']); ?>
                            </td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo formatPrice($item['unit_price']); ?></td>
                            <td><?php echo formatPrice($item['unit_price'] * $item['quantity']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:16px;padding-top:16px;border-top:2px solid var(--border);font-weight:700;font-size:1.1rem;">
                <span>Total Paid</span>
                <span><?php echo formatPrice($order['total_amount']); ?></span>
            </div>
            <div style="margin-top:16px;padding:16px;background:var(--bg-light);border-radius:var(--radius);font-size:.9rem;">
                <strong>Shipping to:</strong> <?php echo e($order['full_name']); ?>, <?php echo e($order['address']); ?>, <?php echo e($order['city']); ?><br>
                <strong>Payment:</strong> <?php echo $order['payment_method'] === 'cod' ? 'Cash on Delivery' : 'Credit Card'; ?>
            </div>
        </div>

        <div style="margin-top:24px;display:flex;gap:12px;justify-content:center;">
            <a href="<?php echo SITE_URL; ?>/pages/orders.php" class="btn btn-accent">View Order History</a>
            <a href="<?php echo SITE_URL; ?>/pages/products.php" class="btn btn-outline">Continue Shopping</a>
        </div>
    </div>
</div>
@endsection
