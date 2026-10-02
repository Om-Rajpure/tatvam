<?php
session_start();
include "db_conn.php";
include "php/func-book.php";
include "php/func-author.php";
include "php/func-category.php";
include "php/func-cart.php";

$books      = get_all_books($conn);
$authors    = get_all_author($conn);
$categories = get_all_categories($conn);

// Count by type
$book_count   = 0;
$paper_count  = 0;
$author_count = is_array($authors) ? count($authors) : 0;
$cat_count    = is_array($categories) ? count($categories) : 0;

if (is_array($books)) {
    foreach ($books as $b) {
        if (($b['content_type'] ?? 'book') === 'research_paper') {
            $paper_count++;
        } else {
            $book_count++;
        }
    }
}

$cart_count = 0;
if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
    $cart_count = get_cart_count($conn, $_SESSION['user_id']);
}

$current_page = 'index.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tatvam Publication — Academic Books & Research Papers</title>
    <meta name="description" content="Tatvam Publication is a trusted platform for discovering, purchasing, and publishing academic books and research papers. Browse our curated collection today.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "php/navbar.php"; ?>

<!-- ====== HERO ====== -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <span class="hero-eyebrow">
                        <i class="bi bi-patch-check-fill"></i>
                        Trusted Academic Publishing
                    </span>
                    <h1>Discover. Read.<br>Publish Your Research.</h1>
                    <p class="lead">
                        Tatvam Publication brings together researchers, authors, and readers on a single platform — with verified academic books and research papers.
                    </p>
                    <div class="hero-search">
                        <form action="search.php" method="get">
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control" name="key"
                                       placeholder="Search books, authors, research papers…"
                                       aria-label="Search">
                                <button class="btn" type="submit">
                                    <i class="bi bi-search"></i> Search
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="hero-stats">
                        <div class="hero-stat">
                            <h3><?= $book_count ?>+</h3>
                            <p>Books</p>
                        </div>
                        <div class="hero-stat">
                            <h3><?= $paper_count ?>+</h3>
                            <p>Research Papers</p>
                        </div>
                        <div class="hero-stat">
                            <h3><?= $author_count ?>+</h3>
                            <p>Authors</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="hero-image text-center" style="padding-top:20px;">
                    <img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=600&q=80" alt="Academic library with books" class="img-fluid" style="max-height:400px; object-fit:cover; width:100%;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====== ALERTS from URL params ====== -->
<?php if (isset($_GET['error']) || isset($_GET['success'])): ?>
<div class="container mt-3">
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- ====== FEATURED BOOKS ====== -->
<section class="py-5 bg-white" id="books">
    <div class="container">
        <div class="section-header text-center">
            <div class="section-label">Our Collection</div>
            <h2>Featured Books</h2>
            <p>A curated selection from our verified catalogue of academic publications</p>
        </div>

        <?php
        // Show only books (not research papers) on homepage, up to 8
        $display_books = [];
        if (is_array($books)) {
            foreach ($books as $b) {
                if (($b['content_type'] ?? 'book') === 'book') {
                    $display_books[] = $b;
                }
                if (count($display_books) >= 8) break;
            }
        }
        ?>

        <?php if (empty($display_books)): ?>
            <div class="empty-state">
                <i class="bi bi-book empty-icon"></i>
                <h4>No books available yet.</h4>
                <p>Check back soon for new publications.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($display_books as $book): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="book-detail.php?id=<?= $book['id'] ?>" style="text-decoration:none; display:block; height:100%;">
                        <div class="book-card">
                            <div class="book-image">
                                <img src="uploads/cover/<?= htmlspecialchars($book['cover']) ?>"
                                     alt="<?= htmlspecialchars($book['title']) ?>"
                                     loading="lazy"
                                     onerror="this.src='img/default-book.png'">
                            </div>
                            <div class="book-info">
                                <span class="book-category">
                                    <?php
                                    $cat_name = '';
                                    if (is_array($categories)) {
                                        foreach ($categories as $cat) {
                                            if ($cat['id'] == $book['category_id']) { $cat_name = $cat['name']; break; }
                                        }
                                    }
                                    echo htmlspecialchars($cat_name ?: 'General');
                                    ?>
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
                                    <span class="text-primary" style="font-size:12.5px; font-weight:600;">View →</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-5">
                <a href="books.php?type=book" class="btn btn-outline-primary">
                    View All Books <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ====== RESEARCH PAPERS ====== -->
