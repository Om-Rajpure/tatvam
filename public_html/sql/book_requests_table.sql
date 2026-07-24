-- Book Publishing Requests Table
CREATE TABLE IF NOT EXISTS book_requests (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    author_name VARCHAR(255) NOT NULL,
    category_id INT(11) NOT NULL,
    price DECIMAL(10,2) DEFAULT 0.00,
    cover VARCHAR(255) NOT NULL,
    file VARCHAR(255) NOT NULL,
    status ENUM('pending', 'approved', 'payment_pending', 'published', 'rejected') DEFAULT 'pending',
    publishing_fee DECIMAL(10,2) DEFAULT 0.00,
    payment_status ENUM('unpaid', 'paid') DEFAULT 'unpaid',
    transaction_id VARCHAR(255) NULL,
    admin_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    published_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
