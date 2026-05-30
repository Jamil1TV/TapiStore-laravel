<?php
/**
 * Admin Sidebar Include
 * Reusable admin navigation sidebar.
 */
?>
<aside class="admin-sidebar">
    <div class="logo">
        <a href="<?php echo SITE_URL; ?>/admin/" style="text-decoration:none;">
            <span class="logo-icon" style="color:var(--accent);font-size:1.4rem;">&#9670;</span>
            <span style="color:#fff;font-size:1.2rem;font-weight:800;">TAPI<span style="color:var(--accent);">Admin</span></span>
        </a>
    </div>
    <nav class="admin-nav">
        <a href="<?php echo SITE_URL; ?>/admin/" class="<?php echo ($adminPage ?? '') === 'dashboard' ? 'active' : ''; ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="<?php echo SITE_URL; ?>/admin/products.php" class="<?php echo ($adminPage ?? '') === 'products' ? 'active' : ''; ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            Products
        </a>
        <a href="<?php echo SITE_URL; ?>/admin/categories.php" class="<?php echo ($adminPage ?? '') === 'categories' ? 'active' : ''; ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
            Categories
        </a>
        <a href="<?php echo SITE_URL; ?>/admin/orders.php" class="<?php echo ($adminPage ?? '') === 'orders' ? 'active' : ''; ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Orders
        </a>
        <a href="<?php echo SITE_URL; ?>/admin/users.php" class="<?php echo ($adminPage ?? '') === 'users' ? 'active' : ''; ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Users
        </a>
        <a href="<?php echo SITE_URL; ?>/admin/messages.php" class="<?php echo ($adminPage ?? '') === 'messages' ? 'active' : ''; ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
            Messages
        </a>
        <div style="border-top:1px solid rgba(255,255,255,.1);margin:16px 0;"></div>
        <a href="<?php echo SITE_URL; ?>/" style="color:rgba(255,255,255,.5);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            View Store
        </a>
        <a href="<?php echo SITE_URL; ?>/pages/logout.php" style="color:rgba(255,255,255,.5);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Logout
        </a>
    </nav>
</aside>
