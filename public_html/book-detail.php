<?php
session_start();
if (!isset($_GET['id'])) {
    header("Location: books.php");
    exit;
}
$book_id = intval($_GET['id']);
include "db_conn.php";
include "php/func-book.php";
include "php/func-author.php";
include "php/func-category.php";
include "php/func-cart.php";

$book = get_book($conn, $book_id);
if (!$book) {
    header("Location: books.php?error=" . urlencode('Book not found.'));
    exit;
}

$author = get_author($conn, $book['author_id']);
$categories = get_all_categories($conn);

// Get category name
$category_name = '';
if (is_array($categories)) {
    foreach ($categories as $cat) {
        if ($cat['id'] == $book['category_id']) {
            $category_name = $cat['name'];
            break;
        }
    }
}

$cart_count = 0;
$in_cart = false;
$purchased = false;
if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
    $cart_count = get_cart_count($conn, $_SESSION['user_id']);
    $in_cart = is_in_cart($conn, $_SESSION['user_id'], $book_id);
    $purchased = has_purchased($conn, $_SESSION['user_id'], $book_id);
}

$is_free = ($book['price'] == 0);
$is_admin = (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin');
$content_type = $book['content_type'] ?? 'book';
$has_preview = !empty($book['preview_file']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=htmlspecialchars($book['title'])?> - Tatvam Publication</title>
    <meta name="description" content="<?=htmlspecialchars(substr($book['description'] ?? '', 0, 160))?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include "php/navbar.php"; ?>

<!-- Breadcrumb -->
<section class="py-3 bg-light border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="books.php"><?=$content_type == 'research_paper' ? 'Research Papers' : 'Books'?></a></li>
                <li class="breadcrumb-item active"><?=htmlspecialchars($book['title'])?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Main Detail Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Left: Cover Image -->
            <div class="col-lg-4 col-md-5">
                <img src="uploads/cover/<?=htmlspecialchars($book['cover'])?>" 
                     alt="<?=htmlspecialchars($book['title'])?>" 
                     class="detail-cover img-fluid rounded shadow-sm"
                     onerror="this.src='img/default-book.png'">
            </div>

            <!-- Right: Details -->
            <div class="col-lg-8 col-md-7">
                <!-- Content type badge -->
                <?php if ($content_type == 'research_paper'): ?>
                <span class="book-category research-paper-badge mb-2">Research Paper</span>
                <?php else: ?>
                <span class="book-category mb-2"><?=htmlspecialchars($category_name)?></span>
                <?php endif; ?>

                <!-- Title -->
                <h1 class="fw-bold mt-2 mb-2" style="font-size: 2rem; line-height: 1.3;"><?=htmlspecialchars($book['title'])?></h1>

                <!-- Author -->
                <p class="book-author mb-3">
                    <i class="bi bi-person-fill"></i>
                    <?php if ($author && $author !== 0): ?>
                        <a href="author.php?id=<?=$author['id']?>" class="text-decoration-none text-primary fw-medium"><?=htmlspecialchars($author['name'])?></a>
                    <?php else: ?>
                        <span class="text-muted">Unknown Author</span>
                    <?php endif; ?>
                </p>

                <!-- Meta table -->
                <table class="table detail-meta-table mb-4">
                    <tr>
                        <td class="text-muted" style="width:120px;">Category</td>
                        <td class="fw-medium"><?=htmlspecialchars($category_name ?: 'N/A')?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Format</td>
                        <td class="fw-medium"><?=htmlspecialchars($book['format'] ?? 'eBook')?></td>
                    </tr>
                    <?php if ($content_type == 'book' && !empty($book['isbn'])): ?>
                    <tr>
                        <td class="text-muted">ISBN</td>
                        <td class="fw-medium"><?=htmlspecialchars($book['isbn'])?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($content_type == 'research_paper' && !empty($book['doi'])): ?>
                    <tr>
                        <td class="text-muted">DOI</td>
                        <td class="fw-medium"><?=htmlspecialchars($book['doi'])?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($book['pages'])): ?>
                    <tr>
                        <td class="text-muted">Pages</td>
                        <td class="fw-medium"><?=intval($book['pages'])?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="text-muted align-middle">Price</td>
                        <td class="book-price fs-4 fw-bold text-primary"><?=$is_free ? 'Free' : '₹' . number_format($book['price'], 2)?></td>
                    </tr>
                </table>

                <!-- Download / Buy actions -->
                <div class="download-actions d-flex flex-wrap gap-2 mt-4">
                    <?php if ($is_free): ?>
                        <a href="download.php?book_id=<?=$book_id?>&type=full" class="btn btn-success btn-lg">
                            <i class="bi bi-download"></i> Download Free
                        </a>
                    <?php elseif ($purchased || $is_admin): ?>
                        <a href="download.php?book_id=<?=$book_id?>&type=full" class="btn btn-success btn-lg">
                            <i class="bi bi-download"></i> Download Full PDF
                        </a>
                        <?php if ($has_preview): ?>
                        <a href="download.php?book_id=<?=$book_id?>&type=preview" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-eye"></i> View Preview
                        </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($has_preview): ?>
                        <a href="download.php?book_id=<?=$book_id?>&type=preview" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-eye"></i> Download Preview
                        </a>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'customer'): ?>
                            <?php if ($in_cart): ?>
                                <a href="cart.php" class="btn btn-secondary btn-lg">
                                    <i class="bi bi-cart-check"></i> Already in Cart
                                </a>
                            <?php else: ?>
                                <a href="php/add-to-cart.php?book_id=<?=$book_id?>&redirect=book-detail.php?id=<?=$book_id?>" class="btn btn-primary btn-lg">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="user-login.php?redirect=book-detail.php?id=<?=$book_id?>" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> Login to Purchase
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Alert for GET messages -->
                <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3">
                    <i class="bi bi-exclamation-triangle"></i> <?=htmlspecialchars($_GET['error'])?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Description Section -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-9">
                <h5 class="detail-section-title fw-bold mb-3"><i class="bi bi-journal-text text-primary"></i> About This <?=$content_type == 'research_paper' ? 'Research Paper' : 'Book'?></h5>
                <p class="text-muted" style="line-height: 1.9;"><?=nl2br(htmlspecialchars($book['description'] ?? ''))?></p>
            </div>
        </div>
    </div>
