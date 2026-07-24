<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'customer') {
    header("Location: user-login.php");
    exit;
}

include "db_conn.php";
include "php/func-category.php";
$categories = get_all_categories($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Book for Publishing - Tatvam Publication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-book-half text-primary"></i>
                <span class="brand-text">Tatvam <span class="text-primary">Publication</span></span>
            </a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-primary me-2"><i class="bi bi-house"></i> Home</a>
                <a href="my-book-requests.php" class="btn btn-outline-secondary me-2"><i class="bi bi-list-ul"></i> My Requests</a>
                <a href="user-profile.php" class="btn btn-outline-secondary"><i class="bi bi-person"></i> Profile</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-upload"></i> Submit Book for Publishing</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
                        <?php endif; ?>
                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
                        <?php endif; ?>

                        <form action="php/submit-book-request.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Book Title *</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Author Name *</label>
                                <input type="text" class="form-control" name="author_name" required>
                                <small class="text-muted">Enter your name or pen name</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description *</label>
                                <textarea class="form-control" name="description" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Category *</label>
                                <select class="form-control" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Price (₹) *</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="price" value="0.00" required>
                                <small class="text-muted">Set 0 for free books</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Book Cover Image *</label>
                                <input type="file" class="form-control" name="cover" accept="image/*" required>
                                <small class="text-muted">Accepted: All image formats (JPG, JPEG, PNG, GIF, etc.)</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Book File (PDF) *</label>
                                <input type="file" class="form-control" name="file" accept=".pdf" required>
                                <small class="text-muted">Accepted: PDF only</small>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Your book will be reviewed by our admin team. You'll be notified once it's approved and published.
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-send"></i> Submit for Review
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
