<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'customer') {
    header("Location: user-login.php?redirect=checkout.php");
    exit;
}

include "db_conn.php";
include "php/func-cart.php";
include "php/func-user.php";

$user_id = $_SESSION['user_id'];
$cart_items = get_cart_items($conn, $user_id);
$cart_total = get_cart_total($conn, $user_id);
$user = get_user($conn, $user_id);

if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Online Bookstore</title>
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
                <a href="user-profile.php" class="btn btn-outline-secondary"><i class="bi bi-person"></i> Profile</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="mb-4"><i class="bi bi-cart-check"></i> Checkout</h2>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Customer Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>Full Name:</strong></label>
                                <p><?= htmlspecialchars($user['full_name']) ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>Email:</strong></label>
                                <p><?= htmlspecialchars($user['email']) ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><strong>Phone:</strong></label>
                                <p><?= htmlspecialchars($user['phone']) ?></p>
                            </div>
                        </div>
                        <a href="user-profile.php" class="btn btn-sm btn-outline-primary">Update Profile</a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($cart_items as $item): ?>
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
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Items (<?= count($cart_items) ?>):</span>
                            <span>₹<?= number_format($cart_total, 2) ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total Amount:</strong>
                            <strong class="text-primary">₹<?= number_format($cart_total, 2) ?></strong>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label small" for="terms">
                                I agree to the terms and conditions
                            </label>
                        </div>

                        <form action="php/create-order.php" method="POST" id="checkoutForm">
                            <button type="submit" class="btn btn-primary w-100" id="proceedBtn" disabled>
                                <i class="bi bi-credit-card"></i> Proceed to Payment
                            </button>
                        </form>

                        <a href="cart.php" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="bi bi-arrow-left"></i> Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('terms').addEventListener('change', function() {
            document.getElementById('proceedBtn').disabled = !this.checked;
        });
    </script>
</body>
</html>
