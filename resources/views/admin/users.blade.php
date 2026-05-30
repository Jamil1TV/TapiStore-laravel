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
            <h1>Users</h1>
            <span style="color:var(--text-muted);"><?php echo count($users); ?> registered users</span>
        </div>

        <div class="admin-card">
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Orders</th><th>Registered</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:50%;background:<?php echo $u['role'] === 'admin' ? 'var(--info)' : 'var(--accent)'; ?>;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0;">
                                    <?php echo strtoupper(substr($u['full_name'], 0, 1)); ?>
                                </div>
                                <strong><?php echo e($u['full_name']); ?></strong>
                            </div>
                        </td>
                        <td><?php echo e($u['email']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $u['role']; ?>"><?php echo ucfirst($u['role']); ?></span>
                        </td>
                        <td><?php echo $u['order_count']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                        <td>
                            <?php if ($u['id'] !== session('user_id')): ?>
                            <div style="display:flex;gap:4px;">
                                <form method="POST" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="action" value="change_role">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <input type="hidden" name="role" value="<?php echo $u['role'] === 'admin' ? 'customer' : 'admin'; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline" onclick="return confirm('Change role to <?php echo $u['role'] === 'admin' ? 'customer' : 'admin'; ?>?')">
                                        <?php echo $u['role'] === 'admin' ? 'Make Customer' : 'Make Admin'; ?>
                                    </button>
                                </form>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this user? This cannot be undone.')">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                            <?php else: ?>
                                <span style="color:var(--text-muted);font-size:.8rem;">Current user</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>setTimeout(()=>{const f=document.getElementById('flashMessage');if(f){f.style.opacity='0';setTimeout(()=>f.remove(),500);}},4000);</script>
</body></html>
