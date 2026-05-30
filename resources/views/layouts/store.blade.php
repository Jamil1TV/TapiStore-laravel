<?php
/**
 * HTML Header Include
 * Contains the <head> section with meta tags, CSS links, and page title.
 * Usage: $pageTitle = 'Page Name'; include 'includes/header.php';
 */
if (!isset($pageTitle)) $pageTitle = 'TAPI Store';
$fullTitle = $pageTitle . ' | ' . SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo e($pageDescription ?? 'TAPI Store - Your premium online shopping destination for electronics, fashion, home & living, and more.'); ?>">
    <title><?php echo e($fullTitle); ?></title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    
    <?php if (isset($extraCSS)): ?>
        <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/<?php echo $extraCSS; ?>">
    <?php endif; ?>
</head>
<body>

<?php
/**
 * Navigation Bar Include
 * Dynamic navbar with cart count, login/logout, and mobile hamburger menu.
 */
$cartCount = getCartCount();
$currentUser = getCurrentUser();
?>

<!-- Flash Messages -->
<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
<div class="flash-message flash-<?php echo e($flash['type']); ?>" id="flashMessage">
    <div class="container">
        <span><?php echo e($flash['message']); ?></span>
        <button class="flash-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
    </div>
</div>
<?php endif; ?>

<!-- Top Bar -->
<div class="top-bar">
    <div class="container">
        <div class="top-bar-left">
            <span>&#9742; +1 (234) 567-890</span>
            <span>&#9993; support@tapistore.com</span>
        </div>
        <div class="top-bar-right">
            <?php if ($currentUser): ?>
                <span>Welcome, <?php echo e($currentUser['full_name']); ?></span>
                <?php if (isAdmin()): ?>
                    <a href="<?php echo SITE_URL; ?>/admin/">Admin Panel</a>
                <?php endif; ?>
            <?php else: ?>
                <span>Free shipping on orders over $50!</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main Navigation -->
<nav class="navbar" id="mainNav">
    <div class="container">
        <a href="<?php echo SITE_URL; ?>/" class="nav-logo">
            <span class="logo-icon">&#9670;</span>
            <span class="logo-text">TAPI<span class="logo-accent">Store</span></span>
        </a>

        <!-- Search Bar (Desktop) -->
        <form class="nav-search" action="<?php echo SITE_URL; ?>/pages/search.php" method="GET">
            <input type="text" name="q" placeholder="Search products..." value="<?php echo e($_GET['q'] ?? ''); ?>" autocomplete="off">
            <button type="submit" class="search-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            </button>
        </form>

        <!-- Nav Actions -->
        <div class="nav-actions">
            <?php if ($currentUser): ?>
                <a href="<?php echo SITE_URL; ?>/pages/wishlist.php" class="nav-icon-link" title="Wishlist">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </a>
                <a href="<?php echo SITE_URL; ?>/pages/cart.php" class="nav-icon-link cart-link" title="Cart">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    <?php if ($cartCount > 0): ?>
                        <span class="cart-badge" id="cartBadge"><?php echo $cartCount; ?></span>
                    <?php endif; ?>
                </a>
                <div class="nav-dropdown">
                    <button class="nav-icon-link dropdown-toggle" title="Account">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </button>
                    <div class="dropdown-menu">
                        <a href="<?php echo SITE_URL; ?>/pages/profile.php">My Profile</a>
                        <a href="<?php echo SITE_URL; ?>/pages/orders.php">Order History</a>
                        <a href="<?php echo SITE_URL; ?>/pages/wishlist.php">Wishlist</a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo SITE_URL; ?>/pages/logout.php" class="dropdown-logout">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo SITE_URL; ?>/pages/login.php" class="btn btn-outline-light btn-sm">Login</a>
                <a href="<?php echo SITE_URL; ?>/pages/register.php" class="btn btn-accent btn-sm">Register</a>
            <?php endif; ?>
        </div>

        <!-- Hamburger Menu (Mobile) -->
        <button class="hamburger" id="hamburgerBtn" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <form class="mobile-search" action="<?php echo SITE_URL; ?>/pages/search.php" method="GET">
            <input type="text" name="q" placeholder="Search products...">
            <button type="submit">Search</button>
        </form>
        <a href="<?php echo SITE_URL; ?>/">Home</a>
        <a href="<?php echo SITE_URL; ?>/pages/products.php">Products</a>
        <a href="<?php echo SITE_URL; ?>/pages/contact.php">Contact</a>
        <?php if ($currentUser): ?>
            <a href="<?php echo SITE_URL; ?>/pages/cart.php">Cart (<?php echo $cartCount; ?>)</a>
            <a href="<?php echo SITE_URL; ?>/pages/wishlist.php">Wishlist</a>
            <a href="<?php echo SITE_URL; ?>/pages/profile.php">Profile</a>
            <a href="<?php echo SITE_URL; ?>/pages/orders.php">Orders</a>
            <a href="<?php echo SITE_URL; ?>/pages/logout.php" class="mobile-logout">Logout</a>
        <?php else: ?>
            <a href="<?php echo SITE_URL; ?>/pages/login.php">Login</a>
            <a href="<?php echo SITE_URL; ?>/pages/register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>

