<?php

// Generate unique order number
function generate_order_number($conn) {
    do {
        $number = 'ORD' . date('Ymd') . rand(1000, 9999);
        $sql = "SELECT COUNT(*) FROM orders WHERE order_number=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$number]);
    } while ($stmt->fetchColumn() > 0);
    return $number;
}

// Create order from cart
function create_order($conn, $user_id) {
    $conn->beginTransaction();
    try {
        // Get user details
        $user_sql = "SELECT full_name, email, phone FROM users WHERE id=?";
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->execute([$user_id]);
        $user = $user_stmt->fetch();
        
        if (!$user) {
            throw new Exception("User not found");
        }
        
        $order_number = generate_order_number($conn);
        $total = get_cart_total($conn, $user_id);
        
        if ($total <= 0) {
            throw new Exception("Cart is empty or invalid");
        }
        
        $sql = "INSERT INTO orders (user_id, order_number, full_name, email, phone, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id, $order_number, $user['full_name'], $user['email'], $user['phone'], $total]);
        $order_id = $conn->lastInsertId();
        
        $cart_items = get_cart_items($conn, $user_id);
        $sql = "INSERT INTO order_items (order_id, book_id, price) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        foreach ($cart_items as $item) {
            $stmt->execute([$order_id, $item['book_id'], $item['price']]);
        }
        
        // Clear cart after order creation
        clear_cart($conn, $user_id);
        
        $conn->commit();
        return ['status' => 'success', 'order_id' => $order_id, 'order_number' => $order_number];
    } catch (Exception $e) {
        $conn->rollBack();
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

// Get user orders
function get_user_orders($conn, $user_id) {
    $sql = "SELECT o.*, COUNT(oi.id) as book_count
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.user_id = ?
            GROUP BY o.id
            ORDER BY o.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Get single order with user details
function get_order($conn, $order_id) {
    $sql = "SELECT o.*, u.full_name, u.email, u.phone
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$order_id]);
    return $stmt->fetch();
}

// Get order items with book details
function get_order_items($conn, $order_id) {
    $sql = "SELECT oi.*, b.title, b.cover, b.file, a.name as author_name
            FROM order_items oi
            JOIN books b ON oi.book_id = b.id
            JOIN authors a ON b.author_id = a.id
            WHERE oi.order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$order_id]);
    return $stmt->fetchAll();
}

// Get all orders (admin)
function get_all_orders($conn, $status = null) {
    $sql = "SELECT o.*, u.full_name, u.email, COUNT(oi.id) as book_count
            FROM orders o
            JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.id = oi.order_id";
    
    if ($status) {
        $sql .= " WHERE o.status = ?";
    }
    
    $sql .= " GROUP BY o.id ORDER BY o.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $status ? $stmt->execute([$status]) : $stmt->execute();
    return $stmt->fetchAll();
}

// Update order status
function update_order_status($conn, $order_id, $status) {
    $sql = "UPDATE orders SET status=?, updated_at=NOW() WHERE id=?";
    $stmt = $conn->prepare($sql);
    return $stmt->execute([$status, $order_id]);
}

// Check if user owns order
function user_owns_order($conn, $user_id, $order_id) {
    $sql = "SELECT COUNT(*) FROM orders WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$order_id, $user_id]);
    return $stmt->fetchColumn() > 0;
}

// Get order statistics
function get_order_stats($conn) {
    $sql = "SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status='failed' THEN 1 ELSE 0 END) as failed_orders,
                SUM(total_amount) as total_revenue,
                SUM(CASE WHEN status='completed' THEN total_amount ELSE 0 END) as completed_revenue
            FROM orders";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetch();
}
