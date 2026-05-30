@extends('layouts.store')

@section('content')
<script>const siteUrl = '<?php echo SITE_URL; ?>'; const csrfToken = '<?php echo generateCSRFToken(); ?>';</script>

<div class="page-header">
    <div class="container">
        <h1><?php echo $viewOrder ? 'Order #' . str_pad($viewOrder['id'], 6, '0', STR_PAD_LEFT) : 'Order History'; ?></h1>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>/">Home</a><span>/</span>
            <?php if ($viewOrder): ?>
                <a href="<?php echo SITE_URL; ?>/pages/orders.php">Orders</a><span>/</span>
                <span>Order Details</span>
            <?php else: ?>
                <span>Orders</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container" style="padding:40px 20px;">
    <?php if ($viewOrder): ?>
        <!-- Order Detail View -->
        <div style="max-width:800px;margin:0 auto;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                <div>
                    <span class="badge badge-<?php echo $viewOrder['order_status']; ?>"><?php echo ucfirst($viewOrder['order_status']); ?></span>
                    <span style="color:var(--text-muted);margin-left:8px;font-size:.9rem;">
                        Placed on <?php echo date('M d, Y \a\t h:i A', strtotime($viewOrder['created_at'])); ?>
                    </span>
                </div>
                <a href="<?php echo SITE_URL; ?>/pages/orders.php" class="btn btn-outline btn-sm">&larr; Back to Orders</a>
            </div>

            <div class="admin-card">
                <h3 style="margin-bottom:16px;">Items</h3>
                <div class="table-responsive">
                    <table>
                        <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                        <tbody>
                        <?php foreach ($viewItems as $item): ?>
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
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div class="admin-card">
                    <h3 style="margin-bottom:12px;">Shipping</h3>
                    <p style="font-size:.9rem;color:var(--text-light);line-height:1.8;">
                        <strong><?php echo e($viewOrder['full_name']); ?></strong><br>
                        <?php echo e($viewOrder['address']); ?><br>
                        <?php echo e($viewOrder['city']); ?><br>
                        <?php echo e($viewOrder['phone']); ?><br>
                        <?php echo e($viewOrder['email']); ?>
                    </p>
                </div>
                <div class="admin-card">
                    <h3 style="margin-bottom:12px;">Payment</h3>
                    <p style="font-size:.9rem;color:var(--text-light);line-height:1.8;">
                        Method: <?php echo $viewOrder['payment_method'] === 'cod' ? 'Cash on Delivery' : 'Credit Card'; ?><br>
                        Status: <span class="badge badge-<?php echo $viewOrder['payment_status']; ?>"><?php echo ucfirst($viewOrder['payment_status']); ?></span><br>
                        <strong style="font-size:1.1rem;color:var(--text);margin-top:8px;display:block;">Total: <?php echo formatPrice($viewOrder['total_amount']); ?></strong>
                    </p>
                </div>
            </div>
        </div>

    <?php elseif (empty($orders)): ?>
        <div class="empty-cart">
            <h2>No orders yet</h2>
            <p>You haven't placed any orders yet. Start shopping!</p>
            <a href="<?php echo SITE_URL; ?>/pages/products.php" class="btn btn-accent">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr><th>Order ID</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order):
                    $itemCount = $db->prepare("SELECT SUM(quantity) FROM order_items WHERE order_id = ?");
                    $itemCount->execute([$order['id']]);
                ?>
                    <tr>
                        <td><strong>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                        <td><?php echo $itemCount->fetchColumn(); ?> items</td>
                        <td><strong><?php echo formatPrice($order['total_amount']); ?></strong></td>
                        <td><span class="badge badge-<?php echo $order['order_status']; ?>"><?php echo ucfirst($order['order_status']); ?></span></td>
                        <td><a href="?view=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline">View</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
@endsection
