<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../db_conn.php";
include "func-payment.php";
include "func-order.php";
include "func-cart.php";

$payment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';
$admin_id = $_SESSION['user_id'];

$payment = get_payment($conn, $payment_id);

if (!$payment) {
    $_SESSION['error'] = "Payment not found";
    header("Location: ../admin-verify-payments.php");
    exit;
}

if ($action == 'verify') {
    $conn->beginTransaction();
    try {
        update_payment_status($conn, $payment_id, 'success', $admin_id);
        update_order_status($conn, $payment['order_id'], 'completed');
        clear_cart($conn, $payment['user_id']);
        
        $conn->commit();
        $_SESSION['success'] = "Payment verified successfully";
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Failed to verify payment";
    }
} elseif ($action == 'reject') {
    update_payment_status($conn, $payment_id, 'failed', $admin_id);
    update_order_status($conn, $payment['order_id'], 'failed');
    $_SESSION['success'] = "Payment rejected";
} else {
    $_SESSION['error'] = "Invalid action";
}

header("Location: ../admin-verify-payments.php");
exit;
