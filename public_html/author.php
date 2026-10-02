<?php
session_start();
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$id = intval($_GET['id']);
include "db_conn.php";
include "php/func-author.php";
include "php/func-book.php";
include "php/func-category.php";
$current_author = get_author($conn, $id);
if (!$current_author) {
    header("Location: authors.php");
    exit;
}

if (!function_exists('get_books_by_author_typed')) {
    $books_by_author = get_books_by_author($conn, $id);
    $papers_by_author = 0;
} else {
    $books_by_author = get_books_by_author_typed($conn, $id, 'book');
    $papers_by_author = get_books_by_author_typed($conn, $id, 'research_paper');
}

$categories = get_all_categories($conn);
$cart_count = 0;
if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
    include "php/func-cart.php";
    $cart_count = get_cart_count($conn, $_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?=htmlspecialchars($current_author['name'])?> - Tatvam Publication</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="css/style.css">
    <style>
        .author-profile-hero {
            background: #f8f9fa;
            padding: 40px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .author-profile-photo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .author-profile-avatar {
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
            color: white;
            margin: 0 auto;
            border: 4px solid #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
    </style>
<?php $current_page = 'authors.php'; ?>
</head>
<body>
<?php include "php/navbar.php"; ?>

    <!-- Author Profile Hero section -->
    <section class="author-profile-hero">
    <div class="container">
        <div class="row align-items-center">
        <div class="col-md-auto text-center text-md-start mb-4 mb-md-0">
            <!-- Photo or avatar -->
            <?php if (!empty($current_author['photo'])): ?>
            <img src="uploads/author_photos/<?=$current_author['photo']?>" class="author-profile-photo" alt="<?=htmlspecialchars($current_author['name'])?>">
            <?php else: ?>
            <div class="author-profile-avatar"><i class="bi bi-person-fill"></i></div>
            <?php endif; ?>
        </div>
        <div class="col">
            <h1 class="fw-bold display-5 mb-2"><?=htmlspecialchars($current_author['name'])?></h1>
            <?php if (!empty($current_author['qualification'])): ?>
            <span class="book-category mb-2 d-inline-block"><?=htmlspecialchars($current_author['qualification'])?></span>
            <?php endif; ?>
            <?php if (!empty($current_author['designation']) || !empty($current_author['organization'])): ?>
            <p class="text-muted mb-1">
            <?php if (!empty($current_author['designation'])): ?><?=htmlspecialchars($current_author['designation'])?><?php endif; ?>
            <?php if (!empty($current_author['designation']) && !empty($current_author['organization'])): ?> &mdash; <?php endif; ?>
            <?php if (!empty($current_author['organization'])): ?><?=htmlspecialchars($current_author['organization'])?><?php endif; ?>
            </p>
            <?php endif; ?>
        </div>
        </div>
    </div>
    </section>

    <!-- About section -->
    <section class="py-4">
    <div class="container">
        <?php if (!empty($current_author['about'])): ?>
        <div class="row">
        <div class="col-lg-9">
            <h5 class="fw-bold mb-3"><i class="bi bi-person-lines-fill text-primary"></i> About <?=htmlspecialchars($current_author['name'])?></h5>
            <p class="text-muted" style="line-height: 1.8;"><?=nl2br(htmlspecialchars($current_author['about']))?></p>
        </div>
        </div>
        <hr>
        <?php endif; ?>
        
        <!-- Published Books section -->
        <div class="row mt-4">
            <div class="col-12 mb-4">
                <h4 class="fw-bold"><i class="bi bi-journal-text text-primary"></i> Published Books</h4>
            </div>
        </div>
        
        <?php if ($books_by_author == 0): ?>
            <div class="text-center py-4 bg-light rounded mb-5">
                <i class="bi bi-journal-x display-4 text-muted mb-3"></i>
                <h5 class="text-muted">No books published yet</h5>
            </div>
        <?php else: ?>
            <div class="row g-4 mb-5">
                <?php foreach ($books_by_author as $book) { ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="book-detail.php?id=<?=$book['id']?>" style="text-decoration:none;color:inherit;display:block;height:100%;">
                        <div class="book-card">
                            <div class="book-image">
                                <img src="uploads/cover/<?=$book['cover']?>" alt="<?=$book['title']?>" onerror="this.src='img/default-book.png'">
                                <?php if ($book['price'] == 0) { ?>
                                <div class="book-overlay">
                                    <span class="btn btn-light btn-sm"><i class="bi bi-eye"></i></span>
                                    <span class="btn btn-primary btn-sm"><i class="bi bi-download"></i></span>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="book-info">
                                <span class="book-category">
                                    <?php foreach($categories as $category){ 
                                        if ($category['id'] == $book['category_id']) {
                                            echo $category['name'];
                                            break;
                                        }
                                    } ?>
                                </span>
                                <h5 class="book-title"><?=$book['title']?></h5>
                                <p class="book-author">
                                    <i class="bi bi-person"></i> <?=htmlspecialchars($current_author['name'])?>
                                </p>
                                <div class="book-footer">
                                    <?php if ($book['price'] > 0) { ?>
                                        <span class="book-price">₹<?=number_format($book['price'], 2)?></span>
                                        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
                                            if (isset($cart_count) && function_exists('is_in_cart') && is_in_cart($conn, $_SESSION['user_id'], $book['id'])) { ?>
                                                <object><a href="cart.php" class="btn btn-sm btn-secondary"><i class="bi bi-cart-check"></i></a></object>
                                            <?php } else { ?>
                                                <object><a href="php/add-to-cart.php?book_id=<?=$book['id']?>" class="btn btn-sm btn-primary"><i class="bi bi-cart-plus"></i></a></object>
                                            <?php }
                                        } else { ?>
                                            <object><a href="user-login.php" class="btn btn-sm btn-primary"><i class="bi bi-box-arrow-in-right"></i></a></object>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <span class="book-price">Free</span>
                                        <object><a href="uploads/files/<?=$book['file']?>" class="btn btn-sm btn-success" download>
                                            <i class="bi bi-download"></i>
                                        </a></object>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php } ?>
            </div>
        <?php endif; ?>

        <!-- Published Research Papers section -->
        <div class="row mt-4">
            <div class="col-12 mb-4">
                <h4 class="fw-bold"><i class="bi bi-file-earmark-text text-primary"></i> Research Papers</h4>
            </div>
        </div>
        
        <?php if ($papers_by_author == 0): ?>
            <div class="text-center py-4 bg-light rounded mb-5">
                <i class="bi bi-file-earmark-x display-4 text-muted mb-3"></i>
                <h5 class="text-muted">No research papers published yet</h5>
            </div>
        <?php else: ?>
            <div class="row g-4 mb-5">
                <?php foreach ($papers_by_author as $book) { ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="book-detail.php?id=<?=$book['id']?>" style="text-decoration:none;color:inherit;display:block;height:100%;">
                        <div class="book-card">
                            <div class="book-image">
                                <img src="uploads/cover/<?=$book['cover']?>" alt="<?=$book['title']?>" onerror="this.src='img/default-book.png'">
                                <?php if ($book['price'] == 0) { ?>
                                <div class="book-overlay">
                                    <span class="btn btn-light btn-sm"><i class="bi bi-eye"></i></span>
                                    <span class="btn btn-primary btn-sm"><i class="bi bi-download"></i></span>
                                </div>
                                <?php } ?>
                            </div>
                            <div class="book-info">
                                <span class="book-category">
                                    <?php foreach($categories as $category){ 
                                        if ($category['id'] == $book['category_id']) {
                                            echo $category['name'];
                                            break;
                                        }
                                    } ?>
                                </span>
                                <h5 class="book-title"><?=$book['title']?></h5>
                                <p class="book-author">
                                    <i class="bi bi-person"></i> <?=htmlspecialchars($current_author['name'])?>
                                </p>
                                <div class="book-footer">
                                    <?php if ($book['price'] > 0) { ?>
                                        <span class="book-price">₹<?=number_format($book['price'], 2)?></span>
                                        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
                                            if (isset($cart_count) && function_exists('is_in_cart') && is_in_cart($conn, $_SESSION['user_id'], $book['id'])) { ?>
                                                <object><a href="cart.php" class="btn btn-sm btn-secondary"><i class="bi bi-cart-check"></i></a></object>
                                            <?php } else { ?>
                                                <object><a href="php/add-to-cart.php?book_id=<?=$book['id']?>" class="btn btn-sm btn-primary"><i class="bi bi-cart-plus"></i></a></object>
                                            <?php }
                                        } else { ?>
                                            <object><a href="user-login.php" class="btn btn-sm btn-primary"><i class="bi bi-box-arrow-in-right"></i></a></object>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <span class="book-price">Free</span>
                                        <object><a href="uploads/files/<?=$book['file']?>" class="btn btn-sm btn-success" download>
                                            <i class="bi bi-download"></i>
                                        </a></object>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php } ?>
            </div>
        <?php endif; ?>

    </div>
    </section>

<?php include "php/footer.php"; ?>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
