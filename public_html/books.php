<?php
session_start();
include "db_conn.php";
include "php/func-book.php";
include "php/func-author.php";
include "php/func-category.php";

// 1. Parse Filter Parameters
$type_filter     = (isset($_GET['type']) && in_array($_GET['type'], ['book', 'research_paper', 'all'])) ? $_GET['type'] : null;
$category_filter = (isset($_GET['category']) && intval($_GET['category']) > 0) ? intval($_GET['category']) : null;
$search_query    = isset($_GET['search']) ? trim($_GET['search']) : null;
$sort_by         = (isset($_GET['sort']) && in_array($_GET['sort'], ['latest', 'price_asc', 'price_desc', 'title_asc'])) ? $_GET['sort'] : 'latest';

// Treat 'all' as no type filter
$query_type = ($type_filter === 'all') ? null : $type_filter;

// 2. Fetch Filtered Books
$books = get_filtered_books($conn, $query_type, $category_filter, $search_query, $sort_by);

// 3. Fetch Metadata & Counts
$authors    = get_all_author($conn);
$categories = get_all_categories_with_count($conn);
$counts     = get_content_counts($conn);

$cart_count = 0;
if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
    include_once "php/func-cart.php";
    $cart_count = get_cart_count($conn, $_SESSION['user_id']);
}

// 4. Page Title and Metadata
$page_title = $type_filter === 'research_paper'
    ? 'Research Papers'
    : ($type_filter === 'book' ? 'Books' : 'All Publications');

$current_page = 'books.php';

// Helper to preserve other query parameters
function build_filter_url($overrides = []) {
    $params = $_GET;
    foreach ($overrides as $k => $v) {
        if ($v === null || $v === '') {
            unset($params[$k]);
        } else {
            $params[$k] = $v;
        }
    }
    $query = http_build_query($params);
    return 'books.php' . ($query ? '?' . $query : '');
}

$has_active_filters = !empty($type_filter) || !empty($category_filter) || !empty($search_query) || ($sort_by !== 'latest');
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
    <style>
        .filter-badge-count {
            display: inline-block;
            padding: 2px 7px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 20px;
            margin-left: 6px;
            background: rgba(0,0,0,0.08);
            color: inherit;
        }
        .btn-primary .filter-badge-count {
            background: rgba(255,255,255,0.25);
            color: #fff;
        }
        .active-filter-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--bg-muted);
            color: var(--text-primary);
            border: 1px solid var(--border);
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 12.5px;
            font-weight: 500;
            text-decoration: none;
            transition: all var(--transition-fast);
        }
        .active-filter-tag:hover {
            background: var(--border);
            color: var(--text-primary);
        }
        .active-filter-tag i {
            font-size: 14px;
            color: var(--text-secondary);
        }
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
        .filter-controls-bar {
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            padding: 16px 0;
        }
        .sort-select, .category-select {
            font-size: 13.5px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            padding: 7px 12px;
            color: var(--text-primary);
            background-color: var(--bg-white);
            font-weight: 500;
        }
        .sort-select:focus, .category-select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(23,50,77,0.12);
        }
        .search-input-group {
            position: relative;
        }
        .search-input-group .form-control {
            border-radius: var(--radius-sm);
            padding-left: 36px;
            font-size: 13.5px;
            border: 1px solid var(--border);
            height: 38px;
        }
        .search-input-group .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
            pointer-events: none;
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
                <li class="breadcrumb-item active"><?= htmlspecialchars($page_title) ?></li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <div>
                <h1><?= htmlspecialchars($page_title) ?></h1>
                <p>
                    <?= $type_filter === 'research_paper'
                        ? 'Peer-reviewed academic research papers and publications from verified scholars'
                        : ($type_filter === 'book' ? 'Academic textbooks, monographs, and volumes across all disciplines' : 'Discover peer-reviewed books, journals, and research papers from our catalogue') ?>
                </p>
            </div>
            <div class="text-muted small">
                Showing <strong><?= is_array($books) ? count($books) : 0 ?></strong> <?= is_array($books) && count($books) === 1 ? 'publication' : 'publications' ?>
            </div>
        </div>
    </div>
</section>