</section>

<!-- About the Author Section -->
<?php if ($author && $author !== 0): ?>
<section class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-9">
                <h5 class="detail-section-title fw-bold mb-3"><i class="bi bi-person-fill text-primary"></i> About the Author</h5>
                <div class="d-flex gap-3 align-items-start bg-light p-4 rounded">
                    <!-- Author photo or avatar -->
                    <?php if (!empty($author['photo'])): ?>
                    <img src="uploads/author_photos/<?=htmlspecialchars($author['photo'])?>" class="author-about-photo rounded-circle" style="width: 80px; height: 80px; object-fit: cover;" alt="<?=htmlspecialchars($author['name'])?>">
                    <?php else: ?>
                    <div class="author-about-avatar rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;"><i class="bi bi-person-fill"></i></div>
                    <?php endif; ?>
                    <div>
                        <h6 class="fw-bold mb-1 fs-5">
                            <a href="author.php?id=<?=$author['id']?>" class="text-decoration-none text-dark"><?=htmlspecialchars($author['name'])?></a>
                        </h6>
                        <?php if (!empty($author['qualification'])): ?>
                        <p class="text-muted small mb-2"><?=htmlspecialchars($author['qualification'])?></p>
                        <?php endif; ?>
                        <?php if (!empty($author['about'])): ?>
                        <p class="text-muted mb-2" style="line-height:1.7;">
                            <?=htmlspecialchars(substr($author['about'], 0, 300))?><?=strlen($author['about']) > 300 ? '...' : ''?>
                        </p>
                        <?php if (strlen($author['about']) > 300): ?>
                        <a href="author.php?id=<?=$author['id']?>" class="text-primary small">Read more about <?=htmlspecialchars($author['name'])?> <i class="bi bi-arrow-right"></i></a>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include "php/footer.php"; ?>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
