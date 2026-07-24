<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: user-login.php");
    exit;
}

include "db_conn.php";
include "php/func-order.php";

$user_id = $_SESSION['user_id'];
$orders = get_user_orders($conn, $user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Online Bookstore</title>
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
                <span class="brand-text">Tatvam <span class="text-primary">Publication</span></span>
            </a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-primary me-2"><i class="bi bi-house"></i> Home</a>
                <a href="books.php?type=research_paper" class="btn btn-outline-secondary me-2"><i class="bi bi-journal-text"></i> Research Papers</a>
                <a href="authors.php" class="btn btn-outline-secondary me-2"><i class="bi bi-people"></i> Authors</a>
                <a href="cart.php" class="btn btn-outline-secondary me-2"><i class="bi bi-cart"></i> Cart</a>
                <a href="user-profile.php" class="btn btn-outline-secondary"><i class="bi bi-person"></i> Profile</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="mb-4"><i class="bi bi-bag-check"></i> My Orders</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> You haven't placed any orders yet.
                <a href="index.php" class="alert-link">Start shopping</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Date</th>
                            <th>Books</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Action</th>
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
                            <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                            <td><?= $order['book_count'] ?> book(s)</td>
                            <td><strong>₹<?= number_format($order['total_amount'], 2) ?></strong></td>
                            <td>
                                <span class="badge bg-<?= $status_class ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="order-details.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
