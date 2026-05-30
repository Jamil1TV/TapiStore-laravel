<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (isLoggedIn()) {
            return redirect(SITE_URL . '/');
        }

        $pageTitle = 'Login';
        $error = '';

        if ($request->isMethod('post')) {
            $email = trim($request->input('email', ''));
            $password = $request->input('password', '');

            if (! $email || ! $password) {
                $error = 'Please fill in all fields.';
            } else {
                $stmt = getDB()->prepare('SELECT * FROM users WHERE email = ?');
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    loginUser($user);

                    if ($request->boolean('remember')) {
                        cookie()->queue('remember_email', $email, 60 * 24 * 30);
                    }

                    $redirectUrl = $request->query('redirect', SITE_URL . '/');
                    if (str_contains($redirectUrl, '://') && ! str_starts_with($redirectUrl, SITE_URL)) {
                        $redirectUrl = SITE_URL . '/';
                    }

                    setFlash('success', 'Welcome back, ' . $user['full_name'] . '!');

                    return redirect($redirectUrl);
                }

                $error = 'Invalid email or password.';
            }
        }

        return view('auth.login', compact('pageTitle', 'error'));
    }

    public function register(Request $request)
    {
        if (isLoggedIn()) {
            return redirect(SITE_URL . '/');
        }

        $pageTitle = 'Register';
        $errors = [];

        if ($request->isMethod('post')) {
            $fullName = trim($request->input('full_name', ''));
            $email = trim($request->input('email', ''));
            $phone = trim($request->input('phone', ''));
            $password = $request->input('password', '');
            $confirmPassword = $request->input('confirm_password', '');

            if (! $fullName) {
                $errors[] = 'Full name is required.';
            }
            if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Valid email is required.';
            }
            if (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters.';
            }
            if ($password !== $confirmPassword) {
                $errors[] = 'Passwords do not match.';
            }

            if (empty($errors)) {
                $check = getDB()->prepare('SELECT id FROM users WHERE email = ?');
                $check->execute([$email]);
                if ($check->fetch()) {
                    $errors[] = 'Email already registered.';
                }
            }

            if (empty($errors)) {
                $stmt = getDB()->prepare("INSERT INTO users (full_name, email, password, phone, role) VALUES (?, ?, ?, ?, 'customer')");
                $stmt->execute([$fullName, $email, password_hash($password, PASSWORD_DEFAULT), $phone]);
                setFlash('success', 'Registration successful! Please log in.');

                return redirect(SITE_URL . '/pages/login.php');
            }
        }

        return view('auth.register', compact('pageTitle', 'errors'));
    }

    public function forgotPassword(Request $request)
    {
        if (isLoggedIn()) {
            return redirect(SITE_URL . '/');
        }

        $pageTitle = 'Reset Password';
        $error = '';
        $success = '';

        if ($request->isMethod('post')) {
            $email = trim($request->input('email', ''));

            if (! $email) {
                $error = 'Please enter your email address.';
            } else {
                $stmt = getDB()->prepare('SELECT * FROM users WHERE email = ?');
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', time() + 3600);
                    getDB()->prepare('UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE id = ?')
                        ->execute([$token, $expires, $user['id']]);

                    Log::info("Password reset link for {$user['email']}: " . SITE_URL . '/pages/reset_password.php?token=' . urlencode($token));
                }

                setFlash('success', 'If an account exists, a password reset link has been sent to your email.');

                return redirect(SITE_URL . '/pages/login.php');
            }
        }

        return view('auth.forgot_password', compact('pageTitle', 'error', 'success'));
    }

    public function resetPassword(Request $request)
    {
        if (isLoggedIn()) {
            return redirect(SITE_URL . '/');
        }

        $pageTitle = 'Set New Password';
        $error = '';
        $success = '';
        $token = $request->query('token', $request->input('token', ''));

        if (! $token) {
            setFlash('error', 'Invalid or missing password reset token.');

            return redirect(SITE_URL . '/pages/login.php');
        }

        $stmt = getDB()->prepare('SELECT * FROM users WHERE reset_token = ? AND reset_expires_at > NOW()');
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (! $user) {
            setFlash('error', 'Invalid or expired password reset token.');

            return redirect(SITE_URL . '/pages/login.php');
        }

        if ($request->isMethod('post')) {
            $password = $request->input('password', '');
            $confirm = $request->input('confirm_password', '');

            if (! $password || ! $confirm) {
                $error = 'Please fill in all fields.';
            } elseif ($password !== $confirm) {
                $error = 'Passwords do not match.';
            } elseif (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters.';
            } else {
                getDB()->prepare('UPDATE users SET password = ?, reset_token = NULL, reset_expires_at = NULL WHERE id = ?')
                    ->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
                setFlash('success', 'Your password has been reset successfully. You can now log in.');

                return redirect(SITE_URL . '/pages/login.php');
            }
        }

        return view('auth.reset_password', compact('pageTitle', 'error', 'success', 'token'));
    }

    public function logout(Request $request)
    {
        logoutUser();
        $request->session()->regenerateToken();
        setFlash('success', 'You have been logged out.');

        return redirect(SITE_URL . '/pages/login.php');
    }
}