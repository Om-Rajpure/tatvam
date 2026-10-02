<?php 
session_start();
include "db_conn.php";

$cart_count = 0;
if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
    include_once "php/func-cart.php";
    $cart_count = get_cart_count($conn, $_SESSION['user_id']);
}

$current_page = 'contact.php';
$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name']) && !empty($_POST['email'])) {
    $sent = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Editorial Office — Tatvam Publication</title>
    <meta name="description" content="Contact the editorial, publishing, and customer support team at Tatvam Publication.">
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
                <li class="breadcrumb-item active">Contact Us</li>
            </ol>
        </nav>
        <h1 class="mb-2">Get in <span class="text-primary">Touch</span></h1>
        <p class="lead text-muted mx-auto" style="max-width: 580px;">
            Have questions about manuscript submissions, editorial review, or order queries? Our academic support desk is here to help.
        </p>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border rounded-3 p-4 text-center h-100 bg-white shadow-xs">
                    <div class="fs-1 text-primary mb-3">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Editorial Office</h5>
                    <p class="text-secondary small mb-0">
                        Tatvam Publication Headquarters<br>
                        Scholarly Publishing Wing, India
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border rounded-3 p-4 text-center h-100 bg-white shadow-xs">
                    <div class="fs-1 text-primary mb-3">
                        <i class="bi bi-envelope-open-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Email Desk</h5>
                    <p class="text-secondary small mb-0">
                        <strong>Editorial:</strong> info@tatvampublication.com<br>
                        <strong>Support:</strong> support@tatvampublication.com
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border rounded-3 p-4 text-center h-100 bg-white shadow-xs">
                    <div class="fs-1 text-primary mb-3">
                        <i class="bi bi-telephone-outbound-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Phone & WhatsApp</h5>
                    <p class="text-secondary small mb-0">
                        +91 99709 99999<br>
                        Mon – Sat: 9:00 AM – 6:00 PM IST
                    </p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border rounded-4 p-4 p-md-5 bg-white shadow-sm">
                    <h3 class="fw-bold mb-2">Send an Inquiry</h3>
                    <p class="text-secondary mb-4 small">Fill out the form below and an editorial manager will respond within 24 business hours.</p>

                    <?php if ($sent): ?>
                        <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
                            <i class="bi bi-check-circle-fill fs-5"></i>
                            <div>Thank you! Your message has been received. Our editorial team will get back to you shortly.</div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="contact.php">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="font-size: 13.5px;">Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Dr. / Prof. / Scholar Name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="font-size: 13.5px;">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="author@university.edu" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" style="font-size: 13.5px;">Subject / Area of Inquiry</label>
                                <select name="subject" class="form-select">
                                    <option value="Manuscript Submission">Manuscript Submission / Publishing Inquiry</option>
                                    <option value="Peer Review Status">Peer Review Status</option>
                                    <option value="Order & Payment">Order & Digital Access Support</option>
                                    <option value="Institutional Subscription">Institutional / Library Query</option>
                                    <option value="General">Other Feedback</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold" style="font-size: 13.5px;">Message Details</label>
                                <textarea name="message" class="form-control" rows="5" placeholder="Please provide details about your inquiry..." required></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary px-4 py-2 w-100">
                                    <i class="bi bi-send-fill me-1"></i> Send Inquiry
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include "php/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
