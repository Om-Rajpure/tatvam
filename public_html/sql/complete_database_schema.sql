/* ============================================================================
   TATVAM PUBLICATION - COMPLETE VALIDATED MYSQL DATABASE SCHEMA
   Target Compatibility: MySQL 5.7+ / 8.0+ / MariaDB 10.4+ / phpMyAdmin / XAMPP
   Engine: InnoDB | Character Set: utf8mb4 (utf8mb4_unicode_ci)
   ============================================================================ */

SET FOREIGN_KEY_CHECKS = 0;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

/*!40101 SET NAMES utf8mb4 */;


/* ----------------------------------------------------------------------------
   1. ADMIN TABLE (Admin Panel Credentials)
   ---------------------------------------------------------------------------- */

CREATE TABLE IF NOT EXISTS `admin` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_admin_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ----------------------------------------------------------------------------
   2. USERS TABLE (Customer & Author Accounts)
   ---------------------------------------------------------------------------- */

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `password` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_users_email` (`email`),
  INDEX `idx_users_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ----------------------------------------------------------------------------
   3. CATEGORIES TABLE (Book & Research Paper Classifications)
   ---------------------------------------------------------------------------- */

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_categories_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ----------------------------------------------------------------------------
   4. AUTHORS TABLE (Author Profiles & Bio Data)
   ---------------------------------------------------------------------------- */