<!-- Desktop Nav Links Bar -->
<div class="nav-links-bar">
    <div class="container">
        <a href="<?php echo SITE_URL; ?>/" class="<?php echo (!isset($activePage) || $activePage === 'home') ? 'active' : ''; ?>">Home</a>
        <a href="<?php echo SITE_URL; ?>/pages/products.php" class="<?php echo (isset($activePage) && $activePage === 'products') ? 'active' : ''; ?>">All Products</a>
        <?php
        // Fetch categories for nav
        $db = getDB();
        $navCats = $db->query("SELECT name, slug FROM categories ORDER BY name LIMIT 6")->fetchAll();
        foreach ($navCats as $cat): ?>
            <a href="<?php echo SITE_URL; ?>/pages/products.php?category=<?php echo e($cat['slug']); ?>"><?php echo e($cat['name']); ?></a>
        <?php endforeach; ?>
        <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="<?php echo (isset($activePage) && $activePage === 'contact') ? 'active' : ''; ?>">Contact</a>
    </div>
</div>

<main class="main-content">

@yield('content')
</main>

<!-- Footer -->
<footer class="site-footer">
    <div class="footer-wave">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="currentColor"></path>
        </svg>
    </div>
    <div class="container">
        <div class="footer-grid">
            <!-- About Column -->
            <div class="footer-col">
                <div class="footer-logo">
                    <span class="logo-icon">&#9670;</span>
                    <span class="logo-text">TAPI<span class="logo-accent">Store</span></span>
                </div>
                <p class="footer-about">Your premium destination for quality products. We deliver excellence with every order, ensuring the best shopping experience.</p>
                <div class="social-links">
                    <a href="#" class="social-link" title="Facebook">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    <a href="#" class="social-link" title="Twitter">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                    </a>
                    <a href="#" class="social-link" title="Instagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                    <a href="#" class="social-link" title="YouTube">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19.1c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02" fill="#fff"/></svg>
                    </a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="<?php echo SITE_URL; ?>/">Home</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/pages/products.php">All Products</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/pages/cart.php">Shopping Cart</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/pages/contact.php">Contact Us</a></li>
                </ul>
            </div>

            <!-- My Account -->
            <div class="footer-col">
                <h3>My Account</h3>
                <ul>
                    <li><a href="<?php echo SITE_URL; ?>/pages/profile.php">My Profile</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/pages/orders.php">Order History</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/pages/wishlist.php">Wishlist</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/pages/login.php">Login / Register</a></li>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div class="footer-col">
                <h3>Contact Info</h3>
                <ul class="footer-contact">
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        123 Commerce Street, Business City, BC 10001
                    </li>
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        +1 (234) 567-890
                    </li>
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        support@tapistore.com
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Shipping Info</a>
            </div>
        </div>
    </div>
</footer>

<!-- Main JavaScript -->
<script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
<?php if (isset($extraJS)): ?>
    <script src="<?php echo SITE_URL; ?>/assets/js/<?php echo $extraJS; ?>"></script>
<?php endif; ?>
</body>
</html>
