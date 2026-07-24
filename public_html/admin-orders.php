<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit;
}

include "db_conn.php";
include "php/func-order.php";

$status_filter = isset($_GET['status']) ? $_GET['status'] : null;
$orders = get_all_orders($conn, $status_filter);
$stats = get_order_stats($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin</title>
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
                    <a class="nav-link active" href="admin-orders.php"><i class="bi bi-bag-check"></i> Orders</a>
                    <a class="nav-link" href="admin-verify-payments.php"><i class="bi bi-credit-card-2-front"></i> Verify Payments</a>
                    <a class="nav-link" href="index.php"><i class="bi bi-house"></i> Store</a>
                    <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </nav>
            </div>

            <div class="col-md-10 admin-content p-4">
                <h2 class="mb-4"><i class="bi bi-bag-check"></i> Order Management</h2>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card text-center border-0">
                            <div class="card-body">
                                <i class="bi bi-bag-check display-4 text-primary mb-2"></i>
                                <h3 class="text-primary"><?= $stats['total_orders'] ?></h3>
                                <p>Total Orders</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-center border-0">
                            <div class="card-body">
                                <i class="bi bi-clock-history display-4 text-warning mb-2"></i>
                                <h3 class="text-warning"><?= $stats['pending_orders'] ?></h3>
                                <p>Pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-center border-0">
                            <div class="card-body">
                                <i class="bi bi-check-circle display-4 text-success mb-2"></i>
                                <h3 class="text-success"><?= $stats['completed_orders'] ?></h3>
                                <p>Completed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-center border-0">
                            <div class="card-body">
                                <i class="bi bi-currency-rupee display-4 text-success mb-2"></i>
                                <h3 class="text-success">₹<?= number_format($stats['completed_revenue'], 0) ?></h3>
                                <p>Revenue</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card table-card mb-4 border-0">
                    <div class="card-body">
                        <div class="btn-group" role="group">
                            <a href="admin-orders.php" class="btn <?= !$status_filter ? 'btn-primary' : 'btn-outline-primary' ?>">
                                <i class="bi bi-list"></i> All Orders
                            </a>
                            <a href="admin-orders.php?status=pending" class="btn <?= $status_filter == 'pending' ? 'btn-warning' : 'btn-outline-warning' ?>">
                                <i class="bi bi-clock"></i> Pending
                            </a>
                            <a href="admin-orders.php?status=completed" class="btn <?= $status_filter == 'completed' ? 'btn-success' : 'btn-outline-success' ?>">
                                <i class="bi bi-check-circle"></i> Completed
                            </a>
                            <a href="admin-orders.php?status=failed" class="btn <?= $status_filter == 'failed' ? 'btn-danger' : 'btn-outline-danger' ?>">
                                <i class="bi bi-x-circle"></i> Failed
                            </a>
                            <a href="admin-orders.php?status=refunded" class="btn <?= $status_filter == 'refunded' ? 'btn-info' : 'btn-outline-info' ?>">
                                <i class="bi bi-arrow-counterclockwise"></i> Refunded
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="card table-card border-0">
                    <div class="card-body">
                        <?php if (empty($orders)): ?>
                            <div class="alert alert-info">No orders found.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Customer</th>
                                            <th>Date</th>
                                            <th>Books</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): 
                                            $status_class = [
                                                'pending' => 'warning',
                                                'completed' => 'success',
                                                'failed' => 'danger',
                                                'refunded' => 'info'
                                            ][$order['status']];
                                        ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($order['order_number']) ?></strong></td>
                                            <td>
                                                <?= htmlspecialchars($order['full_name']) ?><br>
                                                <small class="text-muted"><?= htmlspecialchars($order['email']) ?></small>
                                            </td>
                                            <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                            <td><?= $order['book_count'] ?></td>
                                            <td><strong>₹<?= number_format($order['total_amount'], 2) ?></strong></td>
                                            <td>
                                                <span class="badge bg-<?= $status_class ?>">
                                                    <?= ucfirst($order['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="admin-order-details.php?id=<?= $order['id'] ?>" 
                                                       class="btn btn-primary" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <?php if ($order['status'] == 'pending'): ?>
                                                    <button class="btn btn-success" 
                                                            onclick="updateStatus(<?= $order['id'] ?>, 'completed')" 
                                                            title="Mark Completed">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                    <button class="btn btn-danger" 
                                                            onclick="updateStatus(<?= $order['id'] ?>, 'failed')" 
                                                            title="Mark Failed">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateStatus(orderId, status) {
            if (confirm('Are you sure you want to update this order status?')) {
                window.location.href = 'php/update-order-status.php?id=' + orderId + '&status=' + status;
            }
        }
    </script>
</body>
</html>
