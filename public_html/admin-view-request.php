<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: admin-book-requests.php");
    exit;
}

include "db_conn.php";
include "php/func-book-request.php";

$request_id = intval($_GET['id']);
$request = get_request($conn, $request_id);
if (!$request) {
    header("Location: admin-book-requests.php?error=Request+not+found");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Details - Admin</title>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Request Details</h2>
                    <a href="admin-book-requests.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Requests</a>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php 
                    $status_class = [
                        'pending' => 'warning',
                        'approved' => 'info',
                        'published' => 'success',
                        'rejected' => 'danger'
                    ][$request['status']];
                ?>
                <div class="mb-4">
                    <span class="badge bg-<?= $status_class ?> fs-5 px-3 py-2">Status: <?= ucfirst($request['status']) ?></span>
                </div>

                <div class="row g-4">
                    <!-- Left column -->
                    <div class="col-md-5">
                        <img src="uploads/cover/<?= htmlspecialchars($request['cover']) ?>" class="img-fluid rounded shadow w-100 mb-3">
                        <a href="uploads/files/<?= htmlspecialchars($request['file']) ?>" target="_blank" class="btn btn-outline-primary btn-sm mt-2 d-block"><i class="bi bi-file-pdf"></i> View Full PDF</a>
                        <?php if (!empty($request['preview_file'])): ?>
                        <a href="uploads/files/<?= htmlspecialchars($request['preview_file']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm mt-2 d-block"><i class="bi bi-eye"></i> View Preview PDF</a>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Right column -->
                    <div class="col-md-7">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body">
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Title</dt>
                                    <dd class="col-sm-8 fw-semibold"><?= htmlspecialchars($request['title']) ?></dd>
                                    
                                    <dt class="col-sm-4">Type</dt>
                                    <dd class="col-sm-8">
                                        <span class="badge <?= (isset($request['content_type']) && $request['content_type']=='research_paper') ? 'bg-info text-dark' : 'bg-primary' ?>">
                                            <?= (isset($request['content_type']) && $request['content_type']=='research_paper') ? 'Research Paper' : 'Book' ?>
                                        </span>
                                    </dd>
                                    
                                    <dt class="col-sm-4">Category</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($request['category_name']) ?></dd>
                                    
                                    <dt class="col-sm-4">Price</dt>
                                    <dd class="col-sm-8">₹<?= number_format($request['price'], 2) ?></dd>
                                    
                                    <?php if (isset($request['content_type']) && $request['content_type'] == 'research_paper'): ?>
                                        <?php if (!empty($request['doi'])): ?>
                                        <dt class="col-sm-4">DOI</dt>
                                        <dd class="col-sm-8"><?= htmlspecialchars($request['doi']) ?></dd>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if (!empty($request['isbn'])): ?>
                                        <dt class="col-sm-4">ISBN</dt>
                                        <dd class="col-sm-8"><?= htmlspecialchars($request['isbn']) ?></dd>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($request['pages'])): ?>
                                    <dt class="col-sm-4">Pages</dt>
                                    <dd class="col-sm-8"><?= intval($request['pages']) ?></dd>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($request['format'])): ?>
                                    <dt class="col-sm-4">Format</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($request['format']) ?></dd>
                                    <?php endif; ?>
                                    
                                    <dt class="col-sm-4">Author Name</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($request['author_name']) ?></dd>
                                    
                                    <dt class="col-sm-4">Submitted By</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($request['full_name']) ?> (<?= htmlspecialchars($request['email']) ?>)</dd>
                                    
                                    <dt class="col-sm-4">Submission Date</dt>
                                    <dd class="col-sm-8"><?= date('d M Y h:i A', strtotime($request['created_at'])) ?></dd>
                                    
                                    <?php if (!empty($request['admin_notes'])): ?>
                                    <dt class="col-sm-4">Admin Notes</dt>
                                    <dd class="col-sm-8"><?= nl2br(htmlspecialchars($request['admin_notes'])) ?></dd>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($request['scan_report']) && !empty($request['scan_report'])): ?>
                                    <dt class="col-sm-4 text-warning">Scanner Report</dt>
                                    <dd class="col-sm-8"><?= nl2br(htmlspecialchars($request['scan_report'])) ?></dd>
                                    <?php endif; ?>
                                </dl>
                                
                                <hr>
                                
                                <h6>Description</h6>
                                <p class="text-muted"><?= nl2br(htmlspecialchars($request['description'])) ?></p>
                            </div>
                        </div>
                        
                        <?php if ($request['status'] == 'pending'): ?>
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-check-circle"></i> Approve Request</h5>
                            </div>
                            <div class="card-body">
                                <form action="php/approve-book-request.php" method="GET">
                                    <input type="hidden" name="id" value="<?= $request_id ?>">
                                    <input type="hidden" name="from_detail" value="1">
                                    <div class="mb-3">
                                        <label class="form-label">Publishing Fee (₹) <span class="text-muted small">— Enter 0 for free publishing</span></label>
                                        <input type="number" step="0.01" min="0" class="form-control" name="fee" value="0" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Admin Notes (optional)</label>
                                        <textarea class="form-control" name="notes" rows="3" placeholder="Approval notes for the author..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-circle"></i> Approve & Publish</button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0"><i class="bi bi-x-circle"></i> Reject Request</h5>
                            </div>
                            <div class="card-body">
                                <form action="php/reject-book-request.php" method="GET">
                                    <input type="hidden" name="id" value="<?= $request_id ?>">
                                    <input type="hidden" name="from_detail" value="1">
                                    <div class="mb-3">
                                        <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="notes" rows="3" placeholder="Explain to the author why the request is being rejected..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100"><i class="bi bi-x-circle"></i> Reject Request</button>
                                </form>
                            </div>
                        </div>
                        <?php elseif ($request['status'] == 'approved' && isset($request['payment_status']) && $request['payment_status'] == 'paid'): ?>
                            <a href="php/verify-publishing-payment.php?id=<?= $request_id ?>" class="btn btn-primary w-100"><i class="bi bi-check-circle"></i> Verify Payment & Publish</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
