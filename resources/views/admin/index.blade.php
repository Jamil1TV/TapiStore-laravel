<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle); ?> | <?php echo SITE_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
<div class="admin-layout">
    @include('admin.sidebar')

    <div class="admin-main">
        <div class="admin-header">
            <h1>Dashboard</h1>
            <span style="color:var(--text-muted);font-size:.9rem;">Welcome back, <?php echo e(getCurrentUser()['full_name'] ?? ''); ?></span>
        </div>

        <!-- Stats Cards -->
        <div class="dashboard-cards">
            <div class="dash-card">
                <div class="dash-card-icon products">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                </div>
                <div class="dash-card-info"><h3><?php echo $totalProducts; ?></h3><p>Total Products</p></div>
            </div>
            <div class="dash-card">
                <div class="dash-card-icon orders">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div class="dash-card-info"><h3><?php echo $totalOrders; ?></h3><p>Total Orders</p></div>
            </div>
            <div class="dash-card">
                <div class="dash-card-icon users">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div class="dash-card-info"><h3><?php echo $totalUsers; ?></h3><p>Customers</p></div>
            </div>
            <div class="dash-card">
                <div class="dash-card-icon revenue">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div class="dash-card-info"><h3><?php echo formatPrice($totalRevenue); ?></h3><p>Total Revenue</p></div>
            </div>
        </div>

        <!-- Chart -->
        <div class="chart-container">
            <h3>Orders Per Day (Last 7 Days)</h3>
            <canvas id="ordersChart" width="800" height="250"></canvas>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
            <!-- Recent Orders -->
            <div class="admin-card">
                <div class="admin-card-header"><h2>Recent Orders</h2></div>
                <div class="table-responsive">
                    <table>
                        <thead><tr><th>ID</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach (array_slice($recentOrders, 0, 7) as $order): ?>
                        <tr>
                            <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo e($order['full_name']); ?></td>
                            <td><?php echo formatPrice($order['total_amount']); ?></td>
                            <td><span class="badge badge-<?php echo $order['order_status']; ?>"><?php echo ucfirst($order['order_status']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Low Stock -->
            <div class="admin-card">
                <div class="admin-card-header"><h2>Low Stock Alert</h2></div>
                <?php if (empty($lowStock)): ?>
                    <p style="color:var(--text-muted);padding:20px;text-align:center;">All products are well stocked!</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead><tr><th>Product</th><th>Stock</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($lowStock as $p): ?>
                        <tr>
                            <td><?php echo e($p['name']); ?></td>
                            <td><strong style="color:<?php echo $p['stock_quantity'] == 0 ? 'var(--danger)' : 'var(--warning)'; ?>;"><?php echo $p['stock_quantity']; ?></strong></td>
                            <td><span class="badge <?php echo $p['stock_quantity'] == 0 ? 'badge-cancelled' : 'badge-pending'; ?>"><?php echo $p['stock_quantity'] == 0 ? 'Out of Stock' : 'Low'; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Simple bar chart using Canvas API
const canvas = document.getElementById('ordersChart');
if (canvas) {
    const ctx = canvas.getContext('2d');
    const data = <?php echo json_encode($chartData); ?>;
    const labels = [];
    const values = [];

    // Fill in 7 days
    for (let i = 6; i >= 0; i--) {
        const d = new Date();
        d.setDate(d.getDate() - i);
        const ds = d.toISOString().split('T')[0];
        labels.push(d.toLocaleDateString('en', { weekday: 'short', month: 'short', day: 'numeric' }));
        const found = data.find(r => r.date === ds);
        values.push(found ? parseInt(found.count) : 0);
    }

    const maxVal = Math.max(...values, 1);
    const padding = { top: 20, right: 20, bottom: 40, left: 50 };
    const w = canvas.width - padding.left - padding.right;
    const h = canvas.height - padding.top - padding.bottom;
    const barW = w / labels.length * 0.6;
    const gap = w / labels.length;

    ctx.fillStyle = '#f8fafc';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // Grid lines
    ctx.strokeStyle = '#e2e8f0';
    ctx.lineWidth = 1;
    for (let i = 0; i <= 4; i++) {
        const y = padding.top + (h / 4) * i;
        ctx.beginPath(); ctx.moveTo(padding.left, y); ctx.lineTo(canvas.width - padding.right, y); ctx.stroke();
        ctx.fillStyle = '#94a3b8'; ctx.font = '11px Inter, sans-serif'; ctx.textAlign = 'right';
        ctx.fillText(Math.round(maxVal - (maxVal / 4) * i), padding.left - 8, y + 4);
    }

    // Bars
    values.forEach((val, i) => {
        const x = padding.left + gap * i + (gap - barW) / 2;
        const barH = (val / maxVal) * h;
        const y = padding.top + h - barH;

        const grad = ctx.createLinearGradient(x, y, x, y + barH);
        grad.addColorStop(0, '#f97316');
        grad.addColorStop(1, '#ea580c');
        ctx.fillStyle = grad;
        ctx.beginPath();
        ctx.roundRect(x, y, barW, barH, [4, 4, 0, 0]);
        ctx.fill();

        // Value on top
        if (val > 0) {
            ctx.fillStyle = '#1e293b'; ctx.font = 'bold 12px Inter, sans-serif'; ctx.textAlign = 'center';
            ctx.fillText(val, x + barW / 2, y - 6);
        }

        // Label
        ctx.fillStyle = '#64748b'; ctx.font = '10px Inter, sans-serif'; ctx.textAlign = 'center';
        ctx.fillText(labels[i], x + barW / 2, canvas.height - 8);
    });
}
</script>
</body>
</html>
