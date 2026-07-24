<?php

// Get user by ID
function get_user($con, $id) {
    $sql = "SELECT * FROM users WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Get user by email
function get_user_by_email($con, $email) {
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$email]);
    return $stmt->fetch();
}

// Check if email exists
function email_exists($con, $email) {
    $sql = "SELECT COUNT(*) FROM users WHERE email=?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$email]);
    return $stmt->fetchColumn() > 0;
}

// Update user profile
function update_user($con, $id, $name, $phone) {
    $sql = "UPDATE users SET full_name=?, phone=?, updated_at=NOW() WHERE id=?";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$name, $phone, $id]);
}

// Change password
function change_password($con, $id, $new_password) {
    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password=?, updated_at=NOW() WHERE id=?";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$hashed, $id]);
}

// Get user statistics
function get_user_stats($con, $user_id) {
    $sql = "SELECT 
                COUNT(o.id) as total_orders,
                COALESCE(SUM(o.total_amount), 0) as total_spent
            FROM orders o
            WHERE o.user_id=? AND o.status='completed'";
    $stmt = $con->prepare($sql);
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    return $result ? $result : ['total_orders' => 0, 'total_spent' => 0];
}
