<?php

// Get user's cart items with book details
function get_cart_items($con, $user_id) {
    $sql = "SELECT c.*, b.title, b.price, b.cover, b.file,
                   a.name as author_name, cat.name as category_name
            FROM cart c
            JOIN books b ON c.book_id = b.id
            JOIN authors a ON b.author_id = a.id
            JOIN categories cat ON b.category_id = cat.id
            WHERE c.user_id = ?
            ORDER BY c.created_at DESC";
    $stmt = $con->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Get cart item count
function get_cart_count($con, $user_id) {
    $sql = "SELECT COUNT(*) FROM cart WHERE user_id=?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

// Calculate cart total
function get_cart_total($con, $user_id) {
    $sql = "SELECT SUM(b.price) as total
            FROM cart c
            JOIN books b ON c.book_id = b.id
            WHERE c.user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

// Check if book in cart
function is_in_cart($con, $user_id, $book_id) {
    $sql = "SELECT COUNT(*) FROM cart 
            WHERE user_id=? AND book_id=?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$user_id, $book_id]);
    return $stmt->fetchColumn() > 0;
}

// Add to cart
function add_to_cart($con, $user_id, $book_id) {
    $sql = "INSERT INTO cart (user_id, book_id) VALUES (?, ?)";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$user_id, $book_id]);
}

// Remove from cart
function remove_from_cart($con, $user_id, $book_id) {
    $sql = "DELETE FROM cart WHERE user_id=? AND book_id=?";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$user_id, $book_id]);
}

// Clear cart
function clear_cart($con, $user_id) {
    $sql = "DELETE FROM cart WHERE user_id=?";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$user_id]);
}

// Check if user already purchased book
function has_purchased($con, $user_id, $book_id) {
    $sql = "SELECT COUNT(*) FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            WHERE o.user_id = ? AND oi.book_id = ? AND o.status = 'completed'";
    $stmt = $con->prepare($sql);
    $stmt->execute([$user_id, $book_id]);
    return $stmt->fetchColumn() > 0;
}
