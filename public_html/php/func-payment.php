<?php

// Generate UPI payment link
function generate_upi_link($order_number, $amount) {
    $upi_id = MERCHANT_UPI_ID;
    $merchant_name = urlencode(MERCHANT_NAME);
    $amount = number_format($amount, 2, '.', '');
    $order_ref = urlencode($order_number);
    return "upi://pay?pa={$upi_id}&pn={$merchant_name}&am={$amount}&tn={$order_ref}&cu=INR";
}

// Create payment record
function create_payment($conn, $order_id, $amount) {
    $sql = "INSERT INTO payments (order_id, amount, status) VALUES (?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$order_id, $amount]);
    return $conn->lastInsertId();
}

// Get payment by ID
function get_payment($conn, $payment_id) {
    $sql = "SELECT p.*, o.order_number, o.user_id, u.full_name, u.email
            FROM payments p
            JOIN orders o ON p.order_id = o.id
            JOIN users u ON o.user_id = u.id
            WHERE p.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$payment_id]);
    return $stmt->fetch();
}

// Get payment by order ID
function get_payment_by_order($conn, $order_id) {
    $sql = "SELECT * FROM payments WHERE order_id=? ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$order_id]);
    return $stmt->fetch();
}

// Update payment status
function update_payment_status($conn, $payment_id, $status, $admin_id = null) {
    if ($admin_id) {
        $sql = "UPDATE payments SET status=?, verified_by=?, verified_at=NOW() WHERE id=?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$status, $admin_id, $payment_id]);
    } else {
        $sql = "UPDATE payments SET status=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$status, $payment_id]);
    }
}

// Get pending payments (admin)
function get_pending_payments($conn) {
    $sql = "SELECT p.*, o.order_number, u.full_name, u.email, u.phone
            FROM payments p
            JOIN orders o ON p.order_id = o.id
            JOIN users u ON o.user_id = u.id
            WHERE p.status = 'pending' AND p.transaction_id IS NOT NULL
            ORDER BY p.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get all payments (admin)
function get_all_payments($conn, $status = null) {
    $sql = "SELECT p.*, o.order_number, u.full_name, u.email
            FROM payments p
            JOIN orders o ON p.order_id = o.id
            JOIN users u ON o.user_id = u.id";
    
    if ($status) {
        $sql .= " WHERE p.status = ?";
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $status ? $stmt->execute([$status]) : $stmt->execute();
    return $stmt->fetchAll();
}
