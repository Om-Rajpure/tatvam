<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'customer' || !isset($_SESSION['order_id'])) {
    header("Location: index.php");
    exit;
}

include "db_conn.php";
include "config/payment-config.php";
include "php/func-order.php";
include "php/func-payment.php";

$order_id = $_SESSION['order_id'];
$order = get_order($conn, $order_id);

if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    header("Location: my-orders.php");
    exit;
}

$payment = get_payment_by_order($conn, $order_id);
if (!$payment) {
    $payment_id = create_payment($conn, $order_id, $order['total_amount']);
    $payment = get_payment($conn, $payment_id);
}

$upi_link = generate_upi_link($order['order_number'], $order['total_amount']);
$qr_data = $upi_link;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Online Bookstore</title>
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
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-credit-card"></i> Complete Payment</h4>
                    </div>
                    <div class="card-body">
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
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Scan QR code or click the payment button to pay via UPI
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5>Order Details</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Order Number:</strong></td>
                                        <td><?= htmlspecialchars($order['order_number']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Amount:</strong></td>
                                        <td><strong class="text-primary">₹<?= number_format($order['total_amount'], 2) ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Payment Method:</strong></td>
                                        <td>UPI</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6 text-center">
                                <h5>Scan QR Code</h5>
                                <div id="qrcode" class="mb-3"></div>
                                <p class="small text-muted">Scan with any UPI app</p>
                            </div>
                        </div>

                        <hr>

                        <div class="text-center mb-4">
                            <h5>Pay with UPI Apps</h5>
                            <a href="<?= $upi_link ?>" class="btn btn-lg btn-primary mb-2">
                                <i class="bi bi-phone"></i> Pay Now
                            </a>
                            <p class="small text-muted">Click to open UPI app on mobile</p>
                        </div>

                        <div class="row text-center mb-4">
                            <div class="col">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/Google_Pay_Logo_%282020%29.svg" 
                                     alt="Google Pay" style="height: 40px;">
                            </div>
                            <div class="col">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/4/4c/Paytm_Logo.png" 
                                     alt="Paytm" style="height: 40px;">
                            </div>
                            <div class="col">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/7/71/PhonePe_Logo.svg" 
                                     alt="PhonePe" style="height: 40px;">
                            </div>
                        </div>

                        <hr>

                        <div class="card bg-light">
                            <div class="card-body">
                                <h5>After Payment</h5>
                                <p>Once payment is completed, enter your UPI Transaction ID below:</p>
                                <form action="php/submit-transaction.php" method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">UPI Transaction ID *</label>
                                        <input type="text" name="transaction_id" class="form-control" 
                                               placeholder="Enter 12-digit transaction ID" required>
                                        <small class="text-muted">Find this in your UPI app payment history</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Your UPI ID (Optional)</label>
                                        <input type="text" name="upi_id" class="form-control" 
                                               placeholder="yourname@paytm">
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-check-circle"></i> Submit Transaction ID
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: "<?= $qr_data ?>",
            width: 200,
            height: 200
        });
    </script>
</body>
</html>
