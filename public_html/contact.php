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
	<title>Contact Us - Tatvam Publication</title>
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
					<li class="nav-item"><a class="nav-link" href="books.php">Books</a></li>
					<li class="nav-item"><a class="nav-link" href="books.php?type=research_paper">Research Papers</a></li>
					<li class="nav-item"><a class="nav-link" href="categories.php">Categories</a></li>
					<li class="nav-item"><a class="nav-link" href="authors.php">Authors</a></li>
					<li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
					<li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
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
			<h1 class="display-5 fw-bold text-center">Get in <span class="text-primary">Touch</span></h1>
			<p class="text-center text-muted">We'd love to hear from you</p>
		</div>
	</section>

	<!-- Contact Section -->
	<section class="py-5">
		<div class="container">
			<div class="row g-4">
				<div class="col-lg-4">
					<div class="card border-0 shadow-sm h-100">
						<div class="card-body text-center p-4">
							<i class="bi bi-geo-alt display-4 text-primary mb-3"></i>
							<h5>Visit Us</h5>
							<p class="text-muted">123 Book Street<br>Mumbai, Maharashtra<br>India - 400001</p>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="card border-0 shadow-sm h-100">
						<div class="card-body text-center p-4">
							<i class="bi bi-telephone display-4 text-primary mb-3"></i>
							<h5>Call Us</h5>
							<p class="text-muted">+91 1234567890<br>Mon-Sat: 9AM - 6PM<br>Sunday: Closed</p>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="card border-0 shadow-sm h-100">
						<div class="card-body text-center p-4">
							<i class="bi bi-envelope display-4 text-primary mb-3"></i>
							<h5>Email Us</h5>
							<p class="text-muted">support@tatvampublication.com<br>sales@Tatvam Publication.com<br>Response within 24 hours</p>
						</div>
					</div>
				</div>
			</div>

			<div class="row mt-5">
				<div class="col-lg-8 mx-auto">
					<div class="card border-0 shadow-sm">
						<div class="card-body p-4">
							<h3 class="mb-4">Send us a Message</h3>
							<form>
								<div class="row g-3">
									<div class="col-md-6">
										<label class="form-label">Your Name</label>
										<input type="text" class="form-control" placeholder="John Doe" required>
									</div>
									<div class="col-md-6">
										<label class="form-label">Your Email</label>
										<input type="email" class="form-control" placeholder="john@example.com" required>
									</div>
									<div class="col-12">
										<label class="form-label">Subject</label>
										<input type="text" class="form-control" placeholder="How can we help?" required>
									</div>
									<div class="col-12">
										<label class="form-label">Message</label>
										<textarea class="form-control" rows="5" placeholder="Your message here..." required></textarea>
									</div>
									<div class="col-12">
										<button type="submit" class="btn btn-primary btn-lg w-100">
											<i class="bi bi-send"></i> Send Message
										</button>
									</div>
								</div>
							</form>
						</div>
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
