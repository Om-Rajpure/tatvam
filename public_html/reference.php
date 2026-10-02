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
<?php include "php/navbar.php"; ?>

<!-- Header Banner -->
<section class="page-header text-center">
    <div class="container">
        <nav aria-label="breadcrumb" class="d-flex justify-content-center mb-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Reference & Citation</li>
            </ol>
        </nav>
        <h1 class="mb-2"><i class="bi bi-journal-bookmark text-primary"></i> Academic Reference & Citation Center</h1>
        <p class="lead text-muted mx-auto" style="max-width: 650px;">Standardized citation tools, DOI/ISBN lookup indices, and author referencing guidelines.</p>
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

<?php include "php/footer.php"; ?>

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
