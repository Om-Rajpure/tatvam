<?php 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: user-login.php?redirect=cart.php");
    exit;
}

include "db_conn.php";
include "php/func-cart.php";

$user_id = $_SESSION['user_id'];
$cart_items = get_cart_items($conn, $user_id);
$cart_total = get_cart_total($conn, $user_id);
$cart_count = get_cart_count($conn, $user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Tatvam Publication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-book-half text-primary"></i>
                <span class="brand-text">Tatvam <span class="text-primary">Publication</span></span>
            </a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-primary me-2"><i class="bi bi-house"></i> Home</a>
                <a href="user-profile.php" class="btn btn-outline-secondary"><i class="bi bi-person"></i> Profile</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-cart3"></i> Shopping Cart</h2>
                    <span class="badge bg-primary rounded-pill"><?=$cart_count?> Items</span>
                </div>

                <?php if (isset($_GET['success'])) { ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i> <?=htmlspecialchars($_GET['success'])?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php } ?>
                <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle"></i> <?=htmlspecialchars($_GET['error'])?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php } ?>
                <?php if (isset($_GET['info'])) { ?>
                    <div class="alert alert-info alert-dismissible fade show">
                        <i class="bi bi-info-circle"></i> <?=htmlspecialchars($_GET['info'])?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php } ?>

                <?php if ($cart_count == 0) { ?>
                    <div class="card shadow-sm text-center py-5">
                        <div class="card-body">
                            <i class="bi bi-cart-x display-1 text-muted"></i>
                            <h4 class="mt-3">Your cart is empty</h4>
                            <p class="text-muted">Start adding books to your cart</p>
                            <a href="index.php" class="btn btn-primary mt-3">
                                <i class="bi bi-book"></i> Browse Books
                            </a>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Book</th>
                                            <th>Details</th>
                                            <th>Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cart_items as $item) { ?>
                                        <tr>
                                            <td>
                                                <img src="uploads/cover/<?=$item['cover']?>" 
                                                     alt="<?=htmlspecialchars($item['title'])?>" 
                                                     class="img-thumbnail" 
                                                     style="width: 80px; height: 100px; object-fit: cover;">
                                            </td>
                                            <td>
                                                <h6 class="mb-1"><?=htmlspecialchars($item['title'])?></h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-person"></i> <?=htmlspecialchars($item['author_name'])?>
                                                </small><br>
                                                <small class="text-muted">
                                                    <i class="bi bi-tag"></i> <?=htmlspecialchars($item['category_name'])?>
                                                </small>
                                            </td>
                                            <td class="align-middle">
                                                <strong class="text-primary">₹<?=number_format($item['price'], 2)?></strong>
                                            </td>
                                            <td class="align-middle">
                                                <a href="php/remove-from-cart.php?book_id=<?=$item['book_id']?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Remove this book from cart?')">
                                                    <i class="bi bi-trash"></i> Remove
                                                </a>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="php/clear-cart.php" 
                           class="btn btn-outline-warning"
                           onclick="return confirm('Clear all items from cart?')">
                            <i class="bi bi-trash"></i> Clear Cart
                        </a>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Continue Shopping
                        </a>
                    </div>
                <?php } ?>
            </div>

            <?php if ($cart_count > 0) { ?>
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-receipt"></i> Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Items (<?=$cart_count?>):</span>
                            <span>₹<?=number_format($cart_total, 2)?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery:</span>
                            <span class="text-success">FREE</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-primary fs-4">₹<?=number_format($cart_total, 2)?></strong>
                        </div>
                        <a href="checkout.php" class="btn btn-success w-100 btn-lg">
                            <i class="bi bi-credit-card"></i> Proceed to Checkout
                        </a>
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-shield-check"></i> Secure Checkout
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-body">
                        <h6><i class="bi bi-info-circle text-primary"></i> Why Buy from Us?</h6>
                        <ul class="list-unstyled small">
                            <li><i class="bi bi-check-circle text-success"></i> Instant Download</li>
                            <li><i class="bi bi-check-circle text-success"></i> Lifetime Access</li>
                            <li><i class="bi bi-check-circle text-success"></i> Secure Payment</li>
                            <li><i class="bi bi-check-circle text-success"></i> 24/7 Support</li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
