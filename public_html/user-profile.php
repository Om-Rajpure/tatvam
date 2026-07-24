<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: user-login.php");
    exit;
}

include "db_conn.php";
include "php/func-user.php";

$user = get_user($conn, $_SESSION['user_id']);
$stats = get_user_stats($conn, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Tatvam Publication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-book-half text-primary"></i>
                <span class="brand-text">Tatvam <span class="text-primary">Publication</span></span>
            </a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-primary"><i class="bi bi-house"></i> Home</a>
                <a href="books.php?type=research_paper" class="btn btn-outline-secondary ms-2"><i class="bi bi-journal-text"></i> Research Papers</a>
                <a href="authors.php" class="btn btn-outline-secondary ms-2"><i class="bi bi-people"></i> Authors</a>
                <a href="my-orders.php" class="btn btn-outline-secondary ms-2"><i class="bi bi-bag-check"></i> Orders</a>
                <a href="submit-book.php" class="btn btn-success ms-2"><i class="bi bi-upload"></i> Publish Book</a>
                <a href="php/user-logout.php" class="btn btn-outline-danger ms-2"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="profile-avatar mb-3">
                            <i class="bi bi-person-circle display-1 text-primary"></i>
                        </div>
                        <h4><?=htmlspecialchars($user['full_name'])?></h4>
                        <p class="text-muted mb-3"><?=htmlspecialchars($user['email'])?></p>
                        <span class="badge bg-primary">Customer</span>
                        <hr>
                        <div class="text-start">
                            <p class="mb-2"><i class="bi bi-telephone text-primary"></i> <?=htmlspecialchars($user['phone'])?></p>
                            <p class="mb-0"><i class="bi bi-calendar text-primary"></i> Member since <?=date('M Y', strtotime($user['created_at']))?></p>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3"><i class="bi bi-graph-up"></i> Statistics</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Orders:</span>
                            <strong><?=$stats['total_orders']?></strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Total Spent:</span>
                            <strong>₹<?=number_format($stats['total_spent'], 2)?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
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

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-person-gear"></i> Update Profile</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="php/update-profile.php">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="full_name" value="<?=htmlspecialchars($user['full_name'])?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" name="phone" pattern="[0-9]{10}" value="<?=htmlspecialchars($user['phone'])?>" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Update Profile</button>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-shield-lock"></i> Change Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="php/change-password.php">
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" name="new_password" minlength="6" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" name="confirm_password" minlength="6" required>
                            </div>
                            <button type="submit" class="btn btn-warning"><i class="bi bi-key"></i> Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
