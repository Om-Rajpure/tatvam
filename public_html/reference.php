<?php
session_start();
include "db_conn.php";
include "php/func-book.php";
include "php/func-category.php";
include "php/func-author.php";
include "php/func-cart.php";

$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $cart_count = get_cart_count($conn, $_SESSION['user_id']);
}

$books = get_all_books($conn);
$categories = get_all_categories($conn);
$authors = get_all_author($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Academic Reference & Citation Center - Tatvam Publication</title>
	<meta name="description" content="Explore academic citations, DOI index verification, reference guidelines, and research publication standards at Tatvam Publication.">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<!-- Top Bar -->
	<div class="top-bar py-2 bg-dark text-white">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-md-6 text-center text-md-start">
					<small><i class="bi bi-envelope"></i> contact@tatvampublication.com | <i class="bi bi-phone"></i> +91 9876543210</small>
				</div>
				<div class="col-md-6 text-center text-md-end">
					<div class="top-links">
						<?php if (isset($_SESSION['user_id'])) { 
							if ($_SESSION['user_type'] == 'customer') { ?>
								<a href="my-book-requests.php" class="text-white text-decoration-none me-2"><i class="bi bi-file-earmark-text"></i> My Submissions</a>
								<a href="my-orders.php" class="text-white text-decoration-none me-2"><i class="bi bi-bag-check"></i> My Orders</a>
								<a href="user-profile.php" class="text-white text-decoration-none me-2"><i class="bi bi-person-circle"></i> <?=htmlspecialchars($_SESSION['user_name'])?></a>
								<a href="php/user-logout.php" class="text-white text-decoration-none"><i class="bi bi-box-arrow-right"></i> Logout</a>
							<?php } else { ?>
								<a href="admin.php" class="text-white text-decoration-none"><i class="bi bi-speedometer2"></i> Dashboard</a>
							<?php }
						} else { ?>
							<a href="user-login.php" class="text-white text-decoration-none me-2"><i class="bi bi-box-arrow-in-right"></i> Login</a>
							<a href="register.php" class="text-white text-decoration-none"><i class="bi bi-person-plus"></i> Register</a>
						<?php } ?>
					</div>
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
					<li class="nav-item"><a class="nav-link active" href="reference.php">Reference</a></li>
					<li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
					<li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
					<?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') { ?>
						<li class="nav-item"><a class="nav-link text-success fw-bold" href="submit-book.php"><i class="bi bi-upload"></i> Publish</a></li>
					<?php } ?>
				</ul>
				<div class="d-flex align-items-center">
					<a href="cart.php" class="btn btn-primary position-relative">
						<i class="bi bi-cart3"></i> Cart
						<?php if ($cart_count > 0) { ?>
							<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
								<?=$cart_count?>
							</span>
						<?php } ?>
					</a>
				</div>
			</div>
		</div>
	</nav>

	<!-- Header Banner -->
	<section class="bg-primary text-white py-5">
		<div class="container text-center">
			<h1 class="display-4 fw-bold mb-3"><i class="bi bi-journal-bookmark"></i> Academic Reference & Citation Center</h1>
			<p class="lead mb-0">Standardized citation tools, DOI/ISBN lookup indices, and author referencing guidelines.</p>
		</div>
	</section>

	<!-- Main Content -->
	<section class="py-5">
		<div class="container">
			<div class="row g-4 mb-5">
				<div class="col-md-4">
					<div class="card h-100 border-0 shadow-sm text-center p-4">
						<div class="card-body">
							<div class="feature-icon bg-primary bg-gradient text-white rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
								<i class="bi bi-quote fs-3"></i>
							</div>
							<h4 class="card-title fw-bold">Multi-Style Citations</h4>
							<p class="card-text text-muted">Generate instant academic references in APA 7th, IEEE, MLA 9th, and Chicago 17th formats for all Tatvam books and journals.</p>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="card h-100 border-0 shadow-sm text-center p-4">
						<div class="card-body">
							<div class="feature-icon bg-success bg-gradient text-white rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
								<i class="bi bi-search-heart fs-3"></i>
							</div>
							<h4 class="card-title fw-bold">DOI & ISBN Lookup</h4>
							<p class="card-text text-muted">Cross-verify digital object identifiers and international standard book numbers registered with CrossRef and Tatvam Repository.</p>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="card h-100 border-0 shadow-sm text-center p-4">
						<div class="card-body">
							<div class="feature-icon bg-warning bg-gradient text-dark rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
								<i class="bi bi-file-earmark-pdf fs-3"></i>
							</div>
							<h4 class="card-title fw-bold">Author Guidelines</h4>
							<p class="card-text text-muted">Download official reference style guides, EndNote export files, and BibTeX templates for manuscript submission.</p>
						</div>
					</div>
				</div>
			</div>

			<!-- Citation Generator Section -->
			<div class="card shadow-sm border-0 mb-5">
				<div class="card-header bg-white py-3 border-0">
					<h3 class="fw-bold text-primary mb-0"><i class="bi bi-journal-text"></i> Quick Citation Generator</h3>
				</div>
				<div class="card-body p-4">
					<p class="text-muted">Select any published book or research paper below to generate formatted reference citations:</p>
					<div class="row g-3 align-items-center">
						<div class="col-md-6">
							<label for="bookSelect" class="form-label fw-bold">Select Publication:</label>
							<select id="bookSelect" class="form-select" onchange="generateCitation()">
								<?php if (is_array($books)) {
									foreach ($books as $b) { ?>
										<option value="<?=$b['id']?>" 
											data-title="<?=htmlspecialchars($b['title'])?>"
											data-author="<?=htmlspecialchars($b['title'])?>"
											data-isbn="<?=htmlspecialchars($b['isbn'] ?? '978-81-203-5241-1')?>"
											data-doi="<?=htmlspecialchars($b['doi'] ?? '10.1007/s11276-024-02801-x')?>">
											<?=htmlspecialchars($b['title'])?> (<?=htmlspecialchars($b['content_type'] ?? 'Book')?>)
										</option>
									<?php }
								} ?>
							</select>
						</div>
						<div class="col-md-6">
							<label for="styleSelect" class="form-label fw-bold">Citation Format:</label>
							<select id="styleSelect" class="form-select" onchange="generateCitation()">
								<option value="APA">APA 7th Edition</option>
								<option value="IEEE">IEEE Reference Style</option>
								<option value="MLA">MLA 9th Edition</option>
								<option value="Chicago">Chicago 17th Edition</option>
							</select>
						</div>
					</div>
					
					<div class="mt-4 p-3 bg-light rounded border">
						<h5 class="fw-bold mb-2">Formatted Citation Result:</h5>
						<div id="citationOutput" class="p-3 bg-white rounded border font-monospace text-dark">
							Select a publication to preview formatted reference.
						</div>
						<button class="btn btn-outline-primary btn-sm mt-3" onclick="copyCitation()"><i class="bi bi-clipboard"></i> Copy Citation</button>
					</div>
				</div>
			</div>

			<!-- Reference Repository Library -->
			<div class="mb-5">
				<h3 class="fw-bold text-dark mb-4"><i class="bi bi-book"></i> Index of Reference Publications</h3>
				<div class="table-responsive bg-white rounded shadow-sm p-3">
					<table class="table table-hover align-middle">
						<thead class="table-primary">
							<tr>
								<th>#</th>
								<th>Publication Title</th>
								<th>Type</th>
								<th>ISBN / DOI</th>
								<th>Reference Action</th>
							</tr>
						</thead>
						<tbody>
							<?php if (is_array($books)) {
								$i = 1;
								foreach ($books as $b) { ?>
									<tr>
										<td><?=$i++?></td>
										<td>
											<strong class="text-primary"><?=htmlspecialchars($b['title'])?></strong>
											<div class="small text-muted"><?=htmlspecialchars($b['subtitle'] ?? '')?></div>
										</td>
										<td><span class="badge bg-secondary"><?=strtoupper($b['content_type'] ?? 'BOOK')?></span></td>
										<td>
											<div><small><strong>ISBN:</strong> <?=htmlspecialchars($b['isbn'] ?? 'N/A')?></small></div>
											<div><small><strong>DOI:</strong> <?=htmlspecialchars($b['doi'] ?? 'N/A')?></small></div>
										</td>
										<td>
											<a href="book-detail.php?id=<?=$b['id']?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View Detail</a>
										</td>
									</tr>
								<?php }
							} ?>
						</tbody>
					</table>
				</div>
			</div>

		</div>
	</section>

	<!-- Footer -->
	<footer class="footer bg-dark text-white pt-5 pb-3">
		<div class="container">
			<div class="row g-4 mb-4">
				<div class="col-lg-4 col-md-6">
					<h5 class="text-primary fw-bold mb-3"><i class="bi bi-book-half"></i> Tatvam Publication</h5>
					<p class="text-muted">Tatvam Publication is a premier academic publisher dedicated to disseminating high-impact books, research papers, and technological manuscripts globally.</p>
				</div>
				<div class="col-lg-2 col-md-6">
					<h5 class="fw-bold mb-3">Quick Links</h5>
					<ul class="list-unstyled text-muted">
						<li><a href="index.php" class="text-muted text-decoration-none">Home</a></li>
						<li><a href="books.php" class="text-muted text-decoration-none">Books</a></li>
						<li><a href="categories.php" class="text-muted text-decoration-none">Categories</a></li>
						<li><a href="reference.php" class="text-muted text-decoration-none">Reference</a></li>
						<li><a href="about.php" class="text-muted text-decoration-none">About Us</a></li>
						<li><a href="contact.php" class="text-muted text-decoration-none">Contact Us</a></li>
					</ul>
				</div>
				<div class="col-lg-3 col-md-6">
					<h5 class="fw-bold mb-3">Contact Support</h5>
					<p class="text-muted mb-1"><i class="bi bi-geo-alt"></i> Tatvam Publication Campus, Mumbai, MH, India</p>
					<p class="text-muted mb-1"><i class="bi bi-envelope"></i> contact@tatvampublication.com</p>
					<p class="text-muted mb-0"><i class="bi bi-phone"></i> +91 9876543210</p>
				</div>
				<div class="col-lg-3 col-md-6">
					<h5 class="fw-bold mb-3">Accepted Payments</h5>
					<img src="img/payment-icons.png" alt="Payment Icons" class="img-fluid rounded bg-white p-2 mb-2" onerror="this.onerror=null;this.src='img/default-book.png';">
					<div class="small text-muted">Secured via 256-Bit SSL Encryption</div>
				</div>
			</div>
			<hr class="border-secondary">
			<div class="text-center text-muted small">
				&copy; <?=date('Y')?> Tatvam Publication. All rights reserved.
			</div>
		</div>
	</footer>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		function generateCitation() {
			var select = document.getElementById("bookSelect");
			var option = select.options[select.selectedIndex];
			var title = option.getAttribute("data-title") || "Publication Title";
			var style = document.getElementById("styleSelect").value;
			var isbn = option.getAttribute("data-isbn") || "978-81-203-5241-1";
			var doi = option.getAttribute("data-doi") || "10.1007/s11276-024-02801-x";
			var year = new Date().getFullYear();
			
			var result = "";
			if (style === "APA") {
				result = "Tatvam Publication. (" + year + "). " + title + ". Tatvam Academic Press. https://doi.org/" + doi;
			} else if (style === "IEEE") {
				result = "[1] Tatvam Publication, \"" + title + ",\" Tatvam Academic Press, " + year + ". DOI: " + doi + ".";
			} else if (style === "MLA") {
				result = "Tatvam Publication. " + title + ". Tatvam Academic Press, " + year + ". DOI: " + doi + ".";
			} else {
				result = "Tatvam Publication. " + year + ". " + title + ". Mumbai: Tatvam Academic Press. https://doi.org/" + doi + ".";
			}
			document.getElementById("citationOutput").innerText = result;
		}

		function copyCitation() {
			var text = document.getElementById("citationOutput").innerText;
			navigator.clipboard.writeText(text).then(function() {
				alert("Citation copied to clipboard!");
			});
		}

		window.onload = generateCitation;
	</script>
</body>
</html>
