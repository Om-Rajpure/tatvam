<?php 
session_start();
include "db_conn.php";

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
	<title>About Us - Tatvam Publication</title>
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
				<span class="brand-text">Book<span class="text-primary">Hub</span></span>
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarMain">
				<ul class="navbar-nav mx-auto">
					<li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
					<li class="nav-item"><a class="nav-link" href="books.php">Books</a></li>
					<li class="nav-item"><a class="nav-link" href="categories.php">Categories</a></li>
					<li class="nav-item"><a class="nav-link active" href="about.php">About</a></li>
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

	<!-- About Hero -->
	<section class="hero-section" style="padding: 80px 0;">
		<div class="container">
			<div class="text-center mb-5">
				<h1 class="display-4 fw-bold">About <span class="text-primary">Tatvam Publication</span></h1>
				<p class="lead text-muted">Your trusted partner in digital reading</p>
			</div>
		</div>
	</section>

	<!-- About Content -->
	<section class="py-5">
		<div class="container">
			<div class="row align-items-center mb-5">
				<div class="col-lg-6">
					<h2 class="fw-bold mb-4">Our Story</h2>
					<p class="text-muted">Tatvam Publication was founded with a simple mission: to make quality books accessible to everyone, everywhere. We believe that knowledge should be available at your fingertips, and reading should be a delightful experience.</p>
					<p class="text-muted">Since our inception, we've grown to become one of the leading digital bookstores, serving thousands of readers worldwide with our extensive collection of books across all genres.</p>
				</div>
				<div class="col-lg-6">
					<img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=600" alt="Books" class="img-fluid rounded shadow">
				</div>
			</div>

			<div class="row align-items-center mb-5">
				<div class="col-lg-6 order-lg-2">
					<h2 class="fw-bold mb-4">Our Mission</h2>
					<p class="text-muted">We strive to create a seamless reading experience by providing instant access to a vast library of digital books. Our platform is designed to be user-friendly, secure, and accessible to readers of all ages.</p>
					<p class="text-muted">We're committed to supporting authors and publishers while making literature more accessible and affordable for readers worldwide.</p>
				</div>
				<div class="col-lg-6 order-lg-1">
					<img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=600" alt="Reading" class="img-fluid rounded shadow">
				</div>
			</div>
		</div>
	</section>

	<!-- Features -->
	<section class="py-5 bg-light">
		<div class="container">
			<h2 class="text-center fw-bold mb-5">Why Choose Us</h2>
			<div class="row g-4">
				<div class="col-md-3">
					<div class="text-center p-4">
						<i class="bi bi-download display-3 text-primary mb-3"></i>
						<h5>Instant Download</h5>
						<p class="text-muted small">Get your books immediately after purchase</p>
					</div>
				</div>
				<div class="col-md-3">
					<div class="text-center p-4">
						<i class="bi bi-infinity display-3 text-primary mb-3"></i>
						<h5>Lifetime Access</h5>
						<p class="text-muted small">Read your books anytime, anywhere</p>
					</div>
				</div>
				<div class="col-md-3">
					<div class="text-center p-4">
						<i class="bi bi-shield-check display-3 text-primary mb-3"></i>
						<h5>Secure Payment</h5>
						<p class="text-muted small">Safe and encrypted transactions</p>
					</div>
				</div>
				<div class="col-md-3">
					<div class="text-center p-4">
						<i class="bi bi-headset display-3 text-primary mb-3"></i>
						<h5>24/7 Support</h5>
						<p class="text-muted small">We're here to help you anytime</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Footer -->
	<footer class="footer">
		<div class="container">
			<div class="row g-4">
				<div class="col-lg-4">
					<h4 class="mb-3"><i class="bi bi-book-half text-primary"></i> Book<span class="text-primary">Hub</span></h4>
					<p class="text-muted">Your premium destination for digital books. Discover, download, and enjoy unlimited reading.</p>
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
						<li><a href="#">Privacy Policy</a></li>
						<li><a href="#">Terms of Service</a></li>
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
