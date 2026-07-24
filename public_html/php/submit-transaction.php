<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'customer' || !isset($_SESSION['order_id'])) {
    header("Location: ../index.php");
    exit;
}

include "../db_conn.php";
include "func-payment.php";
include "func-order.php";

$transaction_id = trim($_POST['transaction_id'] ?? '');
$upi_id = trim($_POST['upi_id'] ?? '');
$order_id = $_SESSION['order_id'];

if (empty($transaction_id)) {
    header("Location: ../payment.php?error=Transaction ID is required");
    exit;
}

$payment = get_payment_by_order($conn, $order_id);

if (!$payment) {
    header("Location: ../payment.php?error=Payment record not found");
    exit;
}

$sql = "UPDATE payments SET transaction_id=?, upi_id=?, status='pending' WHERE id=?";
$stmt = $conn->prepare($sql);

if ($stmt->execute([$transaction_id, $upi_id, $payment['id']])) {
    header("Location: ../payment-pending.php?success=Transaction ID submitted successfully");
} else {
    header("Location: ../payment.php?error=Failed to submit transaction ID");
}
exit;
