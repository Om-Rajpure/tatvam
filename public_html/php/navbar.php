<?php
/**
 * TATVAM PUBLICATION — SHARED NAVBAR COMPONENT
 * Enhanced Modern Academic Header with Responsive Navigation & Account Controls.
 */

// Determine cart count if not already set
if (!isset($cart_count)) {
    $cart_count = 0;
    if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
        if (!function_exists('get_cart_count')) {
            include_once __DIR__ . '/func-cart.php';
        }
        if (isset($conn)) {
            $cart_count = get_cart_count($conn, $_SESSION['user_id']);
        }
    }
}

// Determine current page for active state
if (!isset($current_page)) {
    $current_page = basename($_SERVER['PHP_SELF'] ?? '');
}

// Helper: output 'active' class if page matches
if (!function_exists('nav_active')) {
    function nav_active($page, $current) {
        return ($page === $current) ? ' active' : '';
    }
}

// Publish URL: logged-out users get redirected to login first, which then returns them
$publish_url = (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer')
    ? 'submit-book.php'
    : 'user-login.php?redirect=submit-book.php';
?>

<!-- ====== TOP NOTIFICATION / CONTACT BAR ====== -->
<div class="top-bar">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="top-info d-flex align-items-center gap-3">
                <a href="mailto:info@tatvampublication.com" class="top-bar-link">
                    <i class="bi bi-envelope-fill me-1 text-warning"></i> info@tatvampublication.com
                </a>
                <span class="d-none d-md-inline text-white-50">|</span>
                <a href="tel:+919970999999" class="top-bar-link d-none d-sm-inline">
                    <i class="bi bi-telephone-fill me-1 text-warning"></i> +91 99709 99999
                </a>
                <span class="d-none d-lg-inline-block badge bg-white bg-opacity-10 text-white-50 border border-white border-opacity-10 rounded-pill px-2 py-1 ms-1" style="font-size: 11px; font-weight: 500;">
                    <i class="bi bi-patch-check-fill text-warning me-1"></i> Peer-Reviewed Academic Press
                </span>
            </div>
            <div class="top-links d-flex align-items-center gap-3">
                <a href="reference.php" class="top-bar-link d-none d-md-inline">
                    <i class="bi bi-journal-bookmark me-1"></i> Citation Guide
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer'): ?>
                        <span class="text-white-50 d-none d-sm-inline">|</span>
                        <a href="my-book-requests.php" class="top-bar-link d-none d-md-inline">
                            <i class="bi bi-file-earmark-text me-1"></i> Submissions
                        </a>
                        <a href="my-orders.php" class="top-bar-link d-none d-sm-inline">
                            <i class="bi bi-bag-check me-1"></i> Orders
                        </a>
                    <?php else: ?>
                        <span class="text-white-50">|</span>
                        <a href="admin.php" class="top-bar-link">
                            <i class="bi bi-speedometer2 me-1"></i> Admin Panel
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="text-white-50 d-none d-sm-inline">|</span>
                    <a href="user-login.php" class="top-bar-link">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ====== MAIN NAVIGATION BAR ====== -->
<nav class="navbar navbar-expand-xl sticky-top main-navbar shadow-sm" id="mainNav">
    <div class="container">

        <!-- Brand / Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php" aria-label="Tatvam Publication — Home">
            <?php
            $logo_fs_path = dirname(__DIR__) . '/img/logo-tatvam.png';
            if (file_exists($logo_fs_path)): ?>
                <img src="img/logo-tatvam.png" alt="Tatvam Publication" class="navbar-logo">
            <?php else: ?>
                <i class="bi bi-journal-richtext fs-2 text-primary"></i>
            <?php endif; ?>
            <div class="brand-text-lockup d-flex flex-column">
                <span class="brand-title">TATVAM</span>
                <span class="brand-subtitle">PUBLICATION</span>
            </div>
        </a>

        <!-- Mobile Toggler Button -->
        <button class="navbar-toggler border-0 shadow-none p-2" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarMain"
                aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list fs-2 text-primary"></i>
        </button>

        <!-- Navbar Menu Collapse -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Center Navigation Links -->
            <ul class="navbar-nav mx-auto mb-2 mb-xl-0 align-items-xl-center">
                <li class="nav-item">
                    <a class="nav-link<?= nav_active('index.php', $current_page) ?>" href="index.php">
                        Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= ($current_page === 'books.php' && (!isset($_GET['type']) || $_GET['type'] === 'book' || $_GET['type'] === 'all' || empty($_GET['type']))) ? ' active' : '' ?>" href="books.php?type=book">
                        Books
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= ($current_page === 'books.php' && isset($_GET['type']) && $_GET['type'] === 'research_paper') ? ' active' : '' ?>" href="books.php?type=research_paper">
                        Research Papers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= nav_active('categories.php', $current_page) || nav_active('category.php', $current_page) ?>" href="categories.php">
                        Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= nav_active('authors.php', $current_page) || nav_active('author.php', $current_page) ?>" href="authors.php">
                        Authors
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= nav_active('reference.php', $current_page) ?>" href="reference.php">
                        Reference
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= nav_active('about.php', $current_page) ?>" href="about.php">
                        About
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= nav_active('contact.php', $current_page) ?>" href="contact.php">
                        Contact
                    </a>
                </li>
            </ul>

            <!-- Right Action Bar -->
            <div class="d-flex flex-wrap align-items-center gap-2 pt-3 pt-xl-0 border-top border-xl-0 mt-3 mt-xl-0">
                
                <!-- Quick Search Modal / Page Link -->
                <a href="books.php" class="btn btn-nav-icon" title="Search Books & Papers" aria-label="Search">
                    <i class="bi bi-search"></i>
                </a>

                <!-- Publish CTA Button (Signature Warm Gold Accent) -->
                <a href="<?= htmlspecialchars($publish_url) ?>" class="btn btn-publish-cta <?= nav_active('submit-book.php', $current_page) ?>">
                    <i class="bi bi-cloud-arrow-up-fill me-1"></i> Publish
                </a>

                <!-- Shopping Cart -->
                <?php if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer'): ?>
                    <a href="cart.php" class="btn btn-nav-cart position-relative <?= nav_active('cart.php', $current_page) ?>" aria-label="Shopping Cart">
                        <i class="bi bi-bag-fill"></i>
                        <span class="d-xl-none ms-1">Cart</span>
                        <?php if ($cart_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size:10.5px;">
                                <?= intval($cart_count) ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

                <!-- User Account Dropdown / Auth Buttons -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-nav-user dropdown-toggle d-flex align-items-center gap-2" type="button" id="userMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar-circle">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <span class="user-name-label text-truncate" style="max-width: 110px;">
                                <?= htmlspecialchars($_SESSION['user_name'] ?? 'Account') ?>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-md border py-2" aria-labelledby="userMenuBtn" style="min-width: 210px; border-radius: 10px;">
                            <li class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-bold text-truncate" style="font-size: 13.5px;"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></div>
                                <div class="text-muted small text-truncate"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></div>
                            </li>
                            <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer'): ?>
                                <li>
                                    <a class="dropdown-item py-2" href="my-book-requests.php">
                                        <i class="bi bi-file-earmark-text me-2 text-primary"></i> My Submissions
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="my-orders.php">
                                        <i class="bi bi-bag-check me-2 text-primary"></i> Order History
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="user-profile.php">
                                        <i class="bi bi-person-gear me-2 text-primary"></i> Profile Settings
                                    </a>
                                </li>
                            <?php else: ?>
                                <li>
                                    <a class="dropdown-item py-2" href="admin.php">
                                        <i class="bi bi-speedometer2 me-2 text-primary"></i> Admin Dashboard
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item py-2 text-danger" href="php/user-logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="user-login.php" class="btn btn-outline-primary btn-sm px-3 py-2" style="font-size:13px; font-weight:600;">
                        Sign In
                    </a>
                    <a href="register.php" class="btn btn-primary btn-sm px-3 py-2" style="font-size:13px; font-weight:600;">
                        Register
                    </a>
                <?php endif; ?>

            </div>
        </div>
    </div>
</nav>
