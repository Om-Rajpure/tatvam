<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: user-login.php");
    exit;
}

include "db_conn.php";
include "php/func-order.php";

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!user_owns_order($conn, $user_id, $order_id)) {
    header("Location: my-orders.php");
    exit;
}

$order = get_order($conn, $order_id);
$order_items = get_order_items($conn, $order_id);

$status_class = [
    'pending' => 'warning',
    'completed' => 'success',
    'failed' => 'danger',
    'refunded' => 'info'
][$order['status']];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Online Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-book-half text-primary"></i>
                <span class="brand-text">Book<span class="text-primary">Hub</span></span>
            </a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-primary me-2"><i class="bi bi-house"></i> Home</a>
                <a href="my-orders.php" class="btn btn-outline-secondary me-2"><i class="bi bi-bag-check"></i> My Orders</a>
                <a href="user-profile.php" class="btn btn-outline-secondary"><i class="bi bi-person"></i> Profile</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-receipt"></i> Order Details</h2>
            <a href="my-orders.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Orders
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Order Number</label>
                                <p class="mb-0"><strong><?= htmlspecialchars($order['order_number']) ?></strong></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Order Date</label>
                                <p class="mb-0"><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Status</label>
                                <p class="mb-0">
                                    <span class="badge bg-<?= $status_class ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Total Amount</label>
                                <p class="mb-0"><strong class="text-primary">₹<?= number_format($order['total_amount'], 2) ?></strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($order_items as $item): ?>
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <img src="uploads/cover/<?= htmlspecialchars($item['cover']) ?>" 
                                 alt="<?= htmlspecialchars($item['title']) ?>" 
                                 style="width: 60px; height: 80px; object-fit: cover;" 
                                 class="me-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><?= htmlspecialchars($item['title']) ?></h6>
                                <small class="text-muted">by <?= htmlspecialchars($item['author_name']) ?></small>
                            </div>
                            <div class="text-end">
                                <strong>₹<?= number_format($item['price'], 2) ?></strong>
                                <?php if ($order['status'] == 'completed'): ?>
                                    <br>
                                    <a href="download.php?book_id=<?= $item['book_id'] ?>" 
                                       class="btn btn-sm btn-success mt-2">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Customer Details</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Name:</strong><br><?= htmlspecialchars($order['full_name']) ?></p>
                        <p class="mb-2"><strong>Email:</strong><br><?= htmlspecialchars($order['email']) ?></p>
                        <p class="mb-0"><strong>Phone:</strong><br><?= htmlspecialchars($order['phone']) ?></p>
                    </div>
                </div>

                <?php if ($order['status'] == 'pending'): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-clock-history"></i> Payment verification pending. Your books will be available after admin approval.
                </div>
                <?php 
                $_SESSION['order_id'] = $order['id'];
                ?>
                <a href="payment.php" class="btn btn-primary w-100 mb-2">
                    <i class="bi bi-credit-card"></i> Complete Payment
                </a>
                <?php elseif ($order['status'] == 'completed'): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> Order completed! You can download your books now.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
