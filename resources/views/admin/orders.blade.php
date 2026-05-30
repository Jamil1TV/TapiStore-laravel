<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?> | <?php echo SITE_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
<div class="admin-layout">
    @include('admin.sidebar')
    <div class="admin-main">
        <?php $flash = getFlash(); if ($flash): ?>
            <div class="flash-message flash-<?php echo $flash['type']; ?>" id="flashMessage">
                <span><?php echo e($flash['message']); ?></span>
                <button class="flash-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
        <?php endif; ?>

        <div class="admin-header">
            <h1><?php echo $viewOrder ? 'Order #' . str_pad($viewOrder['id'], 6, '0', STR_PAD_LEFT) : 'Orders'; ?></h1>
            <?php if ($viewOrder): ?>
                <a href="<?php echo SITE_URL; ?>/admin/orders.php" class="btn btn-outline">&larr; Back to Orders</a>
            <?php endif; ?>
        </div>

        <?php if ($viewOrder): ?>
        <!-- Order Detail -->
        <div style="display:grid;grid-template-columns:1fr 350px;gap:24px;">
            <div>
                <div class="admin-card">
                    <h3 style="margin-bottom:16px;">Order Items</h3>
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
                                <td><strong><?php echo formatPrice($item['unit_price'] * $item['quantity']); ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="admin-card">
                    <h3 style="margin-bottom:12px;">Shipping Address</h3>
                    <p style="line-height:1.8;font-size:.9rem;">
                        <strong><?php echo e($viewOrder['full_name']); ?></strong><br>
                        <?php echo e($viewOrder['address']); ?><br>
                        <?php echo e($viewOrder['city']); ?><br>
                        Phone: <?php echo e($viewOrder['phone']); ?><br>
                        Email: <?php echo e($viewOrder['email']); ?>
                    </p>
                </div>
            </div>

            <div>
                <div class="admin-card">
                    <h3 style="margin-bottom:16px;">Order Summary</h3>
                    <div class="summary-row">
                        <span>Date</span>
                        <span><?php echo date('M d, Y', strtotime($viewOrder['created_at'])); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Payment</span>
                        <span><?php echo $viewOrder['payment_method'] === 'cod' ? 'COD' : 'Credit Card'; ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Payment Status</span>
                        <span class="badge badge-<?php echo $viewOrder['payment_status']; ?>"><?php echo ucfirst($viewOrder['payment_status']); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span><?php echo formatPrice($viewOrder['total_amount']); ?></span>
                    </div>
                </div>

                <div class="admin-card">
                    <h3 style="margin-bottom:16px;">Update Status</h3>
                    <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="order_id" value="<?php echo $viewOrder['id']; ?>">
                        <div class="form-group">
                            <select name="order_status" class="form-control">
                                <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                                <option value="<?php echo $s; ?>" <?php echo $viewOrder['order_status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" name="update_status" class="btn btn-accent btn-block">Update Status</button>
                    </form>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- Orders List -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h2>All Orders (<?php echo count($orders); ?>)</h2>
                <form class="admin-search" method="GET">
                    <select name="status" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo $statusFilter === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="date" name="date_from" value="<?php echo e($dateFrom); ?>" onchange="this.form.submit()" title="From date">
                    <input type="date" name="date_to" value="<?php echo e($dateTo); ?>" onchange="this.form.submit()" title="To date">
                </form>
            </div>
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Order ID</th><th>Customer</th><th>Date</th><th>Total</th><th>Payment</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                        <td><?php echo e($order['full_name']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                        <td><strong><?php echo formatPrice($order['total_amount']); ?></strong></td>
                        <td><span class="badge badge-<?php echo $order['payment_status']; ?>"><?php echo ucfirst($order['payment_status']); ?></span></td>
                        <td><span class="badge badge-<?php echo $order['order_status']; ?>"><?php echo ucfirst($order['order_status']); ?></span></td>
                        <td><a href="?view=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline">View</a></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<script>setTimeout(()=>{const f=document.getElementById('flashMessage');if(f){f.style.opacity='0';setTimeout(()=>f.remove(),500);}},4000);</script>
</body></html>