<?php
$display_papers = [];
if (is_array($books)) {
    foreach ($books as $b) {
        if (($b['content_type'] ?? 'book') === 'research_paper') {
            $display_papers[] = $b;
        }
        if (count($display_papers) >= 4) break;
    }
}
if (!empty($display_papers)):
?>
<section class="py-5" style="background: var(--bg-light);" id="research">
    <div class="container">
        <div class="section-header text-center">
            <div class="section-label">Academic Research</div>
            <h2>Research Papers</h2>
            <p>Peer-reviewed academic papers across disciplines</p>
        </div>
        <div class="row g-4">
            <?php foreach ($display_papers as $paper): ?>
            <div class="col-12 col-md-6">
                <a href="book-detail.php?id=<?= $paper['id'] ?>" style="text-decoration:none; display:block;">
                    <div class="book-card d-flex" style="flex-direction:row; height:100%;">
                        <div style="width:90px; min-width:90px; overflow:hidden; background: var(--bg-muted);">
                            <img src="uploads/cover/<?= htmlspecialchars($paper['cover']) ?>"
                                 alt="<?= htmlspecialchars($paper['title']) ?>"
                                 loading="lazy"
                                 onerror="this.src='img/default-paper.png'"
                                 style="width:100%; height:100%; object-fit:cover; min-height:120px;">
                        </div>
                        <div class="book-info" style="flex:1;">
                            <span class="book-category research-paper-badge">Research Paper</span>
                            <h5 class="book-title" style="-webkit-line-clamp:2;"><?= htmlspecialchars($paper['title']) ?></h5>
                            <p class="book-author">
                                <i class="bi bi-person"></i>
                                <?php
                                if (is_array($authors)) {
                                    foreach ($authors as $a) {
                                        if ($a['id'] == $paper['author_id']) { echo htmlspecialchars($a['name']); break; }
                                    }
                                }
                                ?>
                            </p>
                            <div class="book-footer">
                                <span class="book-price">
                                    <?= $paper['price'] > 0 ? '₹' . number_format($paper['price'], 2) : 'Free' ?>
                                </span>
                                <span class="text-primary" style="font-size:12px; font-weight:600;">Read →</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="books.php?type=research_paper" class="btn btn-outline-primary">
                View All Research Papers <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ====== CATEGORIES ====== -->
<?php if ($categories && is_array($categories) && count($categories) > 0): ?>
<section class="py-5 bg-white" id="categories">
    <div class="container">
        <div class="section-header text-center">
            <div class="section-label">Browse</div>
            <h2>Explore by Category</h2>
            <p>Find publications in your domain of interest</p>
        </div>
        <div class="row g-3">
            <?php $cat_icons = ['bi-mortarboard','bi-cpu','bi-bar-chart','bi-globe','bi-heart-pulse','bi-tree','bi-calculator','bi-journal-text','bi-building','bi-gear','bi-people','bi-lightning']; $ci = 0; ?>
            <?php foreach ($categories as $category): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="category.php?id=<?= $category['id'] ?>" class="category-card">
                    <div class="category-icon">
                        <i class="bi <?= $cat_icons[$ci % count($cat_icons)] ?>"></i>
                    </div>
                    <h5><?= htmlspecialchars($category['name']) ?></h5>
                </a>
            </div>
            <?php $ci++; endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ====== AUTHORS ====== -->
<?php if ($authors && is_array($authors) && count($authors) > 0): ?>
<section class="py-5" style="background: var(--bg-light);" id="authors">
    <div class="container">
        <div class="section-header text-center">
            <div class="section-label">The People Behind the Work</div>
            <h2>Our Authors</h2>
            <p>Researchers and scholars contributing to Tatvam Publication</p>
        </div>
        <?php
        $display_authors = array_slice(is_array($authors) ? $authors : [], 0, 8);
        ?>
        <div class="row g-4">
            <?php foreach ($display_authors as $author): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="author.php?id=<?= $author['id'] ?>" class="author-card" style="text-decoration:none;">
                    <?php if (!empty($author['photo'])): ?>
                        <img src="uploads/author_photos/<?= htmlspecialchars($author['photo']) ?>"
                             class="author-card-photo"
                             alt="<?= htmlspecialchars($author['name']) ?>"
                             loading="lazy"
                             onerror="this.src='img/default-author.png'">
                    <?php else: ?>
                        <div class="author-avatar mx-auto mb-3"><i class="bi bi-person"></i></div>
                    <?php endif; ?>
                    <h5><?= htmlspecialchars($author['name']) ?></h5>
                    <?php if (!empty($author['designation'])): ?>
                        <p class="text-muted small mb-1"><?= htmlspecialchars($author['designation']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($author['organization'])): ?>
                        <p class="text-muted small mb-0"><?= htmlspecialchars($author['organization']) ?></p>
                    <?php endif; ?>
                    <p class="mt-2" style="font-size:12.5px; color: var(--primary); font-weight:600;">
                        View Profile <i class="bi bi-arrow-right"></i>
                    </p>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($authors) > 8): ?>
        <div class="text-center mt-4">
            <a href="authors.php" class="btn btn-outline-primary">View All Authors</a>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- ====== PUBLISH CTA ====== -->
<section class="cta-section">
    <div class="container" style="position:relative; z-index:1;">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div style="font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:rgba(255,255,255,.55); margin-bottom:10px;">For Researchers & Authors</div>
                <h2 class="fw-bold text-white mb-2" style="font-family:'Playfair Display',serif; font-size:2rem;">
                    Ready to Publish Your Work?
                </h2>
                <p style="color:rgba(255,255,255,.75); font-size:1rem; margin:0;">
                    Submit your book or research paper for review. Our editorial team will guide you through the process.
                </p>
            </div>
            <div class="col-lg-5 text-lg-end mt-4 mt-lg-0 d-flex justify-content-lg-end gap-3 flex-wrap">
                <?php if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer'): ?>
                    <a href="submit-book.php" class="btn btn-light btn-lg px-4 fw-semibold">
                        <i class="bi bi-upload"></i> Submit Now
                    </a>
                <?php else: ?>
                    <a href="user-login.php?redirect=submit-book.php" class="btn btn-light btn-lg px-4 fw-semibold">
                        <i class="bi bi-upload"></i> Start Publishing
                    </a>
                    <a href="register.php" class="btn btn-outline-light btn-lg px-4">
                        Create Account
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include "php/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
