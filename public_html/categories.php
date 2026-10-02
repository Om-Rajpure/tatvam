<?php 
session_start();
include "db_conn.php";
include "php/func-category.php";

$categories = get_all_categories_with_count($conn);

$cart_count = 0;
if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
    include_once "php/func-cart.php";
    $cart_count = get_cart_count($conn, $_SESSION['user_id']);
}

$current_page = 'categories.php';

// Helper for category visual identity
function get_category_meta($name) {
    $n = strtolower($name);
    if (strpos($n, 'artificial') !== false || strpos($n, 'intelligence') !== false || strpos($n, 'ai') !== false) {
        return ['icon' => 'bi-cpu-fill', 'color' => '#4F46E5', 'bg' => '#EEF2FF', 'desc' => 'AI, neural networks & intelligent algorithms'];
    }
    if (strpos($n, 'data science') !== false || strpos($n, 'data') !== false) {
        return ['icon' => 'bi-bar-chart-fill', 'color' => '#059669', 'bg' => '#ECFDF5', 'desc' => 'Big data analytics, mining & visualization'];
    }
    if (strpos($n, 'machine learning') !== false || strpos($n, 'ml') !== false) {
        return ['icon' => 'bi-diagram-3-fill', 'color' => '#0891B2', 'bg' => '#ECFEFF', 'desc' => 'Predictive modeling, deep learning & ML ops'];
    }
    if (strpos($n, 'environment') !== false) {
        return ['icon' => 'bi-tree-fill', 'color' => '#16A34A', 'bg' => '#F0FDF4', 'desc' => 'Ecology, sustainable tech & green solutions'];
    }
    if (strpos($n, 'computer') !== false) {
        return ['icon' => 'bi-laptop-fill', 'color' => '#2563EB', 'bg' => '#EFF6FF', 'desc' => 'Software engineering, systems & cloud tech'];
    }
    if (strpos($n, 'agri') !== false) {
        return ['icon' => 'bi-flower1', 'color' => '#D97706', 'bg' => '#FFFBEB', 'desc' => 'Agronomy, precision farming & smart crops'];
    }
    if (strpos($n, 'bio') !== false) {
        return ['icon' => 'bi-capsule', 'color' => '#DC2626', 'bg' => '#FEF2F2', 'desc' => 'Bioinformatics, genetics & pharmaceutical science'];
    }
    if (strpos($n, 'electr') !== false) {
        return ['icon' => 'bi-lightning-charge-fill', 'color' => '#CA8A04', 'bg' => '#FEFCE8', 'desc' => 'Circuits, embedded tech & VLSI hardware'];
    }
    if (strpos($n, 'commerce') !== false || strpos($n, 'business') !== false) {
        return ['icon' => 'bi-briefcase-fill', 'color' => '#17324D', 'bg' => '#EEF0F4', 'desc' => 'Finance, global trade & corporate strategy'];
    }
    if (strpos($n, 'digital') !== false) {
        return ['icon' => 'bi-broadcast-pin', 'color' => '#7C3AED', 'bg' => '#F5F3FF', 'desc' => 'Digital transformation & tech governance'];
    }
    if (strpos($n, 'literature') !== false || strpos($n, 'fiction') !== false) {
        return ['icon' => 'bi-book-half', 'color' => '#9333EA', 'bg' => '#FAF5FF', 'desc' => 'Literary studies, narratives & linguistics'];
    }
    if (strpos($n, 'civil') !== false) {
        return ['icon' => 'bi-building-fill', 'color' => '#475569', 'bg' => '#F1F5F9', 'desc' => 'Structural engineering & infrastructure'];
    }
    if (strpos($n, 'mechanical') !== false) {
        return ['icon' => 'bi-gear-wide-connected', 'color' => '#64748B', 'bg' => '#F8FAFC', 'desc' => 'Thermodynamics, robotics & mechanics'];
    }
    if (strpos($n, 'social') !== false) {
        return ['icon' => 'bi-people-fill', 'color' => '#0D9488', 'bg' => '#F0FDFA', 'desc' => 'Sociology, human behavior & cultural dynamics'];
    }
    if (strpos($n, 'economic') !== false) {
        return ['icon' => 'bi-graph-up-arrow', 'color' => '#2563EB', 'bg' => '#EFF6FF', 'desc' => 'Macroeconomics, fiscal analysis & markets'];
    }
    if (strpos($n, 'research') !== false) {
        return ['icon' => 'bi-award-fill', 'color' => '#C58A2B', 'bg' => '#FFFDF5', 'desc' => 'Peer-reviewed academic research papers'];
    }
    return ['icon' => 'bi-journal-bookmark-fill', 'color' => '#17324D', 'bg' => '#EEF0F4', 'desc' => 'Specialized academic publications & literature'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Categories — Tatvam Publication</title>
    <meta name="description" content="Explore published books and research papers organized by academic category and discipline.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .category-hero-stats {
            display: inline-flex;
            gap: 24px;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 8px 24px;
            margin-top: 14px;
            backdrop-filter: blur(8px);
        }
        .category-stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }
        .category-stat-item i {
            color: var(--primary);
            font-size: 16px;
        }
        .cat-card-modern {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px 20px 20px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-xs);
        }
        .cat-card-modern::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary);
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        .cat-card-modern:hover {
            transform: translateY(-4px);
            border-color: var(--border-strong);
            box-shadow: var(--shadow-md);
            color: var(--text-primary);
        }
        .cat-card-modern:hover::after {
            opacity: 1;
        }
        .cat-icon-squircle {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 18px;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .cat-card-modern:hover .cat-icon-squircle {
            transform: scale(1.08) rotate(2deg);
        }
        .cat-card-modern h4 {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
            letter-spacing: -0.01em;
            line-height: 1.35;
        }
        .cat-card-modern:hover h4 {
            color: var(--primary);
        }
        .cat-card-desc {
            font-size: 12.5px;
            color: var(--text-secondary);
            line-height: 1.5;
            margin-bottom: 16px;
        }
        .cat-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 14px;
            border-top: 1px solid var(--border);
            font-size: 12px;
        }
        .cat-pill-count {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-weight: 600;
            color: var(--text-secondary);
            background: var(--bg-muted);
            padding: 3px 10px;
            border-radius: 20px;
        }
        .cat-pill-count.has-books {
            background: rgba(36, 122, 82, 0.1);
            color: var(--success);
        }
        .cat-card-arrow {
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: color 0.2s ease, transform 0.2s ease;
        }
        .cat-card-modern:hover .cat-card-arrow {
            color: var(--primary);
            transform: translateX(4px);
        }
        .cat-search-box {
            max-width: 480px;
            margin: 0 auto;
            position: relative;
        }
        .cat-search-box .form-control {
            border-radius: 100px;
            padding: 12px 20px 12px 48px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            font-size: 14px;
        }
        .cat-search-box .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(23, 50, 77, 0.12);
        }
        .cat-search-box i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 17px;
        }
    </style>
