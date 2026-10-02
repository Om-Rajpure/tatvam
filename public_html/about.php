<?php 
session_start();
include "db_conn.php";

$cart_count = 0;
if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
    include_once "php/func-cart.php";
    $cart_count = get_cart_count($conn, $_SESSION['user_id']);
}

$current_page = 'about.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us — Tatvam Publication</title>
    <meta name="description" content="Learn more about Tatvam Publication, our mission, academic peer review standards, and scholarly publishing values.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "php/navbar.php"; ?>

<!-- Page Header -->
<section class="page-header text-center">
    <div class="container">
        <nav aria-label="breadcrumb" class="d-flex justify-content-center mb-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">About Us</li>
            </ol>
        </nav>
        <h1 class="mb-2">About <span class="text-primary">Tatvam Publication</span></h1>
        <p class="lead text-muted mx-auto" style="max-width: 600px;">
            A premier academic publishing house dedicated to advancing scholarly research, scientific excellence, and global knowledge dissemination.
        </p>
    </div>
</section>

<!-- About Content -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center mb-5 g-5">
            <div class="col-lg-6">
                <span class="badge bg-primary px-3 py-2 rounded-pill mb-3" style="font-size: 11.5px; letter-spacing: 0.05em;">OUR MISSION & VISION</span>
                <h2 class="fw-bold mb-4">Empowering Researchers & Disseminating Scholarly Knowledge</h2>
                <p class="text-secondary mb-3">
                    Tatvam Publication is committed to bridging the gap between rigorous academic inquiry and global accessibility. We curate, peer-review, and publish cutting-edge research across science, engineering, business, and humanities.
                </p>
                <p class="text-secondary">
                    Our platform empowers scholars, professors, and industry experts to publish high-impact monographs, conference volumes, and open-access research papers with persistent digital indexing and global distribution.
                </p>
                <div class="d-flex gap-3 mt-4">
                    <a href="submit-book.php" class="btn btn-primary px-4 py-2">
                        <i class="bi bi-upload me-1"></i> Submit Work
                    </a>
                    <a href="books.php" class="btn btn-outline-primary px-4 py-2">
                        <i class="bi bi-book me-1"></i> Browse Catalogue
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=800&auto=format&fit=crop&q=80" alt="Academic Library" class="img-fluid" style="height: 380px; object-fit: cover;">
                </div>
            </div>
        </div>

        <div class="row align-items-center py-5 g-5">
            <div class="col-lg-6 order-lg-2">
                <span class="badge bg-secondary text-white px-3 py-2 rounded-pill mb-3" style="font-size: 11.5px; letter-spacing: 0.05em;">EDITORIAL INTEGRITY</span>
                <h2 class="fw-bold mb-4">Peer-Reviewed Excellence & Open Academic Standards</h2>
                <p class="text-secondary mb-3">
                    Every submission undergoes a multi-tier editorial evaluation and blind peer-review by subject matter experts to ensure methodological soundness, factual accuracy, and academic relevance.
                </p>
                <p class="text-secondary">
                    We adhere to international publication ethics and standards, fostering transparent, accessible scholarship for institutions, libraries, and independent researchers worldwide.
                </p>
            </div>
            <div class="col-lg-6 order-lg-1">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=800&auto=format&fit=crop&q=80" alt="Research Study" class="img-fluid" style="height: 380px; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values & Pillars -->
<section class="py-5 bg-light border-top">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Academic Authors Choose Tatvam</h2>
            <p class="text-secondary">Comprehensive publishing support designed for scholarly impact</p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 border rounded-3 bg-white text-center">
                    <div class="mx-auto mb-3 text-primary fs-1">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5 class="fw-bold">Peer-Reviewed</h5>
                    <p class="text-secondary small mb-0">Rigorous blind peer-review by leading domain experts and professors.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 border rounded-3 bg-white text-center">
                    <div class="mx-auto mb-3 text-primary fs-1">
                        <i class="bi bi-globe"></i>
                    </div>
                    <h5 class="fw-bold">Global Reach</h5>
                    <p class="text-secondary small mb-0">Distributed across international digital repositories and academic indexing networks.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 border rounded-3 bg-white text-center">
                    <div class="mx-auto mb-3 text-primary fs-1">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>
                    <h5 class="fw-bold">Instant Digital Access</h5>
                    <p class="text-secondary small mb-0">Secure high-speed digital distribution with instant watermarked downloads.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 border rounded-3 bg-white text-center">
                    <div class="mx-auto mb-3 text-primary fs-1">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h5 class="fw-bold">Author Support</h5>
                    <p class="text-secondary small mb-0">Dedicated editorial assistance from manuscript submission to indexing.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "php/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
