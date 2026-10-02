<?php 
session_start();
include "db_conn.php";
include "php/func-book.php";
include "php/func-author.php";
include "php/func-category.php";

if (!isset($_GET['id']) || intval($_GET['id']) <= 0) {
    header("Location: categories.php");
    exit;
}

$id = intval($_GET['id']);
$current_category = get_category($conn, $id);

if (!$current_category) {
    header("Location: categories.php");
    exit;
}

$books = get_books_by_category($conn, $id);
$authors = get_all_author($conn);
$categories = get_all_categories_with_count($conn);

$cart_count = 0;
if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
    include_once "php/func-cart.php";
    $cart_count = get_cart_count($conn, $_SESSION['user_id']);
}

$current_page = 'categories.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($current_category['name']) ?> — Tatvam Publication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .book-card-wrapper {
            position: relative;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .book-card-link-title {
            color: var(--text-primary);
            text-decoration: none;
            transition: color var(--transition-fast);
        }
        .book-card-link-title:hover {
            color: var(--secondary);
        }
        .action-button-layer {
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body>

<?php include "php/navbar.php"; ?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="categories.php">Categories</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($current_category['name']) ?></li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <div>
                <a href="categories.php" class="text-muted small text-decoration-none d-inline-flex align-items-center gap-1 mb-2">
                    <i class="bi bi-arrow-left"></i> Back to all categories
                </a>
                <h1 class="mb-1"><?= htmlspecialchars($current_category['name']) ?></h1>
                <p class="text-secondary">Browse academic books and research papers published in <?= htmlspecialchars($current_category['name']) ?></p>
            </div>
            <div class="text-muted small">
                <strong><?= is_array($books) ? count($books) : 0 ?></strong> <?= is_array($books) && count($books) === 1 ? 'publication' : 'publications' ?>
            </div>
        </div>
    </div>
</section>

<!-- Publications Grid -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar: Other Categories -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="card border p-3 rounded-3 shadow-none bg-white">
                    <h6 class="fw-bold mb-3 text-uppercase text-muted" style="font-size:12px; letter-spacing:0.05em;">
                        Academic Categories
                    </h6>
                    <div class="list-group list-group-flush">
                        <?php if (is_array($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <a href="category.php?id=<?= $cat['id'] ?>"
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-2 border-0 rounded-2 <?= $cat['id'] == $id ? 'bg-primary text-white fw-bold' : '' ?>"
                                   style="font-size: 13.5px;">
                                    <span><?= htmlspecialchars($cat['name']) ?></span>
                                    <span class="badge rounded-pill <?= $cat['id'] == $id ? 'bg-light text-dark' : 'bg-light text-muted' ?>" style="font-size:11px;">
                                        <?= intval($cat['book_count'] ?? 0) ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Main Publications Grid -->
            <div class="col-12 col-lg-9">
                <?php if ($books == 0 || (is_array($books) && count($books) === 0)): ?>
                    <div class="empty-state bg-white border rounded-3 p-5 text-center">
                        <i class="bi bi-journal-x empty-icon"></i>
                        <h4 class="mt-3">No publications in this category yet</h4>
                        <p class="text-muted">New scholarly books and research papers for <?= htmlspecialchars($current_category['name']) ?> are currently being peer-reviewed.</p>
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <a href="categories.php" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-grid me-1"></i> All Categories
                            </a>
                            <a href="books.php" class="btn btn-primary btn-sm">
                                <i class="bi bi-book me-1"></i> All Books
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($books as $book):
                            $is_paper = ($book['content_type'] ?? 'book') === 'research_paper';
                            $book_author = '';
                            if (is_array($authors)) {
                                foreach ($authors as $a) {
                                    if ($a['id'] == $book['author_id']) {
                                        $book_author = $a['name'];
                                        break;
                                    }
                                }
                            }
                        ?>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="book-card-wrapper">
                                <div class="book-card">
                                    <div class="book-image">
                                        <a href="book-detail.php?id=<?= $book['id'] ?>" aria-label="<?= htmlspecialchars($book['title']) ?>">
                                            <img src="uploads/cover/<?= htmlspecialchars($book['cover']) ?>"
                                                 alt="<?= htmlspecialchars($book['title']) ?>"
                                                 loading="lazy"
                                                 onerror="this.src='<?= $is_paper ? 'img/default-paper.png' : 'img/default-book.png' ?>'">
                                        </a>
                                        <div class="book-overlay">
                                            <a href="book-detail.php?id=<?= $book['id'] ?>" class="btn btn-light btn-sm fw-semibold">
                                                <i class="bi bi-eye"></i> View Details
                                            </a>
                                        </div>
                                    </div>
                                    <div class="book-info d-flex flex-column justify-content-between">
                                        <div>
                                            <span class="book-category <?= $is_paper ? 'research-paper-badge' : '' ?>">
                                                <?= $is_paper ? 'Research Paper' : htmlspecialchars($current_category['name']) ?>
                                            </span>
                                            <h5 class="book-title">
                                                <a href="book-detail.php?id=<?= $book['id'] ?>" class="book-card-link-title">
                                                    <?= htmlspecialchars($book['title']) ?>
                                                </a>
                                            </h5>
                                            <p class="book-author mb-3">
                                                <i class="bi bi-person me-1"></i>
                                                <?php if (!empty($book['author_id'])): ?>
                                                    <a href="author.php?id=<?= $book['author_id'] ?>" class="text-secondary text-decoration-none action-button-layer">
                                                        <?= htmlspecialchars($book_author ?: 'Tatvam Editorial') ?>
                                                    </a>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($book_author ?: 'Tatvam Editorial') ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="book-footer d-flex align-items-center justify-content-between pt-2 mt-auto border-top">
                                            <div>
                                                <span class="book-price fw-bold">
                                                    <?= $book['price'] > 0 ? '₹' . number_format($book['price'], 2) : 'Free' ?>
                                                </span>
                                            </div>
                                            <div class="action-button-layer">
                                                <?php if ($book['price'] > 0): ?>
                                                    <?php if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer'): ?>
                                                        <?php 
                                                        if (!function_exists('is_in_cart')) { include_once "php/func-cart.php"; }
                                                        if (is_in_cart($conn, $_SESSION['user_id'], $book['id'])): ?>
                                                            <a href="cart.php" class="btn btn-sm btn-outline-secondary" style="font-size:12px;">
                                                                <i class="bi bi-check2"></i> In Cart
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="php/add-to-cart.php?book_id=<?= $book['id'] ?>" class="btn btn-primary btn-sm" style="font-size:12px;" title="Add to Cart">
                                                                <i class="bi bi-cart-plus me-1"></i> Add
                                                            </a>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <a href="user-login.php" class="btn btn-primary btn-sm" style="font-size:12px;">
                                                            <i class="bi bi-bag me-1"></i> Buy
                                                        </a>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <a href="book-detail.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-outline-success" style="font-size:11.5px; font-weight:600;">
                                                        <i class="bi bi-unlock me-1"></i> Free Access
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include "php/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
