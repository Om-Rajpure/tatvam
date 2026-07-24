<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'customer') {
    header("Location: user-login.php?redirect=author-registration.php");
    exit;
}
include "db_conn.php";
include "php/func-author.php";
// If user already has an author profile, go to submit
$existing = function_exists('get_author_by_user_id') ? get_author_by_user_id($conn, $_SESSION['user_id']) : 0;
if ($existing) {
    header("Location: submit-book.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Author Registration - Tatvam Publication</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="bi bi-book-half text-primary me-2"></i>
                <span class="fw-bold">Tatvam <span class="text-primary">Publication</span></span>
            </a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-secondary btn-sm me-2">Home</a>
                <a href="my-requests.php" class="btn btn-outline-secondary btn-sm me-2">My Requests</a>
                <a href="user-profile.php" class="btn btn-outline-primary btn-sm">Profile</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?=htmlspecialchars($_GET['error'])?></div>
                <?php endif; ?>
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?=htmlspecialchars($_GET['success'])?></div>
                <?php endif; ?>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> This is a one-time setup. Once your author profile is created, you can publish books and research papers directly.
                </div>

                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h4 class="mb-0"><i class="bi bi-person-plus"></i> Create Author Profile</h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="php/create-author-profile.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required value="<?=htmlspecialchars($_SESSION['user_name'] ?? '')?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Author Photo</label>
                                <input type="file" class="form-control" name="photo" accept="image/*">
                                <div class="form-text">Upload a professional photo (optional)</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">About / Bio <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="about" rows="5" required></textarea>
                                <div class="form-text">Tell readers about yourself, your background, and your work</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Qualification <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="qualification" required placeholder="e.g. PhD in Computer Science">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Designation</label>
                                    <input type="text" class="form-control" name="designation" placeholder="e.g. Professor">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Organization</label>
                                    <input type="text" class="form-control" name="organization" placeholder="e.g. IIT Mumbai">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Contact Info (Internal only — not shown to public)</label>
                                <input type="text" class="form-control" name="contact" placeholder="Email or Phone">
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">Create My Author Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
