<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit;
}

include "db_conn.php";
include "php/func-book-request.php";

$status_filter = isset($_GET['status']) ? $_GET['status'] : null;
$requests = get_all_requests($conn, $status_filter);
$stats = get_request_stats($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Requests - Admin</title>
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
                    <a class="nav-link active" href="admin-book-requests.php"><i class="bi bi-file-earmark-text"></i> Book Requests</a>
                    <a class="nav-link" href="index.php"><i class="bi bi-house"></i> Store</a>
                    <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </nav>
            </div>

            <div class="col-md-10 admin-content p-4">
                <h2 class="mb-4"><i class="bi bi-file-earmark-text"></i> Book Publishing Requests</h2>

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
                                <i class="bi bi-list-ul display-4 text-primary mb-2"></i>
                                <h3 class="text-primary"><?= $stats['total'] ?></h3>
                                <p>Total Requests</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-center border-0">
                            <div class="card-body">
                                <i class="bi bi-clock-history display-4 text-warning mb-2"></i>
                                <h3 class="text-warning"><?= $stats['pending'] ?></h3>
                                <p>Pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-center border-0">
                            <div class="card-body">
                                <i class="bi bi-check-circle display-4 text-success mb-2"></i>
                                <h3 class="text-success"><?= ($stats['approved'] ?? 0) + ($stats['published'] ?? 0) ?></h3>
                                <p>Approved</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-center border-0">
                            <div class="card-body">
                                <i class="bi bi-book-fill display-4 text-info mb-2"></i>
                                <h3 class="text-info"><?= $stats['published'] ?? 0 ?></h3>
                                <p>Published</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-center border-0">
                            <div class="card-body">
                                <i class="bi bi-x-circle display-4 text-danger mb-2"></i>
                                <h3 class="text-danger"><?= $stats['rejected'] ?></h3>
                                <p>Rejected</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card table-card mb-4 border-0">
                    <div class="card-body">
                        <div class="btn-group" role="group">
                            <a href="admin-book-requests.php" class="btn <?= !$status_filter ? 'btn-primary' : 'btn-outline-primary' ?>">
                                <i class="bi bi-list"></i> All
                            </a>
                            <a href="admin-book-requests.php?status=pending" class="btn <?= $status_filter == 'pending' ? 'btn-warning' : 'btn-outline-warning' ?>">
                                <i class="bi bi-clock"></i> Pending
                            </a>
                            <a href="admin-book-requests.php?status=approved" class="btn <?= $status_filter == 'approved' ? 'btn-success' : 'btn-outline-success' ?>">
                                <i class="bi bi-check-circle"></i> Approved
                            </a>
                            <a href="admin-book-requests.php?status=published" class="btn <?= $status_filter == 'published' ? 'btn-info' : 'btn-outline-info' ?>">
                                <i class="bi bi-book-fill"></i> Published
                            </a>
                            <a href="admin-book-requests.php?status=rejected" class="btn <?= $status_filter == 'rejected' ? 'btn-danger' : 'btn-outline-danger' ?>">
                                <i class="bi bi-x-circle"></i> Rejected
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Requests Table -->
                <div class="card table-card border-0">
                    <div class="card-body">
                        <?php if (empty($requests)): ?>
                            <div class="alert alert-info">No requests found.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Cover</th>
                                            <th>Book Details</th>
                                            <th>Author</th>
                                            <th>Submitted By</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($requests as $request): 
                                            $status_class = [
                                                'pending' => 'warning',
                                                'approved' => 'info',
                                                'published' => 'success',
                                                'rejected' => 'danger'
                                            ][$request['status']];
                                        ?>
                                        <tr>
                                            <td>
                                                <img src="uploads/cover/<?= $request['cover'] ?>" width="50" class="rounded">
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($request['title']) ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($request['category_name']) ?></small><br>
                                                <small class="text-primary">₹<?= number_format($request['price'], 2) ?></small>
                                            </td>
                                            <td><?= htmlspecialchars($request['author_name']) ?></td>
                                            <td>
                                                <?= htmlspecialchars($request['full_name']) ?><br>
                                                <small class="text-muted"><?= htmlspecialchars($request['email']) ?></small>
                                            </td>
                                            <td><?= date('d M Y', strtotime($request['created_at'])) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $status_class ?>">
                                                    <?= ucfirst($request['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-info" onclick="viewDetails(<?= $request['id'] ?>)">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <?php if ($request['status'] == 'pending'): ?>
                                                    <button class="btn btn-success" onclick="approveRequest(<?= $request['id'] ?>)">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                    <button class="btn btn-danger" onclick="rejectRequest(<?= $request['id'] ?>)">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                    <?php elseif ($request['status'] == 'approved' && $request['payment_status'] == 'paid'): ?>
                                                    <button class="btn btn-primary" onclick="verifyPayment(<?= $request['id'] ?>)">
                                                        <i class="bi bi-check-circle"></i> Verify
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
        function viewDetails(id) {
            window.location.href = 'admin-view-request.php?id=' + id;
        }

        function approveRequest(id) {
            const fee = prompt('Enter publishing fee (₹) - Enter 0 for free publishing:');
            if (fee !== null) {
                const notes = prompt('Enter approval notes (optional):');
                window.location.href = 'php/approve-book-request.php?id=' + id + '&fee=' + fee + '&notes=' + encodeURIComponent(notes || '');
            }
        }

        function rejectRequest(id) {
            const notes = prompt('Enter rejection reason:');
            if (notes) {
                window.location.href = 'php/reject-book-request.php?id=' + id + '&notes=' + encodeURIComponent(notes);
            }
        }

        function verifyPayment(id) {
            if (confirm('Verify payment and publish book?')) {
                window.location.href = 'php/verify-publishing-payment.php?id=' + id;
            }
        }
    </script>
</body>
</html>
