-- Module 05: User Management System
-- Create users table for customer accounts

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) PRIMARY KEY AUTO_INCREMENT,
    `full_name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `phone` VARCHAR(15) NOT NULL,
    `password` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create indexes for better performance
CREATE UNIQUE INDEX idx_email ON users(email);
CREATE INDEX idx_phone ON users(phone);
