@extends('layouts.store')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <h1>Set New Password</h1>
        <p class="auth-subtitle">Enter your new password below</p>

        <?php if ($error): ?>
            <div style="background:var(--danger-bg);color:#991b1b;padding:12px;border-radius:var(--radius);margin-bottom:16px;font-size:.9rem;">
                <?php echo e($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="resetPasswordForm">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" name="password" id="password" class="form-control" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required minlength="6">
            </div>

            <button type="submit" class="btn btn-accent btn-block btn-lg">Save Password</button>
        </form>

        <div class="auth-footer">
            <a href="<?php echo SITE_URL; ?>/pages/login.php">Back to Login</a>
        </div>
    </div>
</div>
@endsection