CREATE TABLE IF NOT EXISTS `authors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `name` VARCHAR(255) NOT NULL,
  `photo` VARCHAR(255) DEFAULT NULL,
  `about` TEXT DEFAULT NULL,
  `qualification` VARCHAR(255) DEFAULT NULL,
  `designation` VARCHAR(255) DEFAULT NULL,
  `organization` VARCHAR(255) DEFAULT NULL,
  `contact` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_authors_user_id` (`user_id`),
  CONSTRAINT `fk_author_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ----------------------------------------------------------------------------
   5. BOOKS TABLE (Published Books Catalogue)
   ---------------------------------------------------------------------------- */

CREATE TABLE IF NOT EXISTS `books` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `subtitle` VARCHAR(255) DEFAULT NULL,
  `author_id` INT(11) NOT NULL,
  `description` TEXT NOT NULL,
  `category_id` INT(11) NOT NULL,
  `cover` VARCHAR(255) NOT NULL,
  `file` VARCHAR(255) NOT NULL,
  `preview_file` VARCHAR(255) DEFAULT NULL,
  `isbn` VARCHAR(50) DEFAULT NULL,
  `doi` VARCHAR(100) DEFAULT NULL,
  `pages` INT(11) DEFAULT NULL,
  `format` ENUM('Paperback','eBook') DEFAULT 'eBook',
  `language` VARCHAR(50) DEFAULT 'English',
  `edition` VARCHAR(50) DEFAULT '1st Edition',
  `keywords` TEXT DEFAULT NULL,
  `publication_date` DATE DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `status` ENUM('Draft','Pending','Approved','Rejected','Published') NOT NULL DEFAULT 'Published',
  `content_type` ENUM('book','research_paper') NOT NULL DEFAULT 'book',
  `created_by` INT(11) DEFAULT NULL,
  `approved_by` INT(11) DEFAULT NULL,
  `approved_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_books_author` (`author_id`),
  KEY `idx_books_category` (`category_id`),
  KEY `idx_books_status` (`status`),
  KEY `idx_books_content_type` (`content_type`),
  CONSTRAINT `fk_books_author` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_books_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ----------------------------------------------------------------------------
   6. RESEARCH_PAPERS TABLE (Published Research Papers Catalogue)
   ---------------------------------------------------------------------------- */

CREATE TABLE IF NOT EXISTS `research_papers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `abstract` TEXT NOT NULL,
  `pdf` VARCHAR(255) NOT NULL,
  `preview_pdf` VARCHAR(255) DEFAULT NULL,
  `doi` VARCHAR(100) DEFAULT NULL,
  `keywords` TEXT DEFAULT NULL,
  `category_id` INT(11) NOT NULL,
  `pages` INT(11) DEFAULT NULL,
  `publication_date` DATE DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `status` ENUM('Draft','Pending','Approved','Rejected','Published') NOT NULL DEFAULT 'Published',
  `author_id` INT(11) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_research_author` (`author_id`),
  KEY `idx_research_category` (`category_id`),
  KEY `idx_research_status` (`status`),
  CONSTRAINT `fk_research_author` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_research_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ----------------------------------------------------------------------------
   7. BOOK_REQUESTS TABLE (Author Publishing Request Submissions)
   ---------------------------------------------------------------------------- */

CREATE TABLE IF NOT EXISTS `book_requests` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `author_name` VARCHAR(255) NOT NULL,
  `category_id` INT(11) NOT NULL,
  `price` DECIMAL(10,2) DEFAULT '0.00',
  `cover` VARCHAR(255) NOT NULL,
  `file` VARCHAR(255) NOT NULL,
  `preview_file` VARCHAR(255) DEFAULT NULL,
  `content_type` ENUM('book','research_paper') NOT NULL DEFAULT 'book',
  `isbn` VARCHAR(50) DEFAULT NULL,
  `doi` VARCHAR(100) DEFAULT NULL,
  `pages` INT(11) DEFAULT NULL,
  `format` ENUM('Paperback','eBook') DEFAULT 'eBook',
  `status` ENUM('pending','approved','payment_pending','published','rejected') DEFAULT 'pending',
  `publishing_fee` DECIMAL(10,2) DEFAULT '0.00',
  `payment_status` ENUM('unpaid','paid') DEFAULT 'unpaid',
  `transaction_id` VARCHAR(255) DEFAULT NULL,
  `scanner_status` ENUM('pending','passed','failed') DEFAULT 'passed',
  `admin_notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `reviewed_at` TIMESTAMP NULL DEFAULT NULL,
  `published_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_requests_user` (`user_id`),
  KEY `idx_requests_category` (`category_id`),
  KEY `idx_requests_status` (`status`),
  KEY `idx_requests_payment_status` (`payment_status`),
  CONSTRAINT `fk_requests_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_requests_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ----------------------------------------------------------------------------
   8. PUBLICATION_SCANS TABLE (Plagiarism & Scanner Audit Reports)
   ---------------------------------------------------------------------------- */

CREATE TABLE IF NOT EXISTS `publication_scans` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `publication_id` INT(11) NOT NULL,
  `publication_type` ENUM('book','research_paper','book_request') NOT NULL DEFAULT 'book_request',
  `plagiarism_percentage` DECIMAL(5,2) DEFAULT '0.00',
  `scanner_name` VARCHAR(100) DEFAULT 'Pluggable Content Scanner v1.0',
  `scan_status` ENUM('pending','passed','failed','flagged') DEFAULT 'passed',
  `report_path` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_scans_publication` (`publication_id`, `publication_type`),
  KEY `idx_scans_status` (`scan_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ----------------------------------------------------------------------------
   9. CART TABLE (User Shopping Cart Persistence)
   ---------------------------------------------------------------------------- */

CREATE TABLE IF NOT EXISTS `cart` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `book_id` INT(11) NOT NULL,
  `publication_type` ENUM('book','research_paper') DEFAULT 'book',
  `publication_id` INT(11) DEFAULT NULL,
  `quantity` INT(11) DEFAULT 1,
  `unit_price` DECIMAL(10,2) DEFAULT '0.00',
  `subtotal` DECIMAL(10,2) DEFAULT '0.00',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_cart_item` (`user_id`, `book_id`),
  KEY `idx_user_cart` (`user_id`),
  KEY `idx_book_cart` (`book_id`),
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cart_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ----------------------------------------------------------------------------
   10. ORDERS TABLE (Customer Checkout Orders)
   ---------------------------------------------------------------------------- */

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `order_number` VARCHAR(50) NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('pending','completed','failed','refunded') DEFAULT 'pending',
  `payment_status` ENUM('pending','paid','failed') DEFAULT 'pending',
  `payment_method` VARCHAR(50) DEFAULT 'UPI',
  `invoice_number` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_order_number` (`order_number`),
  KEY `idx_user_orders` (`user_id`),
  KEY `idx_order_status` (`status`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ----------------------------------------------------------------------------
   11. ORDER_ITEMS TABLE (Line-Items inside Orders)
   ---------------------------------------------------------------------------- */

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `book_id` INT(11) NOT NULL,
  `publication_type` ENUM('book','research_paper') DEFAULT 'book',
  `publication_id` INT(11) DEFAULT NULL,
  `quantity` INT(11) DEFAULT 1,
  `price` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) DEFAULT '0.00',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order` (`order_id`),
  KEY `idx_order_items_book` (`book_id`),
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_order_items_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ----------------------------------------------------------------------------
   12. PAYMENTS TABLE (Payment Transactions & UPI Verification Log)
   ---------------------------------------------------------------------------- */

CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `transaction_id` VARCHAR(255) DEFAULT NULL,
  `upi_id` VARCHAR(255) DEFAULT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('pending','success','completed','failed') DEFAULT 'pending',
  `payment_method` VARCHAR(50) DEFAULT 'UPI',
  `payment_response` TEXT DEFAULT NULL,
  `verified_by` INT(11) DEFAULT NULL,
  `verified_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_payments_transaction_id` (`transaction_id`),
  KEY `idx_payments_order` (`order_id`),
  KEY `idx_payments_status` (`status`),
  CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ----------------------------------------------------------------------------
   13. DOWNLOADS TABLE (File Download Access & Preview Controller)
   ---------------------------------------------------------------------------- */

CREATE TABLE IF NOT EXISTS `downloads` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `book_id` INT(11) DEFAULT NULL,
  `publication_id` INT(11) DEFAULT NULL,
  `publication_type` ENUM('book','research_paper') DEFAULT 'book',
  `order_id` INT(11) DEFAULT NULL,
  `preview_pdf` VARCHAR(255) DEFAULT NULL,
  `full_pdf` VARCHAR(255) DEFAULT NULL,
  `preview_page_limit` INT(11) DEFAULT 10,
  `download_count` INT(11) DEFAULT 0,
  `last_downloaded` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_downloads_user` (`user_id`),
  KEY `idx_downloads_book` (`book_id`),
  KEY `idx_downloads_order` (`order_id`),
  CONSTRAINT `fk_downloads_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_downloads_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_downloads_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ----------------------------------------------------------------------------
   14. ADMIN_APPROVALS TABLE (Admin Workflow Audit Trail)
   ---------------------------------------------------------------------------- */

CREATE TABLE IF NOT EXISTS `admin_approvals` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `publication_id` INT(11) NOT NULL,
  `publication_type` ENUM('book','research_paper','book_request') NOT NULL DEFAULT 'book_request',
  `admin_id` INT(11) NOT NULL,
  `action` ENUM('approve','reject','verify_payment','publish') NOT NULL,
  `comments` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_approvals_publication` (`publication_id`, `publication_type`),
  KEY `idx_approvals_admin` (`admin_id`),
  CONSTRAINT `fk_approvals_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/* ----------------------------------------------------------------------------
   IDEMPOTENT ALTER STATEMENTS FOR EXISTING TABLES
   ---------------------------------------------------------------------------- */

/* Authors Profile Fields Migration */
SET @dbname = DATABASE();

SET @tablename = "authors";

SET @columnname = "user_id";

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 1",
  "ALTER TABLE authors ADD COLUMN user_id INT(11) NULL AFTER id, ADD COLUMN photo VARCHAR(255) NULL, ADD COLUMN about TEXT NULL, ADD COLUMN qualification VARCHAR(255) NULL, ADD COLUMN designation VARCHAR(255) NULL, ADD COLUMN organization VARCHAR(255) NULL, ADD COLUMN contact VARCHAR(255) NULL;"
));

