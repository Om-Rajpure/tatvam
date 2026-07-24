<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../user-login.php");
    exit;
}

include "../db_conn.php";
include "func-book-request.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = intval($_POST['request_id']);
    $transaction_id = trim($_POST['transaction_id']);

    if (empty($transaction_id)) {
        header("Location: ../pay-publishing-fee.php?id=$request_id&error=Transaction ID is required");
        exit;
    }

    if (submit_publishing_payment($conn, $request_id, $transaction_id)) {
        header("Location: ../my-book-requests.php?success=Payment submitted! Waiting for admin verification");
    } else {
        header("Location: ../pay-publishing-fee.php?id=$request_id&error=Failed to submit payment");
    }
} else {
    header("Location: ../my-book-requests.php");
}
