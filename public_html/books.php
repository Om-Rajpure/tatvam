<?php
session_start();
include "db_conn.php";
include "php/func-book.php";
include "php/func-author.php";
include "php/func-category.php";

// Type filter
$type_filter = null;
if (isset($_GET['type']) && in_array($_GET['type'], ['book', 'research_paper'])) {
    $type_filter = $_GET['type'];
}

if ($type_filter && function_exists('get_books_by_type')) {
    $books = get_books_by_type($conn, $type_filter);
} else {
    $books = get_all_books($conn);
    if ($type_filter && is_array($books)) {
        $books = array_values(array_filter($books, fn($b) => ($b['content_type'] ?? 'book') === $type_filter));
        if (empty($books)) $books = 0;
    }
}

$authors    = get_all_author($conn);
$categories = get_all_categories($conn);

$cart_count = 0;
if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
    include "php/func-cart.php";
    $cart_count = get_cart_count($conn, $_SESSION['user_id']);
}

$page_title = $type_filter === 'research_paper'
    ? 'Research Papers'
    : ($type_filter === 'book' ? 'Books' : 'All Publications');

$current_page = 'books.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> — Tatvam Publication</title>
    <meta name="description" content="Browse our curated collection of academic <?= htmlspecialchars($page_title) ?> at Tatvam Publication.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "php/navbar.php"; ?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($page_title) ?></li>
            </ol>
        </nav>
        <h1><?= htmlspecialchars($page_title) ?></h1>
        <p>
            <?= $type_filter === 'research_paper'
                ? 'Peer-reviewed academic research papers from verified authors'
                : ($type_filter === 'book' ? 'Academic books across all disciplines' : 'Books and research papers from our catalogue') ?>
        </p>
    </div>
</section>

<!-- Content Type Filter -->
<section class="py-3 bg-white border-bottom">
    <div class="container">
        <div class="d-flex justify-content-center content-filter">
            <div class="btn-group" role="group" aria-label="Content type filter">
                <a href="books.php"
                   class="btn <?= !$type_filter ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <i class="bi bi-grid"></i> All
                </a>
                <a href="books.php?type=book"
                   class="btn <?= $type_filter === 'book' ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <i class="bi bi-book"></i> Books
                </a>
                <a href="books.php?type=research_paper"
                   class="btn <?= $type_filter === 'research_paper' ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <i class="bi bi-journal-text"></i> Research Papers
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Books Grid -->
<section class="py-5">
    <div class="container">
        <?php if ($books == 0 || (is_array($books) && count($books) === 0)): ?>
            <div class="empty-state">
                <i class="bi bi-inbox empty-icon"></i>
                <h4>No <?= $type_filter === 'research_paper' ? 'research papers' : 'publications' ?> available yet.</h4>
                <p>Check back soon for new additions.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($books as $book):
                    $is_paper = ($book['content_type'] ?? 'book') === 'research_paper';
                ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="book-detail.php?id=<?= $book['id'] ?>" style="text-decoration:none; display:block; height:100%;">
                        <div class="book-card">
                            <div class="book-image">
                                <img src="uploads/cover/<?= htmlspecialchars($book['cover']) ?>"
                                     alt="<?= htmlspecialchars($book['title']) ?>"
                                     loading="lazy"
                                     onerror="this.src='<?= $is_paper ? 'img/default-paper.png' : 'img/default-book.png' ?>'">
                            </div>
                            <div class="book-info">
                                <span class="book-category <?= $is_paper ? 'research-paper-badge' : '' ?>">
                                    <?php if ($is_paper): ?>
                                        Research Paper
                                    <?php else:
                                        $cat_name = '';
                                        if (is_array($categories)) {
                                            foreach ($categories as $cat) {
                                                if ($cat['id'] == $book['category_id']) { $cat_name = $cat['name']; break; }
                                            }
                                        }
                                        echo htmlspecialchars($cat_name ?: 'General');
                                    endif; ?>
                                </span>
                                <h5 class="book-title"><?= htmlspecialchars($book['title']) ?></h5>
                                <p class="book-author">
                                    <i class="bi bi-person"></i>
                                    <?php
                                    if (is_array($authors)) {
                                        foreach ($authors as $a) {
                                            if ($a['id'] == $book['author_id']) { echo htmlspecialchars($a['name']); break; }
                                        }
                                    }
                                    ?>
                                </p>
                                <div class="book-footer">
                                    <span class="book-price">
                                        <?= $book['price'] > 0 ? '₹' . number_format($book['price'], 2) : 'Free' ?>
                                    </span>
                                    <?php
                                    if ($book['price'] > 0) {
                                        if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
                                            if (!function_exists('is_in_cart')) { include_once "php/func-cart.php"; }
                                            if (is_in_cart($conn, $_SESSION['user_id'], $book['id'])) { ?>
                                                <a href="cart.php" class="btn btn-sm" style="background:var(--bg-muted); color:var(--text-secondary); font-size:11px;" onclick="event.stopPropagation();">In Cart</a>
                                            <?php } else { ?>
                                                <a href="php/add-to-cart.php?book_id=<?= $book['id'] ?>" class="btn btn-primary btn-sm" style="font-size:11px;" onclick="event.stopPropagation();">
                                                    <i class="bi bi-cart-plus"></i>
                                                </a>
                                            <?php }
                                        } else { ?>
                                            <a href="user-login.php" class="btn btn-primary btn-sm" style="font-size:11px;" onclick="event.stopPropagation();">Buy</a>
                                        <?php }
                                    } else { ?>
                                        <span style="font-size:11.5px; color:var(--success); font-weight:600;">Free Access</span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include "php/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