</head>
<body>

<?php include "php/navbar.php"; ?>

<!-- Page Header -->
<section class="page-header text-center">
    <div class="container">
        <nav aria-label="breadcrumb" class="d-flex justify-content-center mb-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Categories</li>
            </ol>
        </nav>
        <h1 class="mb-2">Browse by <span class="text-primary">Categories</span></h1>
        <p class="lead text-muted mx-auto" style="max-width: 620px; font-size: 1rem;">
            Explore scholarly publications, specialized books, and peer-reviewed research papers across all academic domains.
        </p>

        <!-- Stats strip -->
        <div class="category-hero-stats">
            <div class="category-stat-item">
                <i class="bi bi-folder2-open"></i>
                <span><?= is_array($categories) ? count($categories) : 0 ?> Disciplines</span>
            </div>
            <div class="category-stat-item border-start ps-3">
                <i class="bi bi-book"></i>
                <span>All Academic Genres</span>
            </div>
            <div class="category-stat-item border-start ps-3">
                <i class="bi bi-patch-check-fill text-success"></i>
                <span>Peer-Reviewed</span>
            </div>
        </div>

        <!-- Quick filter search -->
        <div class="cat-search-box mt-4">
            <i class="bi bi-search"></i>
            <input type="text" id="categoryFilterInput" class="form-control" placeholder="Quick search category by name or keyword..." onkeyup="filterCategories()">
        </div>
    </div>