PREPARE alterIfNotExists FROM @preparedStatement;

EXECUTE alterIfNotExists;

DEALLOCATE PREPARE alterIfNotExists;


/* Books Extended Metadata Fields Migration */
SET @columnname = "content_type";

SET @tablename = "books";

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 1",
  "ALTER TABLE books ADD COLUMN content_type ENUM('book','research_paper') NOT NULL DEFAULT 'book' AFTER category_id, ADD COLUMN isbn VARCHAR(50) NULL, ADD COLUMN doi VARCHAR(100) NULL, ADD COLUMN pages INT NULL, ADD COLUMN format ENUM('Paperback','eBook') DEFAULT 'eBook', ADD COLUMN preview_file VARCHAR(255) NULL;"
));

PREPARE alterIfNotExists FROM @preparedStatement;

EXECUTE alterIfNotExists;

DEALLOCATE PREPARE alterIfNotExists;


/* Orders Full Name, Email, Phone Migration */
SET @columnname = "full_name";

SET @tablename = "orders";

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 1",
  "ALTER TABLE orders ADD COLUMN full_name VARCHAR(255) NOT NULL AFTER order_number, ADD COLUMN email VARCHAR(255) NOT NULL AFTER full_name, ADD COLUMN phone VARCHAR(20) NOT NULL AFTER email;"
));

