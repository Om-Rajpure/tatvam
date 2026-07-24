<?php
session_start();

// Destroy user session
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_type']);
}

session_destroy();

header("Location: ../index.php?success=Logged out successfully");
exit;
