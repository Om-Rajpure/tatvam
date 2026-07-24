<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include "db_conn.php";
include "php/func-order.php";

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$order = get_order($conn, $order_id);
$order_items = get_order_items($conn, $order_id);

if (!$order) {
    header("Location: admin-orders.php");
    exit;
}

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
    <title>Order Details - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 admin-sidebar p-0">
                <div class="sidebar-header">
                    <h4><i class="bi bi-shield-check"></i> Admin Panel</h4>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="admin.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                    <a class="nav-link" href="admin-orders.php"><i class="bi bi-bag-check"></i> Orders</a>
                    <a class="nav-link" href="admin-verify-payments.php"><i class="bi bi-credit-card-2-front"></i> Verify Payments</a>
                    <a class="nav-link" href="index.php"><i class="bi bi-house"></i> Store</a>
                    <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </nav>
            </div>

            <div class="col-md-10 admin-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-receipt"></i> Order Details</h2>
                    <a href="admin-orders.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Order Information</h5>
                                <span class="badge bg-<?= $status_class ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
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
                                        <label class="text-muted small">Total Amount</label>
                                        <p class="mb-0"><strong class="text-primary">₹<?= number_format($order['total_amount'], 2) ?></strong></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small">Last Updated</label>
                                        <p class="mb-0"><?= date('d M Y, h:i A', strtotime($order['updated_at'])) ?></p>
                                    </div>
                                </div>

                                <?php if ($order['status'] == 'pending'): ?>
                                <div class="mt-3">
                                    <button class="btn btn-success" onclick="updateStatus(<?= $order['id'] ?>, 'completed')">
                                        <i class="bi bi-check-circle"></i> Mark as Completed
                                    </button>
                                    <button class="btn btn-danger" onclick="updateStatus(<?= $order['id'] ?>, 'failed')">
                                        <i class="bi bi-x-circle"></i> Mark as Failed
                                    </button>
                                </div>
                                <?php endif; ?>
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
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Customer Details</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Name:</strong><br><?= htmlspecialchars($order['full_name']) ?></p>
                                <p class="mb-2"><strong>Email:</strong><br><?= htmlspecialchars($order['email']) ?></p>
                                <p class="mb-0"><strong>Phone:</strong><br><?= htmlspecialchars($order['phone']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateStatus(orderId, status) {
            if (confirm('Are you sure you want to update this order status to ' + status + '?')) {
                window.location.href = 'php/update-order-status.php?id=' + orderId + '&status=' + status;
            }
        }
    </script>
</body>
</html>
