<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include "../db_conn.php";
include "func-order.php";

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

$valid_statuses = ['pending', 'completed', 'failed', 'refunded'];

if (!in_array($status, $valid_statuses)) {
    $_SESSION['error'] = "Invalid status";
    header("Location: ../admin-orders.php");
    exit;
}

if (update_order_status($conn, $order_id, $status)) {
    $_SESSION['success'] = "Order status updated to " . ucfirst($status);
} else {
    $_SESSION['error'] = "Failed to update order status";
}

header("Location: ../admin-orders.php");
exit;