</section>

<!-- Categories Grid Section -->
<section class="py-5 bg-light">
    <div class="container">
        <?php if ($categories == 0 || (is_array($categories) && count($categories) === 0)): ?>
            <div class="empty-state">
                <i class="bi bi-folder-x empty-icon"></i>
                <h4 class="mt-3">No categories found</h4>
                <p class="text-muted">Categories are currently being curated. Please check back shortly.</p>
                <a href="books.php" class="btn btn-primary btn-sm mt-2">
                    <i class="bi bi-book me-1"></i> Browse All Books
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4" id="categoriesGrid">
                <?php foreach ($categories as $cat): 
                    $meta = get_category_meta($cat['name']);
                    $count = intval($cat['book_count'] ?? 0);
                ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 category-item-col" data-cat-name="<?= htmlspecialchars(strtolower($cat['name'])) ?>" data-cat-desc="<?= htmlspecialchars(strtolower($meta['desc'])) ?>">
                    <a href="books.php?category=<?= $cat['id'] ?>" class="cat-card-modern">
                        <div>
                            <div class="cat-icon-squircle" style="background-color: <?= $meta['bg'] ?>; color: <?= $meta['color'] ?>;">
                                <i class="bi <?= $meta['icon'] ?>"></i>
                            </div>
                            <h4><?= htmlspecialchars($cat['name']) ?></h4>
                            <p class="cat-card-desc"><?= htmlspecialchars($meta['desc']) ?></p>
                        </div>
                        <div class="cat-card-footer">
                            <span class="cat-pill-count <?= $count > 0 ? 'has-books' : '' ?>">
                                <i class="bi <?= $count > 0 ? 'bi-journal-check' : 'bi-journal' ?>"></i>
                                <?= $count ?> <?= $count === 1 ? 'Publication' : 'Publications' ?>
                            </span>
                            <span class="cat-card-arrow">
                                Explore <i class="bi bi-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>

            <div id="noMatchMessage" class="empty-state d-none">
                <i class="bi bi-search empty-icon"></i>
                <h4 class="mt-3">No matching category</h4>
                <p class="text-muted">No academic discipline matches your keyword. Try searching for a different topic.</p>
                <button class="btn btn-outline-primary btn-sm mt-2" onclick="clearCategorySearch()">
                    Clear Search
                </button>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Callout Banner -->
<section class="py-5 bg-white border-top">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="p-4 p-md-5 rounded-3" style="background: linear-gradient(135deg, var(--bg-light) 0%, var(--bg-muted) 100%); border: 1px solid var(--border);">
                    <span class="badge bg-primary px-3 py-2 rounded-pill mb-3" style="font-size: 12px; letter-spacing: 0.04em;">FOR AUTHORS & RESEARCHERS</span>
                    <h3 class="fw-bold mb-2">Publish In Your Discipline</h3>
                    <p class="text-secondary mb-4 mx-auto" style="max-width: 500px;">
                        Submit your book manuscripts or research papers to Tatvam Publication for peer review, cataloguing, and global academic indexing.
                    </p>
                    <a href="submit-book.php" class="btn btn-primary px-4 py-2">
                        <i class="bi bi-upload me-1"></i> Submit For Publication
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "php/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
<script>
function filterCategories() {
    const query = document.getElementById('categoryFilterInput').value.toLowerCase().trim();
    const items = document.querySelectorAll('.category-item-col');
    let visibleCount = 0;

    items.forEach(item => {
        const name = item.getAttribute('data-cat-name') || '';
        const desc = item.getAttribute('data-cat-desc') || '';
        if (name.includes(query) || desc.includes(query)) {
            item.classList.remove('d-none');
            visibleCount++;
        } else {
            item.classList.add('d-none');
        }
    });

    const noMatch = document.getElementById('noMatchMessage');
    if (noMatch) {
        if (visibleCount === 0) {
            noMatch.classList.remove('d-none');
        } else {
            noMatch.classList.add('d-none');
        }
    }
}

function clearCategorySearch() {
    const input = document.getElementById('categoryFilterInput');
    if (input) {
        input.value = '';
        filterCategories();
    }
}
</script>
</body>
</html>
