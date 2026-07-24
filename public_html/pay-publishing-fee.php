<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'customer') {
    header("Location: user-login.php");
    exit;
}

include "db_conn.php";
include "php/func-book-request.php";
include "config/payment-config.php";

$request_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$request = get_request($conn, $request_id);

if (!$request || $request['user_id'] != $_SESSION['user_id']) {
    header("Location: my-book-requests.php");
    exit;
}

$upi_link = "upi://pay?pa=" . MERCHANT_UPI_ID . "&pn=" . urlencode(MERCHANT_NAME) . 
            "&am=" . $request['publishing_fee'] . "&cu=" . CURRENCY . 
            "&tn=" . urlencode("Publishing Fee - " . $request['title']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Publishing Fee - Tatvam Publication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-book-half text-primary"></i>
                <span class="brand-text">Tatvam <span class="text-primary">Publication</span></span>
            </a>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-credit-card"></i> Pay Publishing Fee</h4>
                    </div>
                    <div class="card-body text-center">
                        <h5><?= htmlspecialchars($request['title']) ?></h5>
                        <h2 class="text-primary my-4">\u20b9<?= number_format($request['publishing_fee'], 2) ?></h2>
                        
                        <div id="qrcode" class="mb-4 d-flex justify-content-center"></div>
                        
                        <div class="alert alert-info">
                            <p class="mb-2"><strong>UPI ID:</strong> <?= MERCHANT_UPI_ID ?></p>
                            <a href="<?= $upi_link ?>" class="btn btn-primary">
                                <i class="bi bi-phone"></i> Pay with UPI App
                            </a>
                        </div>

                        <form action="php/submit-publishing-payment.php" method="POST" class="mt-4">
                            <input type="hidden" name="request_id" value="<?= $request_id ?>">
                            <div class="mb-3">
                                <label class="form-label">Enter Transaction ID / UTR Number</label>
                                <input type="text" class="form-control" name="transaction_id" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Submit Payment Proof
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        new QRCode(document.getElementById("qrcode"), {
            text: "<?= $upi_link ?>",
            width: 200,
            height: 200
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
