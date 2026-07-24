<?php

// Submit book request
function submit_book_request($con, $data) {
    $sql = "INSERT INTO book_requests (user_id, title, description, author_name, category_id, price, cover, file) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    return $stmt->execute([
        $data['user_id'],
        $data['title'],
        $data['description'],
        $data['author_name'],
        $data['category_id'],
        $data['price'],
        $data['cover'],
        $data['file']
    ]);
}

// Get user's book requests
function get_user_requests($con, $user_id) {
    $sql = "SELECT br.*, c.name as category_name 
            FROM book_requests br
            JOIN categories c ON br.category_id = c.id
            WHERE br.user_id = ?
            ORDER BY br.created_at DESC";
    $stmt = $con->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Get all pending requests (admin)
function get_pending_requests($con) {
    $sql = "SELECT br.*, u.full_name, u.email, c.name as category_name 
            FROM book_requests br
            JOIN users u ON br.user_id = u.id
            JOIN categories c ON br.category_id = c.id
            WHERE br.status = 'pending'
            ORDER BY br.created_at DESC";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Get all requests (admin)
function get_all_requests($con, $status = null) {
    if ($status) {
        $sql = "SELECT br.*, u.full_name, u.email, c.name as category_name 
                FROM book_requests br
                JOIN users u ON br.user_id = u.id
                JOIN categories c ON br.category_id = c.id
                WHERE br.status = ?
                ORDER BY br.created_at DESC";
        $stmt = $con->prepare($sql);
        $stmt->execute([$status]);
    } else {
        $sql = "SELECT br.*, u.full_name, u.email, c.name as category_name 
                FROM book_requests br
                JOIN users u ON br.user_id = u.id
                JOIN categories c ON br.category_id = c.id
                ORDER BY br.created_at DESC";
        $stmt = $con->prepare($sql);
        $stmt->execute();
    }
    return $stmt->fetchAll();
}

// Get single request
function get_request($con, $id) {
    $sql = "SELECT br.*, u.full_name, u.email, c.name as category_name 
            FROM book_requests br
            JOIN users u ON br.user_id = u.id
            JOIN categories c ON br.category_id = c.id
            WHERE br.id = ?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Approve request with publishing fee
function approve_request($con, $request_id, $publishing_fee, $admin_notes = '') {
    // If free publishing, publish immediately
    if ($publishing_fee == 0) {
        return publish_book($con, $request_id, $admin_notes);
    }
    
    // Otherwise, set status to payment_pending
    $sql = "UPDATE book_requests 
            SET status = 'approved', publishing_fee = ?, admin_notes = ?, reviewed_at = NOW() 
            WHERE id = ?";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$publishing_fee, $admin_notes, $request_id]);
}

// Publish book after payment
function publish_book($con, $request_id, $admin_notes = '') {
    // Get request details
    $request = get_request($con, $request_id);
    
    // Check if author exists, if not create
    $author_sql = "SELECT id FROM authors WHERE name = ?";
    $stmt = $con->prepare($author_sql);
    $stmt->execute([$request['author_name']]);
    $author = $stmt->fetch();
    
    if (!$author) {
        $insert_author = "INSERT INTO authors (name) VALUES (?)";
        $stmt = $con->prepare($insert_author);
        $stmt->execute([$request['author_name']]);
        $author_id = $con->lastInsertId();
    } else {
        $author_id = $author['id'];
    }
    
    // Insert book
    $book_sql = "INSERT INTO books (title, author_id, description, price, category_id, cover, file) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($book_sql);
    $stmt->execute([
        $request['title'],
        $author_id,
        $request['description'],
        $request['price'],
        $request['category_id'],
        $request['cover'],
        $request['file']
    ]);
    
    // Update request status
    $update_sql = "UPDATE book_requests 
                   SET status = 'published', admin_notes = ?, published_at = NOW() 
                   WHERE id = ?";
    $stmt = $con->prepare($update_sql);
    return $stmt->execute([$admin_notes, $request_id]);
}

// Submit payment for publishing fee
function submit_publishing_payment($con, $request_id, $transaction_id) {
    $sql = "UPDATE book_requests 
            SET payment_status = 'paid', transaction_id = ? 
            WHERE id = ?";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$transaction_id, $request_id]);
}

// Verify publishing payment and publish book
function verify_publishing_payment($con, $request_id) {
    return publish_book($con, $request_id, 'Payment verified and book published');
}

// Reject request
function reject_request($con, $request_id, $admin_notes) {
    $sql = "UPDATE book_requests 
            SET status = 'rejected', admin_notes = ?, reviewed_at = NOW() 
            WHERE id = ?";
    $stmt = $con->prepare($sql);
    return $stmt->execute([$admin_notes, $request_id]);
}

// Get request statistics
function get_request_stats($con) {
    $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
            FROM book_requests";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    return $stmt->fetch();
}

// Get pending payments
function get_pending_publishing_payments($con) {
    $sql = "SELECT br.*, u.full_name, u.email 
            FROM book_requests br
            JOIN users u ON br.user_id = u.id
            WHERE br.status = 'approved' AND br.payment_status = 'paid'
            ORDER BY br.created_at DESC";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}
