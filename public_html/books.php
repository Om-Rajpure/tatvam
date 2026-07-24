<?php 
session_start();
include "db_conn.php";
include "php/func-book.php";
$books = get_all_books($conn);
include "php/func-author.php";
$authors = get_all_author($conn);
include "php/func-category.php";
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
	<title>All Books - Tatvam Publication</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<!-- Top Bar -->
	<div class="top-bar">
		<div class="container">
			<div class="d-flex justify-content-between align-items-center">
				<div class="top-info">
					<i class="bi bi-envelope"></i> support@tatvampublication.com
					<span class="ms-3"><i class="bi bi-telephone"></i> +91 1234567890</span>
				</div>
				<div class="top-links">
					<?php if (isset($_SESSION['user_id'])) {
						if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') { ?>
							<a href="my-orders.php"><i class="bi bi-bag-check"></i> My Orders</a>
							<a href="user-profile.php" class="ms-2"><i class="bi bi-person-circle"></i> <?=$_SESSION['user_name']?></a>
							<a href="php/user-logout.php" class="ms-2"><i class="bi bi-box-arrow-right"></i> Logout</a>
						<?php } else { ?>
							<a href="admin.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
						<?php }
					}else{ ?>
						<a href="user-login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
						<a href="register.php" class="ms-2"><i class="bi bi-person-plus"></i> Register</a>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Navigation -->
	<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
		<div class="container">
			<a class="navbar-brand" href="index.php">
				<i class="bi bi-book-half text-primary"></i>
				<span class="brand-text">Tatvam <span class="text-primary">Publication</span></span>
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarMain">
				<ul class="navbar-nav mx-auto">
					<li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
					<li class="nav-item"><a class="nav-link active" href="books.php">Books</a></li>
					<li class="nav-item"><a class="nav-link" href="categories.php">Categories</a></li>
					<li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
					<li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
				</ul>
				<div class="d-flex align-items-center">
					<?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') { ?>
						<a href="cart.php" class="btn btn-primary position-relative">
							<i class="bi bi-cart3"></i> Cart
							<?php if ($cart_count > 0) { ?>
								<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
									<?=$cart_count?>
								</span>
							<?php } ?>
						</a>
					<?php } else { ?>
						<a href="user-login.php" class="btn btn-primary"><i class="bi bi-cart3"></i> Cart</a>
					<?php } ?>
				</div>
			</div>
		</div>
	</nav>

	<!-- Page Header -->
	<section class="py-5 bg-light">
		<div class="container">
			<h1 class="display-5 fw-bold text-center">Explore Our <span class="text-primary">Book Collection</span></h1>
			<p class="text-center text-muted">Discover thousands of books across all genres</p>
		</div>
	</section>

	<!-- Books Section -->
	<section class="py-5">
		<div class="container">
			<?php if ($books == 0){ ?>
				<div class="text-center py-5">
					<i class="bi bi-inbox display-1 text-muted"></i>
					<h4 class="mt-3 text-muted">No books available yet</h4>
					<p class="text-muted">Check back soon for new additions</p>
				</div>
			<?php }else{ ?>
				<div class="row g-4">
					<?php foreach ($books as $book) { ?>
					<div class="col-6 col-md-4 col-lg-3">
						<div class="book-card">
							<div class="book-image">
								<img src="uploads/cover/<?=$book['cover']?>" alt="<?=$book['title']?>" onerror="this.src='https://via.placeholder.com/300x400?text=No+Cover'">
								<?php if ($book['price'] == 0) { ?>
								<div class="book-overlay">
									<a href="uploads/files/<?=$book['file']?>" class="btn btn-light btn-sm" target="_blank"><i class="bi bi-eye"></i></a>
									<a href="uploads/files/<?=$book['file']?>" class="btn btn-primary btn-sm" download><i class="bi bi-download"></i></a>
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
									<i class="bi bi-person"></i>
									<?php foreach($authors as $author){ 
										if ($author['id'] == $book['author_id']) {
											echo $author['name'];
											break;
										}
									} ?>
								</p>
								<div class="book-footer">
									<?php if ($book['price'] > 0) { ?>
										<span class="book-price">₹<?=number_format($book['price'], 2)?></span>
										<?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
											if (isset($cart_count) && is_in_cart($conn, $_SESSION['user_id'], $book['id'])) { ?>
												<a href="cart.php" class="btn btn-sm btn-secondary"><i class="bi bi-cart-check"></i></a>
											<?php } else { ?>
												<a href="php/add-to-cart.php?book_id=<?=$book['id']?>" class="btn btn-sm btn-primary"><i class="bi bi-cart-plus"></i></a>
											<?php }
										} else { ?>
											<a href="user-login.php" class="btn btn-sm btn-primary"><i class="bi bi-box-arrow-in-right"></i></a>
										<?php } ?>
									<?php } else { ?>
										<span class="book-price">Free</span>
										<a href="uploads/files/<?=$book['file']?>" class="btn btn-sm btn-success" download>
											<i class="bi bi-download"></i>
										</a>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</section>

	<!-- Footer -->
	<footer class="footer">
		<div class="container">
			<div class="row g-4">
				<div class="col-lg-4">
					<h4 class="mb-3"><i class="bi bi-book-half text-primary"></i> Tatvam <span class="text-primary">Publication</span></h4>
					<p class="text-muted">Your premium destination for digital books.</p>
				</div>
				<div class="col-lg-2 col-6">
					<h5 class="mb-3">Quick Links</h5>
					<ul class="footer-links">
						<li><a href="index.php">Home</a></li>
						<li><a href="books.php">Books</a></li>
						<li><a href="categories.php">Categories</a></li>
						<li><a href="about.php">About</a></li>
					</ul>
				</div>
				<div class="col-lg-2 col-6">
					<h5 class="mb-3">Support</h5>
					<ul class="footer-links">
						<li><a href="contact.php">Contact Us</a></li>
					</ul>
				</div>
				<div class="col-lg-4">
					<h5 class="mb-3">Contact</h5>
					<p class="text-muted"><i class="bi bi-envelope"></i> support@tatvampublication.com</p>
					<p class="text-muted"><i class="bi bi-telephone"></i> +91 1234567890</p>
				</div>
			</div>
			<hr class="my-4">
			<div class="text-center text-muted">
				<p class="mb-0">&copy; 2024 Tatvam Publication. All rights reserved.</p>
			</div>
		</div>
	</footer>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
