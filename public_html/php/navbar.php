<?php
/**
 * TATVAM PUBLICATION — SHARED NAVBAR COMPONENT
 * Include this at the top of every public-facing page.
 *
 * Requires:
 *   - session_start() already called
 *   - $conn available (db_conn.php included)
 *   - $cart_count variable set (or 0 by default)
 *   - $current_page variable set to current filename (e.g. 'books.php')
 *
 * REQ-18: Professional hover effects & active states
 * REQ-19: Publish CTA visible to all users (logged-in or not)
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
function nav_active($page, $current) {
    return ($page === $current) ? ' active' : '';
}

// Publish URL: logged-out users get redirected to login first, which then returns them
$publish_url = (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer')
    ? 'submit-book.php'
    : 'user-login.php?redirect=submit-book.php';
?>

<!-- ====== TOP BAR ====== -->
<div class="top-bar">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="top-info d-flex align-items-center gap-3">
                <span><i class="bi bi-envelope"></i> info@tatvampublication.com</span>
                <span class="d-none d-sm-inline"><i class="bi bi-telephone"></i> +91 99709 99999</span>
            </div>
            <div class="top-links d-flex align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer'): ?>
                        <a href="my-book-requests.php"><i class="bi bi-file-earmark-text"></i> My Submissions</a>
                        <a href="my-orders.php" class="ms-2 d-none d-md-inline"><i class="bi bi-bag-check"></i> Orders</a>
                        <a href="user-profile.php" class="ms-2"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['user_name'] ?? 'Profile') ?></a>
                        <a href="php/user-logout.php" class="ms-2"><i class="bi bi-box-arrow-right"></i> Logout</a>
                    <?php else: ?>
                        <a href="admin.php"><i class="bi bi-speedometer2"></i> Admin Dashboard</a>
                        <a href="php/user-logout.php" class="ms-2"><i class="bi bi-box-arrow-right"></i> Logout</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="user-login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                    <a href="register.php" class="ms-2"><i class="bi bi-person-plus"></i> Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ====== MAIN NAVBAR ====== -->
<nav class="navbar navbar-expand-lg sticky-top shadow-sm" id="mainNav">
    <div class="container">

        <!-- Brand / Logo -->
        <a class="navbar-brand" href="index.php" aria-label="Tatvam Publication — Home">
        <?php
        $logo_fs_path = dirname(__DIR__) . '/img/logo-tatvam.png';
        if (file_exists($logo_fs_path)): ?>
                <img src="img/logo-tatvam.png" alt="Tatvam Publication" class="navbar-logo">
            <?php else: ?>
                <span class="brand-fallback">Tatvam <span>Publication</span></span>
            <?php endif; ?>
        </a>

        <!-- Mobile toggle -->
        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarMain"
                aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Primary nav links -->
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link<?= nav_active('index.php', $current_page) ?>" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= ($current_page === 'books.php' && (!isset($_GET['type']) || $_GET['type'] === 'book')) ? ' active' : '' ?>" href="books.php?type=book">Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= ($current_page === 'books.php' && isset($_GET['type']) && $_GET['type'] === 'research_paper') ? ' active' : '' ?>" href="books.php?type=research_paper">Research Papers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= nav_active('categories.php', $current_page) ?>" href="categories.php">Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= nav_active('authors.php', $current_page) ?>" href="authors.php">Authors</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= nav_active('reference.php', $current_page) ?>" href="reference.php">Reference</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= nav_active('about.php', $current_page) ?>" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?= nav_active('contact.php', $current_page) ?>" href="contact.php">Contact</a>
                </li>
                <!-- REQ-19: Publish always visible -->
                <li class="nav-item">
                    <a class="nav-link nav-publish-cta<?= nav_active('submit-book.php', $current_page) ?>" href="<?= htmlspecialchars($publish_url) ?>">
                        <i class="bi bi-upload"></i> Publish
                    </a>
                </li>
            </ul>

            <!-- Right side: cart / login -->
            <div class="d-flex align-items-center gap-2">
                <?php if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer'): ?>
                    <a href="cart.php" class="btn btn-cart position-relative" aria-label="Shopping Cart">
                        <i class="bi bi-bag"></i> Cart
                        <?php if ($cart_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:10px;">
                                <?= intval($cart_count) ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php elseif (!isset($_SESSION['user_id'])): ?>
                    <a href="user-login.php" class="btn btn-outline-primary btn-sm" style="font-size:13px; padding: 6px 16px;">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                    <a href="register.php" class="btn btn-primary btn-sm" style="font-size:13px; padding: 6px 16px;">
                        Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