PREPARE alterIfNotExists FROM @preparedStatement;

EXECUTE alterIfNotExists;

DEALLOCATE PREPARE alterIfNotExists;


/* ----------------------------------------------------------------------------
   SEED DATA INSERT STATEMENTS
   ---------------------------------------------------------------------------- */

/* Insert Admin Account */

INSERT INTO `admin` (`id`, `full_name`, `email`, `password`)
VALUES
(1, 'System Admin', 'admin@tatvampublication.com', '$2y$10$Nqq/y251QX2Ccvb1Ax7hUuMqQSkG3yRLCxN2KPdetnSP3oaXVH70a')
ON DUPLICATE KEY UPDATE `full_name` = VALUES(`full_name`);


/* Insert Categories */

INSERT INTO `categories` (`id`, `name`)
VALUES
(1, 'Artificial Intelligence'),
(2, 'Data Science'),
(3, 'Machine Learning'),
(4, 'Environmental Science'),
(5, 'Computer Science'),
(6, 'Agriculture'),
(7, 'Biotechnology'),
(8, 'Electronics'),
(9, 'Mechanical Engineering'),
(10, 'Civil Engineering')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);


/* Insert Users */

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`)
VALUES
(1, 'Rahul Patil', 'rahul@example.com', '9876543210', '$2y$10$Nqq/y251QX2Ccvb1Ax7hUuMqQSkG3yRLCxN2KPdetnSP3oaXVH70a'),
(2, 'Sneha Kulkarni', 'sneha@example.com', '9876543211', '$2y$10$Nqq/y251QX2Ccvb1Ax7hUuMqQSkG3yRLCxN2KPdetnSP3oaXVH70a'),
(3, 'Om Rajpure', 'om@example.com', '9876543212', '$2y$10$Nqq/y251QX2Ccvb1Ax7hUuMqQSkG3yRLCxN2KPdetnSP3oaXVH70a'),
(4, 'Priya Sharma', 'priya@example.com', '9876543213', '$2y$10$Nqq/y251QX2Ccvb1Ax7hUuMqQSkG3yRLCxN2KPdetnSP3oaXVH70a'),
(5, 'Rohit Deshmukh', 'rohit@example.com', '9876543214', '$2y$10$Nqq/y251QX2Ccvb1Ax7hUuMqQSkG3yRLCxN2KPdetnSP3oaXVH70a')
ON DUPLICATE KEY UPDATE `full_name` = VALUES(`full_name`);


/* Insert Authors */

INSERT INTO `authors` (`id`, `user_id`, `name`, `photo`, `about`, `qualification`, `designation`, `organization`, `contact`)
VALUES
(1, 1, 'Dr. A. P. Kulkarni', 'kulkarni.png', 'Distinguished Professor of Artificial Intelligence and Robotics with over 20 years of research experience.', 'Ph.D. in Computer Science & AI', 'Professor & Head of Department', 'IIT Bombay', 'kulkarni@example.com'),
(2, 4, 'Dr. Priya Sharma', 'priya.png', 'Expert in Machine Learning algorithms, medical image processing, and neural network optimization.', 'Ph.D. in Machine Learning', 'Associate Professor', 'COEP Technological University', 'priya@example.com'),
(3, 5, 'Prof. Rajesh Patil', 'rajesh.png', 'Specialist in Cloud Infrastructure, Distributed Systems, and Blockchain technology.', 'M.Tech in Computer Engineering', 'Assistant Professor', 'VJTI Mumbai', 'rajesh@example.com'),
(4, 2, 'Dr. Sneha Joshi', 'sneha.png', 'Researcher focusing on Environmental Science, Sustainable Development, and Climate Modeling.', 'Ph.D. in Environmental Science', 'Senior Scientist', 'NEERI Nagpur', 'sneha@example.com'),
(5, 3, 'Dr. Amit Verma', 'verma.png', 'Big Data Analytics expert specializing in Predictive Modeling and Financial Data Science.', 'Ph.D. in Data Science', 'Lead Data Scientist', 'Tata Consultancy Services', 'om@example.com'),
(6, NULL, 'Dr. Kavita Deshmukh', 'kavita.png', 'Agricultural Scientist working on IoT sensors, Precision Farming, and Crop Yield Optimization.', 'Ph.D. in Agricultural Engineering', 'Principal Researcher', 'MPKV Rahuri', 'kavita@example.com')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);


/* Insert Books */

INSERT INTO `books` (`id`, `title`, `subtitle`, `author_id`, `description`, `category_id`, `cover`, `file`, `preview_file`, `isbn`, `doi`, `pages`, `format`, `price`, `status`, `content_type`)
VALUES
(1, 'Fundamentals of Artificial Intelligence', 'Theory and Applications in the Modern Era', 1, 'A comprehensive guide to state-of-the-art Artificial Intelligence, heuristics, logic programming, and deep neural networks.', 1, 'cover_ai.png', 'files_ai.pdf', 'preview_ai.pdf', '978-81-203-5241-1', '10.1007/s11276-024-02801-x', 480, 'eBook', 499.00, 'Published', 'book'),
(2, 'Introduction to Machine Learning', 'Algorithms, Mathematics and Code', 2, 'In-depth textbook covering Supervised, Unsupervised, and Reinforcement Learning with practical Python examples.', 3, 'cover_ml.png', 'files_ml.pdf', 'preview_ml.pdf', '978-81-265-5412-2', '10.1016/j.csi.2024.103551', 360, 'eBook', 399.00, 'Published', 'book'),
(3, 'Environmental Sustainability in India', 'Challenges, Policies and Innovations', 4, 'An analysis of renewable energy strategies, waste management, and environmental conservation policies across Indian states.', 4, 'cover_env.png', 'files_env.pdf', 'preview_env.pdf', '978-81-7023-987-6', '10.1080/09640568.2024.1122334', 310, 'eBook', 299.00, 'Published', 'book'),
(4, 'Advanced Data Science', 'Predictive Modeling and Big Data Pipelines', 5, 'Advanced methodologies for data wrangling, feature engineering, distributed Spark pipelines, and MLOps.', 2, 'cover_ds.png', 'files_ds.pdf', 'preview_ds.pdf', '978-81-8404-512-3', '10.1145/3318464.3389700', 520, 'eBook', 599.00, 'Published', 'book'),
(5, 'Cloud Computing Essentials', 'Architecture, Virtualization and DevOps', 3, 'Core concepts of cloud architecture, container orchestration with Kubernetes, microservices, and serverless computing.', 5, 'cover_cloud.png', 'files_cloud.pdf', 'preview_cloud.pdf', '978-81-317-2101-5', '10.1109/TCC.2024.9876543', 410, 'eBook', 449.00, 'Published', 'book'),
(6, 'Smart Agriculture Technologies', 'IoT Sensors and Automated Farming', 6, 'Explores modern technological interventions in Indian agriculture including precision irrigation and drone monitoring.', 6, 'cover_agri.png', 'files_agri.pdf', 'preview_agri.pdf', '978-81-272-4321-9', '10.1016/j.compag.2024.108920', 290, 'eBook', 349.00, 'Published', 'book')
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);


/* Insert Research Papers */

INSERT INTO `research_papers` (`id`, `title`, `abstract`, `pdf`, `preview_pdf`, `doi`, `keywords`, `category_id`, `pages`, `publication_date`, `price`, `status`, `author_id`)
VALUES
(1, 'AI in Precision Agriculture', 'This paper proposes a low-cost IoT sensor network integrated with lightweight convolutional neural networks for automated crop disease detection in small-scale Indian farms.', 'paper_precision_agri.pdf', 'preview_precision_agri.pdf', '10.1109/TAPAS.2026.0101', 'AI, Precision Agriculture, IoT Sensors, Crop Disease Detection', 6, 12, '2026-03-15', 149.00, 'Published', 6),
(2, 'Deep Learning for Medical Imaging', 'A comprehensive study on 3D U-Net architectures for early tumor segmentation in low-field MRI scans with enhanced signal-to-noise ratio.', 'paper_medical_imaging.pdf', 'preview_medical_imaging.pdf', '10.1109/TMI.2026.0202', 'Deep Learning, Medical Imaging, MRI Segmentation, U-Net', 1, 16, '2026-04-10', 199.00, 'Published', 2),
(3, 'Blockchain in Supply Chain Management', 'Investigating permissioned Hyperledger Fabric ledgers for real-time tracking and verification of pharmaceutical supply chains across Maharashtra.', 'paper_blockchain_scm.pdf', 'preview_blockchain_scm.pdf', '10.1016/j.ijpe.2026.0303', 'Blockchain, Supply Chain, Hyperledger, Smart Contracts', 5, 14, '2026-05-02', 129.00, 'Published', 3),
(4, 'Smart Cities using IoT', 'Design and implementation of an integrated urban flood monitoring and smart traffic management framework for metropolitan municipalities.', 'paper_smart_cities.pdf', 'preview_smart_cities.pdf', '10.1109/JIOT.2026.0404', 'Smart Cities, IoT, Urban Flood Monitoring, Traffic Optimization', 8, 18, '2026-05-20', 179.00, 'Published', 1),
(5, 'Renewable Energy Forecasting', 'Hybrid LSTM-XGBoost time series models for accurate solar and wind energy generation prediction under variable monsoon weather conditions.', 'paper_renewable_energy.pdf', 'preview_renewable_energy.pdf', '10.1016/j.apenergy.2026.0505', 'Renewable Energy, LSTM, Solar Forecasting, Machine Learning', 4, 15, '2026-06-01', 159.00, 'Published', 4),
(6, 'Cyber Security using Machine Learning', 'Evaluating adversarial defense mechanisms and anomaly detection systems against zero-day exploits in enterprise cloud networks.', 'paper_cyber_security.pdf', 'preview_cyber_security.pdf', '10.1109/TDSC.2026.0606', 'Cyber Security, Machine Learning, Anomaly Detection, Zero-Day Exploits', 3, 20, '2026-06-18', 189.00, 'Published', 5)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);


/* Insert Book Requests */

INSERT INTO `book_requests` (`id`, `user_id`, `title`, `description`, `author_name`, `category_id`, `price`, `cover`, `file`, `preview_file`, `content_type`, `isbn`, `status`, `publishing_fee`, `payment_status`, `scanner_status`, `admin_notes`)
VALUES
(1, 1, 'Quantum Computing for Next Generation Software', 'Comprehensive manuscript covering quantum gates, Shor algorithm, and Qiskit programming.', 'Rahul Patil', 5, 499.00, 'cover_quantum.png', 'file_quantum.pdf', 'preview_quantum.pdf', 'book', '978-81-9876-012-3', 'pending', 500.00, 'unpaid', 'passed', 'Submitted manuscript pending initial admin review.'),
(2, 2, 'Genomic Data Processing with Python', 'Manuscript detailing bioinformatics algorithms for DNA sequencing and variant calling.', 'Sneha Kulkarni', 7, 599.00, 'cover_genomics.png', 'file_genomics.pdf', 'preview_genomics.pdf', 'book', '978-81-9876-013-4', 'approved', 600.00, 'paid', 'passed', 'Manuscript reviewed and approved. Publishing fee received via UPI.'),
(3, 5, 'High Speed Railway Bridge Infrastructure', 'Engineering analysis of prestressed concrete bridges under dynamic high-speed train loads.', 'Rohit Deshmukh', 10, 399.00, 'cover_railway.png', 'file_railway.pdf', 'preview_railway.pdf', 'book', '978-81-9876-014-5', 'rejected', 0.00, 'unpaid', 'failed', 'Manuscript rejected due to high similarity index detected in scanner.')
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);


/* Insert Publication Scans */

INSERT INTO `publication_scans` (`id`, `publication_id`, `publication_type`, `plagiarism_percentage`, `scanner_name`, `scan_status`, `report_path`)
VALUES
(1, 1, 'book_request', 1.50, 'Pluggable Content Scanner v1.0', 'passed', 'reports/scan_req_1.pdf'),
(2, 2, 'book_request', 3.20, 'Pluggable Content Scanner v1.0', 'passed', 'reports/scan_req_2.pdf'),
(3, 3, 'book_request', 28.70, 'Pluggable Content Scanner v1.0', 'failed', 'reports/scan_req_3.pdf')
ON DUPLICATE KEY UPDATE `scan_status` = VALUES(`scan_status`);


/* Insert Shopping Cart Items */

INSERT INTO `cart` (`id`, `user_id`, `book_id`, `publication_type`, `publication_id`, `quantity`, `unit_price`, `subtotal`)
VALUES
(1, 3, 1, 'book', 1, 1, 499.00, 499.00),
(2, 3, 2, 'book', 2, 1, 399.00, 399.00)
ON DUPLICATE KEY UPDATE `quantity` = VALUES(`quantity`);


/* Insert Orders */

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `full_name`, `email`, `phone`, `total_amount`, `status`, `payment_status`, `payment_method`, `invoice_number`)
VALUES
(1, 1, 'ORD2026072401', 'Rahul Patil', 'rahul@example.com', '9876543210', 499.00, 'completed', 'paid', 'UPI', 'INV-2026-001'),
(2, 2, 'ORD2026072402', 'Sneha Kulkarni', 'sneha@example.com', '9876543211', 399.00, 'pending', 'pending', 'Credit Card', NULL),
(3, 4, 'ORD2026072403', 'Priya Sharma', 'priya@example.com', '9876543213', 599.00, 'failed', 'failed', 'Debit Card', NULL)
ON DUPLICATE KEY UPDATE `status` = VALUES(`status`);


/* Insert Order Items */

INSERT INTO `order_items` (`id`, `order_id`, `book_id`, `publication_type`, `publication_id`, `quantity`, `price`, `subtotal`)
VALUES
(1, 1, 1, 'book', 1, 1, 499.00, 499.00),
(2, 2, 2, 'book', 2, 1, 399.00, 399.00),
(3, 3, 4, 'book', 4, 1, 599.00, 599.00)
ON DUPLICATE KEY UPDATE `price` = VALUES(`price`);


/* Insert Payments */

INSERT INTO `payments` (`id`, `order_id`, `transaction_id`, `upi_id`, `amount`, `status`, `payment_method`, `verified_by`, `verified_at`)
VALUES
(1, 1, 'UPI2026000001', 'rahul@upi', 499.00, 'success', 'UPI', 1, CURRENT_TIMESTAMP),
(2, 2, 'UPI2026000002', NULL, 399.00, 'pending', 'Card', NULL, NULL),
(3, 3, 'TXN2026000003', NULL, 599.00, 'failed', 'Net Banking', NULL, NULL)
ON DUPLICATE KEY UPDATE `status` = VALUES(`status`);


/* Insert Downloads Access Control */

INSERT INTO `downloads` (`id`, `user_id`, `book_id`, `publication_id`, `publication_type`, `order_id`, `preview_pdf`, `full_pdf`, `preview_page_limit`, `download_count`)
VALUES
(1, 1, 1, 1, 'book', 1, 'preview_ai.pdf', 'files_ai.pdf', 10, 3)
ON DUPLICATE KEY UPDATE `download_count` = VALUES(`download_count`);


/* Insert Admin Approvals Log */

INSERT INTO `admin_approvals` (`id`, `publication_id`, `publication_type`, `admin_id`, `action`, `comments`)
VALUES
(1, 2, 'book_request', 1, 'approve', 'Approved publication request after verification of manuscript and payment UPI2026000003.'),
(2, 3, 'book_request', 1, 'reject', 'Rejected publication request due to similarity index exceeding 25% threshold.')
ON DUPLICATE KEY UPDATE `action` = VALUES(`action`);

SET FOREIGN_KEY_CHECKS = 1;

COMMIT;