<!-- Filter and Controls Bar -->
<section class="filter-controls-bar">
    <div class="container">
        <div class="row g-3 align-items-center justify-content-between">
            <!-- Content Type Tabs -->
            <div class="col-12 col-lg-5">
                <div class="btn-group w-100 w-sm-auto" role="group" aria-label="Content type filter">
                    <a href="<?= build_filter_url(['type' => null]) ?>"
                       class="btn <?= empty($type_filter) || $type_filter === 'all' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm py-2">
                        <i class="bi bi-grid-fill me-1"></i> All
                        <span class="filter-badge-count"><?= intval($counts['total'] ?? 0) ?></span>
                    </a>
                    <a href="<?= build_filter_url(['type' => 'book']) ?>"
                       class="btn <?= $type_filter === 'book' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm py-2">
                        <i class="bi bi-book me-1"></i> Books
                        <span class="filter-badge-count"><?= intval($counts['books_count'] ?? 0) ?></span>
                    </a>
                    <a href="<?= build_filter_url(['type' => 'research_paper']) ?>"
                       class="btn <?= $type_filter === 'research_paper' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm py-2">
                        <i class="bi bi-journal-text me-1"></i> Research Papers
                        <span class="filter-badge-count"><?= intval($counts['papers_count'] ?? 0) ?></span>
                    </a>
                </div>
            </div>

            <!-- Category, Search & Sort Controls -->
            <div class="col-12 col-lg-7">
                <form method="GET" action="books.php" class="row g-2 align-items-center justify-content-lg-end">
                    <?php if ($type_filter): ?>
                        <input type="hidden" name="type" value="<?= htmlspecialchars($type_filter) ?>">
                    <?php endif; ?>

                    <!-- Category Select -->
                    <div class="col-12 col-sm-4 col-md-4">
                        <select name="category" class="form-select category-select" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php if (is_array($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $category_filter == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?> (<?= intval($cat['book_count'] ?? 0) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="col-12 col-sm-5 col-md-5">
                        <div class="search-input-group">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" name="search" class="form-control" placeholder="Search by title, author..." value="<?= htmlspecialchars($search_query ?? '') ?>">
                        </div>
                    </div>

                    <!-- Sort Select -->
                    <div class="col-12 col-sm-3 col-md-3">
                        <select name="sort" class="form-select sort-select" onchange="this.form.submit()">
                            <option value="latest" <?= $sort_by === 'latest' ? 'selected' : '' ?>>Latest</option>
                            <option value="price_asc" <?= $sort_by === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_desc" <?= $sort_by === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="title_asc" <?= $sort_by === 'title_asc' ? 'selected' : '' ?>>Title: A to Z</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Active Filter Chips -->
        <?php if ($has_active_filters): ?>
            <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-3 border-top">
                <span class="text-muted small me-1">Active filters:</span>
                
                <?php if (!empty($type_filter) && $type_filter !== 'all'): ?>
                    <a href="<?= build_filter_url(['type' => null]) ?>" class="active-filter-tag">
                        <span>Type: <?= $type_filter === 'book' ? 'Books' : 'Research Papers' ?></span>
                        <i class="bi bi-x"></i>
                    </a>
                <?php endif; ?>

                <?php if (!empty($category_filter)): 
                    $active_cat_name = 'Category';
                    if (is_array($categories)) {
                        foreach ($categories as $cat) {
                            if ($cat['id'] == $category_filter) {
                                $active_cat_name = $cat['name'];
                                break;
                            }
                        }
                    }
                ?>
                    <a href="<?= build_filter_url(['category' => null]) ?>" class="active-filter-tag">
                        <span>Category: <?= htmlspecialchars($active_cat_name) ?></span>
                        <i class="bi bi-x"></i>
                    </a>
                <?php endif; ?>

                <?php if (!empty($search_query)): ?>
                    <a href="<?= build_filter_url(['search' => null]) ?>" class="active-filter-tag">
                        <span>Search: "<?= htmlspecialchars($search_query) ?>"</span>
                        <i class="bi bi-x"></i>
                    </a>
                <?php endif; ?>

                <?php if ($sort_by !== 'latest'): ?>
                    <a href="<?= build_filter_url(['sort' => null]) ?>" class="active-filter-tag">
                        <span>Sort: <?= $sort_by === 'price_asc' ? 'Price Low-High' : ($sort_by === 'price_desc' ? 'Price High-Low' : 'Title A-Z') ?></span>
                        <i class="bi bi-x"></i>
                    </a>
                <?php endif; ?>

                <a href="books.php" class="text-danger small ms-2 text-decoration-none fw-semibold">
                    <i class="bi bi-trash3 me-1"></i>Reset all
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Books Grid Section -->
<section class="py-5">
    <div class="container">
        <?php if ($books == 0 || (is_array($books) && count($books) === 0)): ?>
            <div class="empty-state">
                <i class="bi bi-journal-x empty-icon"></i>
                <h4 class="mt-3">No publications found</h4>
                <p class="text-muted">We couldn't find any items matching your selected criteria.</p>
                <?php if ($has_active_filters): ?>
                    <a href="books.php" class="btn btn-outline-primary btn-sm mt-3">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Clear Filters
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($books as $book):
                    $is_paper = ($book['content_type'] ?? 'book') === 'research_paper';
                    $book_category = $book['category_name'] ?? '';
                    if (empty($book_category) && is_array($categories)) {
                        foreach ($categories as $cat) {
                            if ($cat['id'] == $book['category_id']) {
                                $book_category = $cat['name'];
                                break;
                            }
                        }
                    }
                    $book_author = $book['author_name'] ?? '';
                    if (empty($book_author) && is_array($authors)) {
                        foreach ($authors as $a) {
                            if ($a['id'] == $book['author_id']) {
                                $book_author = $a['name'];
                                break;
                            }
                        }
                    }
                ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
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
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="book-category <?= $is_paper ? 'research-paper-badge' : '' ?>">
                                            <?= $is_paper ? 'Research Paper' : htmlspecialchars($book_category ?: 'Academic') ?>
                                        </span>
                                    </div>
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
</section>

<?php include "php/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
