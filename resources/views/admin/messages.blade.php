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
            <h1>Contact Messages</h1>
        </div>

        <div class="admin-card">
            <div class="admin-card-header"><h2>All Messages (<?php echo count($messages); ?>)</h2></div>
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php if (empty($messages)): ?>
                        <tr><td colspan="6" style="text-align:center;">No messages found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($messages as $msg): ?>
                    <tr style="<?php echo $msg['is_read'] ? 'opacity: 0.7;' : 'font-weight: 600; background: #f8fafc;'; ?>">
                        <td><?php echo date('M d, Y', strtotime($msg['created_at'])); ?></td>
                        <td><?php echo e($msg['name']); ?></td>
                        <td><a href="mailto:<?php echo e($msg['email']); ?>"><?php echo e($msg['email']); ?></a></td>
                        <td><?php echo e($msg['subject']); ?></td>
                        <td>
                            <?php if ($msg['is_read']): ?>
                                <span class="badge" style="background:#e2e8f0;color:#475569;">Read</span>
                            <?php else: ?>
                                <span class="badge badge-active">New</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex;gap:4px;">
                                <a href="?view=<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline">View</a>
                                <?php if (!$msg['is_read']): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="action" value="mark_read">
                                    <input type="hidden" name="id" value="<?php echo $msg['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-accent">Mark Read</button>
                                </form>
                                <?php endif; ?>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this message?')">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $msg['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php if (isset($_GET['view']) && $_GET['view'] == $msg['id']): ?>
                    <tr>
                        <td colspan="6" style="background: #f1f5f9; padding: 20px;">
                            <strong>Message:</strong><br><br>
                            <?php echo nl2br(e($msg['message'])); ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>setTimeout(()=>{const f=document.getElementById('flashMessage');if(f){f.style.opacity='0';setTimeout(()=>f.remove(),500);}},4000);</script>
</body></html>
