<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$id = intval($_GET['id']);
include "db_conn.php";
include "php/func-author.php";
$author = get_author($conn, $id);

if (!$author) {
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Author - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 admin-sidebar p-0">
                <div class="sidebar-header">
                    <h4><i class="bi bi-shield-check"></i> Admin</h4>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="admin.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    <a class="nav-link" href="add-book.php"><i class="bi bi-book"></i> Add Book</a>
                    <a class="nav-link" href="add-category.php"><i class="bi bi-folder"></i> Add Category</a>
                    <a class="nav-link" href="add-author.php"><i class="bi bi-person"></i> Add Author</a>
                    <a class="nav-link" href="admin-orders.php"><i class="bi bi-bag-check"></i> Orders</a>
                    <a class="nav-link" href="admin-verify-payments.php"><i class="bi bi-credit-card-2-front"></i> Verify Payments</a>
                    <a class="nav-link" href="admin-book-requests.php"><i class="bi bi-file-earmark-text"></i> Book Requests</a>
                    <a class="nav-link" href="index.php"><i class="bi bi-house"></i> Store</a>
                    <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </nav>
            </div>

            <div class="col-md-10 admin-content p-4">
                <div class="card shadow border-0" style="max-width: 50rem; margin: 0 auto;">
                    <div class="card-body p-4">
                        <h2 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Author</h2>

                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?= htmlspecialchars($_GET['error']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <?= htmlspecialchars($_GET['success']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="php/edit-author.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $author['id'] ?>">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Author Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($author['name']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Replace Photo (leave empty to keep current)</label>
                                <input type="file" class="form-control" name="photo" accept="image/*">
                                <?php if (!empty($author['photo'])): ?>
                                    <div class="mt-2">
                                        <img src="uploads/author_photos/<?= $author['photo'] ?>" width="100" class="rounded border">
                                        <small class="d-block text-muted mt-1">Current photo</small>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">About / Bio</label>
                                <textarea class="form-control" name="about" rows="4"><?= htmlspecialchars($author['about'] ?? '') ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Qualification</label>
                                <input type="text" class="form-control" name="qualification" value="<?= htmlspecialchars($author['qualification'] ?? '') ?>">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Designation</label>
                                    <input type="text" class="form-control" name="designation" value="<?= htmlspecialchars($author['designation'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Organization</label>
                                    <input type="text" class="form-control" name="organization" value="<?= htmlspecialchars($author['organization'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Contact Info</label>
                                <input type="text" class="form-control" name="contact" value="<?= htmlspecialchars($author['contact'] ?? '') ?>">
                            </div>

                            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Author</button>
                            <a href="admin.php" class="btn btn-secondary ms-2">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>