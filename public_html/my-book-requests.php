<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'customer') {
    header("Location: user-login.php");
    exit;
}

include "db_conn.php";
include "php/func-book-request.php";
$requests = get_user_requests($conn, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Book Requests - Tatvam Publication</title>
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
                <a href="submit-book.php" class="btn btn-primary me-2"><i class="bi bi-plus-circle"></i> Submit New Book</a>
                <a href="user-profile.php" class="btn btn-outline-secondary"><i class="bi bi-person"></i> Profile</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="mb-4"><i class="bi bi-list-ul"></i> My Book Requests</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($requests)): ?>
            <div class="card text-center py-5">
                <div class="card-body">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h4 class="mt-3">No Book Requests Yet</h4>
                    <p class="text-muted">Submit your first book for publishing</p>
                    <a href="submit-book.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Submit Book
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($requests as $request): 
                    $status_class = [
                        'pending' => 'warning',
                        'approved' => 'info',
                        'published' => 'success',
                        'rejected' => 'danger'
                    ][$request['status']];
                    
                    $status_display = $request['status'];
                    if ($request['status'] == 'approved') {
                        if ($request['publishing_fee'] > 0) {
                            if ($request['payment_status'] == 'paid') {
                                $status_display = 'Payment Submitted';
                            } else {
                                $status_display = 'Awaiting Payment';
                            }
                        } else {
                            $status_display = 'Approved (Free)';
                        }
                    }
                ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="row g-0">
                            <div class="col-4">
                                <img src="uploads/cover/<?= $request['cover'] ?>" class="img-fluid h-100" style="object-fit: cover;">
                            </div>
                            <div class="col-8">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($request['title']) ?></h5>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="bi bi-person"></i> <?= htmlspecialchars($request['author_name']) ?><br>
                                            <i class="bi bi-folder"></i> <?= htmlspecialchars($request['category_name']) ?><br>
                                            <i class="bi bi-currency-rupee"></i> <?= $request['price'] > 0 ? number_format($request['price'], 2) : 'Free' ?><br>
                                            <i class="bi bi-calendar"></i> <?= date('d M Y', strtotime($request['created_at'])) ?>
                                        </small>
                                    </p>
                                    <span class="badge bg-<?= $status_class ?>">
                                        <?= ucfirst($status_display) ?>
                                    </span>
                                    <?php if ($request['status'] == 'approved' && $request['publishing_fee'] > 0 && $request['payment_status'] == 'unpaid'): ?>
                                        <div class="alert alert-warning mt-2">
                                            <strong>Publishing Fee: ₹<?= number_format($request['publishing_fee'], 2) ?></strong><br>
                                            <a href="pay-publishing-fee.php?id=<?= $request['id'] ?>" class="btn btn-sm btn-primary mt-2">
                                                <i class="bi bi-credit-card"></i> Pay Now
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($request['admin_notes']): ?>
                                        <div class="alert alert-info mt-2 mb-0">
                                            <small><strong>Admin Notes:</strong><br><?= htmlspecialchars($request['admin_notes']) ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
