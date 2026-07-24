-- Module 06: Shopping Cart System
-- Create cart table for shopping cart functionality

CREATE TABLE IF NOT EXISTS `cart` (
    `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `book_id` INT(11) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create indexes for better performance
CREATE INDEX idx_user_cart ON cart(user_id);
CREATE INDEX idx_book_cart ON cart(book_id);
