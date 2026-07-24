<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit;
}

include "db_conn.php";
include "php/func-payment.php";

$pending_payments = get_pending_payments($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Payments - Admin</title>
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
                    <a class="nav-link active" href="admin-verify-payments.php"><i class="bi bi-credit-card-2-front"></i> Verify Payments</a>
                    <a class="nav-link" href="index.php"><i class="bi bi-house"></i> Store</a>
                    <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </nav>
            </div>

            <div class="col-md-10 admin-content p-4">
                <h2 class="mb-4"><i class="bi bi-credit-card-2-front"></i> Payment Verification</h2>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (empty($pending_payments)): ?>
                    <div class="card table-card border-0 text-center py-5">
                        <div class="card-body">
                            <i class="bi bi-check-circle-fill display-1 text-success mb-3"></i>
                            <h4>All Caught Up!</h4>
                            <p class="text-muted">No pending payments to verify at the moment.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card table-card border-0">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Customer</th>
                                            <th>Amount</th>
                                            <th>Transaction ID</th>
                                            <th>UPI ID</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pending_payments as $payment): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($payment['order_number']) ?></strong></td>
                                            <td>
                                                <?= htmlspecialchars($payment['full_name']) ?><br>
                                                <small class="text-muted"><?= htmlspecialchars($payment['email']) ?></small><br>
                                                <small class="text-muted"><?= htmlspecialchars($payment['phone']) ?></small>
                                            </td>
                                            <td><strong>₹<?= number_format($payment['amount'], 2) ?></strong></td>
                                            <td><code><?= htmlspecialchars($payment['transaction_id']) ?></code></td>
                                            <td><?= htmlspecialchars($payment['upi_id'] ?: 'N/A') ?></td>
                                            <td><?= date('d M Y, h:i A', strtotime($payment['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-success" 
                                                            onclick="verifyPayment(<?= $payment['id'] ?>, 'verify')" 
                                                            title="Verify Payment">
                                                        <i class="bi bi-check-circle"></i> Verify
                                                    </button>
                                                    <button class="btn btn-danger" 
                                                            onclick="verifyPayment(<?= $payment['id'] ?>, 'reject')" 
                                                            title="Reject Payment">
                                                        <i class="bi bi-x-circle"></i> Reject
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function verifyPayment(paymentId, action) {
            const message = action === 'verify' 
                ? 'Are you sure you want to verify this payment?' 
                : 'Are you sure you want to reject this payment?';
            
            if (confirm(message)) {
                window.location.href = 'php/verify-payment.php?id=' + paymentId + '&action=' + action;
            }
        }
    </script>
</body>
</html>
