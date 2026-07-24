<?php
session_start();
include "db_conn.php";
include "php/func-book.php";
$books = get_all_books($conn);
include "php/func-author.php";
$authors = get_all_author($conn);
include "php/func-category.php";
$categories = get_all_categories($conn);
include "php/func-cart.php";

$cart_count = 0;
if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
    $cart_count = get_cart_count($conn, $_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Tatvam Publication - Your Premium Digital Library</title>
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
							<a href="my-book-requests.php"><i class="bi bi-file-earmark-text"></i> My Submissions</a>
							<a href="my-orders.php" class="ms-2"><i class="bi bi-bag-check"></i> My Orders</a>
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
					<li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
					<li class="nav-item"><a class="nav-link" href="books.php">Books</a></li>
					<li class="nav-item"><a class="nav-link" href="books.php?type=research_paper">Research Papers</a></li>
					<li class="nav-item"><a class="nav-link" href="categories.php">Categories</a></li>
					<li class="nav-item"><a class="nav-link" href="authors.php">Authors</a></li>
					<li class="nav-item"><a class="nav-link" href="reference.php">Reference</a></li>
					<li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
					<li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
					<?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') { ?>
					<li class="nav-item"><a class="nav-link text-success fw-bold" href="submit-book.php"><i class="bi bi-upload"></i> Publish Your Book</a></li>
					<?php } ?>
				</ul>
				<div class="d-flex align-items-center">
					<button class="btn btn-outline-primary me-2"><i class="bi bi-search"></i></button>
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

	<!-- Hero Section -->
	<section class="hero-section">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-6">
					<div class="hero-content">
						<span class="badge bg-primary mb-3">Welcome to Tatvam Publication</span>
						<h1 class="display-3 fw-bold mb-4">Discover Your Next <span class="text-primary">Favorite Book</span></h1>
						<p class="lead mb-4">Explore thousands of digital books across all genres. Download instantly and start your reading journey today.</p>
						<div class="hero-search">
							<form action="search.php" method="get">
								<div class="input-group input-group-lg">
									<input type="text" class="form-control" name="key" placeholder="Search books, authors, categories...">
									<button class="btn btn-primary px-4" type="submit"><i class="bi bi-search"></i> Search</button>
								</div>
							</form>
						</div>
						<div class="hero-stats mt-4">
							<div class="row">
								<div class="col-4">
									<h3 class="fw-bold text-primary"><?=is_array($books) ? count($books) : 0?>+</h3>
									<p class="text-muted mb-0">Books</p>
								</div>
								<div class="col-4">
									<h3 class="fw-bold text-primary"><?=is_array($categories) ? count($categories) : 0?>+</h3>
									<p class="text-muted mb-0">Categories</p>
								</div>
								<div class="col-4">
									<h3 class="fw-bold text-primary"><?=is_array($authors) ? count($authors) : 0?>+</h3>
									<p class="text-muted mb-0">Authors</p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="hero-image">
						<img src="https://images.unsplash.com/photo-1512820790803-83ca734da794?w=600" alt="Books" class="img-fluid">
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Categories Section -->
	<section class="categories-section py-5" id="categories">
		<div class="container">
			<div class="section-header text-center mb-5">
				<h2 class="fw-bold">Browse by <span class="text-primary">Categories</span></h2>
				<p class="text-muted">Explore books from your favorite genres</p>
			</div>
			<div class="row g-4">
				<?php if ($categories != 0) { 
					foreach ($categories as $category) { ?>
				<div class="col-6 col-md-4 col-lg-3">
					<a href="category.php?id=<?=$category['id']?>" class="category-card">
						<div class="category-icon">
							<i class="bi bi-folder-fill"></i>
						</div>
						<h5><?=$category['name']?></h5>
					</a>
				</div>
				<?php }} ?>
			</div>
		</div>
	</section>

	<!-- Books Section -->
	<section class="books-section py-5 bg-light" id="books">
		<div class="container">
			<div class="section-header text-center mb-5">
				<h2 class="fw-bold">Featured <span class="text-primary">Books</span></h2>
				<p class="text-muted">Discover our handpicked collection</p>
			</div>

			<?php if (isset($_GET['error'])) { ?>
				<div class="alert alert-danger alert-dismissible fade show">
					<i class="bi bi-exclamation-triangle"></i> <?=htmlspecialchars($_GET['error'])?>
					<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				</div>
			<?php } ?>
			<?php if (isset($_GET['success'])) { ?>
				<div class="alert alert-success alert-dismissible fade show">
					<i class="bi bi-check-circle"></i> <?=htmlspecialchars($_GET['success'])?>
					<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				</div>
			<?php } ?>

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
									<?php } else { ?>
										<span class="book-price">Free</span>
									<?php } ?>
									
									<?php if (isset($_SESSION['user_id'])) {
										if (is_in_cart($conn, $_SESSION['user_id'], $book['id'])) { ?>
											<a href="cart.php" class="btn btn-sm btn-secondary"><i class="bi bi-cart-check"></i></a>
										<?php } else { ?>
											<a href="php/add-to-cart.php?book_id=<?=$book['id']?>" class="btn btn-sm btn-primary"><i class="bi bi-cart-plus"></i></a>
										<?php }
									} else { ?>
										<a href="user-login.php" class="btn btn-sm btn-primary"><i class="bi bi-box-arrow-in-right"></i></a>
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

	<!-- Authors Section -->
	<section class="authors-section py-5">
		<div class="container">
			<div class="section-header text-center mb-5">
				<h2 class="fw-bold">Popular <span class="text-primary">Authors</span></h2>
				<p class="text-muted">Meet our featured authors</p>
			</div>
			<div class="row g-4">
				<?php if ($authors != 0) { 
					foreach ($authors as $author) { ?>
				<div class="col-6 col-md-4 col-lg-3">
					<a href="author.php?id=<?=$author['id']?>" class="author-card">
						<div class="author-avatar">
							<i class="bi bi-person-circle"></i>
						</div>
						<h5><?=$author['name']?></h5>
						<p class="text-muted">View Books</p>
					</a>
				</div>
				<?php }} ?>
			</div>
		</div>
	</section>

	<!-- CTA Section -->
	<section class="cta-section">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-8">
					<h2 class="fw-bold text-white mb-3">Start Your Reading Journey Today</h2>
					<p class="text-white-50 mb-0">Join thousands of readers and access unlimited books</p>
				</div>
				<div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
					<button class="btn btn-light btn-lg px-5">Get Started</button>
				</div>
			</div>
		</div>
	</section>

	<!-- Footer -->
	<footer class="footer">
		<div class="container">
			<div class="row g-4">
				<div class="col-lg-4">
					<h4 class="mb-3"><i class="bi bi-book-half text-primary"></i> Tatvam <span class="text-primary">Publication</span></h4>
					<p class="text-muted">Your premium destination for digital books. Discover, download, and enjoy unlimited reading.</p>
					<div class="social-links">
						<a href="#"><i class="bi bi-facebook"></i></a>
						<a href="#"><i class="bi bi-twitter"></i></a>
						<a href="#"><i class="bi bi-instagram"></i></a>
						<a href="#"><i class="bi bi-linkedin"></i></a>
					</div>
				</div>
				<div class="col-lg-2 col-6">
					<h5 class="mb-3">Quick Links</h5>
					<ul class="footer-links">
						<li><a href="#">Home</a></li>
						<li><a href="#">Books</a></li>
						<li><a href="#">Categories</a></li>
						<li><a href="#">Authors</a></li>
					</ul>
				</div>
				<div class="col-lg-2 col-6">
					<h5 class="mb-3">Support</h5>
					<ul class="footer-links">
						<li><a href="#">Help Center</a></li>
						<li><a href="#">Contact Us</a></li>
						<li><a href="#">Privacy Policy</a></li>
						<li><a href="#">Terms of Service</a></li>
					</ul>
				</div>
				<div class="col-lg-4">
					<h5 class="mb-3">Newsletter</h5>
					<p class="text-muted">Subscribe to get updates on new books</p>
					<form class="newsletter-form">
						<div class="input-group">
							<input type="email" class="form-control" placeholder="Your email">
							<button class="btn btn-primary">Subscribe</button>
						</div>
					</form>
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
