<?php
session_start();
include "db_conn.php";
include "php/func-author.php";

if (function_exists('get_all_authors_with_stats')) {
    $authors = get_all_authors_with_stats($conn);
} else {
    $authors = get_all_author($conn);
}

$cart_count = 0;
if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
    include "php/func-cart.php";
    $cart_count = get_cart_count($conn, $_SESSION['user_id']);
}

$current_page = 'authors.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authors — Tatvam Publication</title>
    <meta name="description" content="Meet the researchers and scholars publishing with Tatvam Publication.">
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
                <li class="breadcrumb-item active">Authors</li>
            </ol>
        </nav>
        <h1>Our Authors</h1>
        <p>The researchers and scholars contributing to Tatvam Publication</p>
    </div>
</section>

<!-- Authors Section -->
<section class="py-5 bg-white">
    <div class="container">
        <?php if ($authors == 0 || (is_array($authors) && count($authors) === 0)): ?>
            <div class="empty-state">
                <i class="bi bi-people empty-icon"></i>
                <h4>No authors available yet.</h4>
                <p>Check back soon as our community grows.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($authors as $author): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="author.php?id=<?= $author['id'] ?>" class="author-card" style="text-decoration:none;">
                        <?php if (!empty($author['photo'])): ?>
                            <img src="uploads/author_photos/<?= htmlspecialchars($author['photo']) ?>"
                                 class="author-card-photo"
                                 alt="<?= htmlspecialchars($author['name']) ?>"
                                 loading="lazy"
                                 onerror="this.src='img/default-author.png'">
                        <?php else: ?>
                            <div class="author-avatar mx-auto mb-3">
                                <i class="bi bi-person"></i>
                            </div>
                        <?php endif; ?>

                        <h5><?= htmlspecialchars($author['name']) ?></h5>

                        <?php if (!empty($author['qualification'])): ?>
                            <p class="text-muted small mb-1"><?= htmlspecialchars($author['qualification']) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($author['designation'])): ?>
                            <p class="text-muted small mb-1"><?= htmlspecialchars($author['designation']) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($author['organization'])): ?>
                            <p class="text-muted small mb-2"><?= htmlspecialchars($author['organization']) ?></p>
                        <?php endif; ?>

                        <?php if (isset($author['book_count'])): ?>
                            <p class="small mb-2" style="color:var(--text-muted);">
                                <i class="bi bi-book"></i> <?= intval($author['book_count']) ?> Publication<?= $author['book_count'] != 1 ? 's' : '' ?>
                            </p>
                        <?php endif; ?>

                        <p class="mt-1" style="font-size:12.5px; color: var(--primary); font-weight:600;">
                            View Profile <i class="bi bi-arrow-right"></i>
                        </p>
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
