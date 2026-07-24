<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../db_conn.php";
include "func-book-request.php";
include "func-order.php";

$request_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$publishing_fee = isset($_GET['fee']) ? floatval($_GET['fee']) : 0;
$notes = isset($_GET['notes']) ? trim($_GET['notes']) : '';

if ($request_id > 0) {
    if (approve_request($conn, $request_id, $publishing_fee, $notes)) {
        if ($publishing_fee == 0) {
            $_SESSION['success'] = "Book published successfully (Free)!";
        } else {
            $_SESSION['success'] = "Request approved! User needs to pay ₹" . number_format($publishing_fee, 2) . " publishing fee";
        }
        
        $request = get_request($conn, $request_id);
        // TODO (REQ-027): Send notification to author on approval
        // send_notification($request['user_id'], 'approved', $request['title']);
        
    } else {
        $_SESSION['error'] = "Failed to approve request";
    }
}

if (isset($_GET['from_detail']) && $_GET['from_detail'] == 1) {
    header("Location: ../admin-view-request.php?id=" . $request_id);
} else {
    header("Location: ../admin-book-requests.php");
}
exit;
