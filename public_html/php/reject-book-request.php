<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../db_conn.php";
include "func-book-request.php";

$request_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$notes = isset($_GET['notes']) ? trim($_GET['notes']) : 'Request rejected';

if ($request_id > 0) {
    if (reject_request($conn, $request_id, $notes)) {
        $_SESSION['success'] = "Book request rejected";
        
        $request = get_request($conn, $request_id);
        // TODO (REQ-027): Send notification to author on rejection
        // send_notification($request['user_id'], 'rejected', $request['title']);
        
    } else {
        $_SESSION['error'] = "Failed to reject request";
    }
}

if (isset($_GET['from_detail']) && $_GET['from_detail'] == 1) {
    header("Location: ../admin-view-request.php?id=" . $request_id);
} else {
    header("Location: ../admin-book-requests.php");
}
exit;
