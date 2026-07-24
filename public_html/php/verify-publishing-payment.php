<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include "../db_conn.php";
include "func-book-request.php";

$request_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($request_id > 0) {
    if (verify_publishing_payment($conn, $request_id)) {
        $_SESSION['success'] = "Payment verified and book published successfully!";
    } else {
        $_SESSION['success'] = "Failed to publish book";
    }
}

header("Location: ../admin-book-requests.php");
exit;
