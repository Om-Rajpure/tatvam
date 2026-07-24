<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: user-login.php");
    exit;
}

include "db_conn.php";
include "php/func-order.php";
include "php/func-payment.php";

$order_id = $_SESSION['order_id'] ?? 0;
$order = get_order($conn, $order_id);
$payment = get_payment_by_order($conn, $order_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Pending - Online Bookstore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-book-half text-primary"></i>
                <span class="brand-text">Book<span class="text-primary">Hub</span></span>
            </a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow text-center">
                    <div class="card-body py-5">
                        <i class="bi bi-clock-history display-1 text-warning"></i>
                        <h2 class="mt-4">Payment Verification Pending</h2>
                        <p class="text-muted">Your transaction ID has been submitted successfully.</p>
                        
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success">
                                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="card bg-light mt-4">
                            <div class="card-body text-start">
                                <h5>Order Details</h5>
                                <p class="mb-1"><strong>Order Number:</strong> <?= htmlspecialchars($order['order_number']) ?></p>
                                <p class="mb-1"><strong>Amount:</strong> ₹<?= number_format($order['total_amount'], 2) ?></p>
                                <p class="mb-1"><strong>Transaction ID:</strong> <?= htmlspecialchars($payment['transaction_id']) ?></p>
                                <p class="mb-0"><strong>Status:</strong> <span class="badge bg-warning">Pending Verification</span></p>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4">
                            <i class="bi bi-info-circle"></i> Our team will verify your payment within 24 hours. 
                            You will receive access to download books once verified.
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <a href="my-orders.php" class="btn btn-primary">
                                <i class="bi bi-bag-check"></i> View My Orders
                            </a>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-house"></i> Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
