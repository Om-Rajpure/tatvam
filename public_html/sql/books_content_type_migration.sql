-- Tatvam Publication: Books Content Type, ISBN/DOI, Pages, Format, Preview Migration
-- Run this on the live database: u679317752_tatvam
-- Date: 2026-07-24

ALTER TABLE `books`
  ADD COLUMN `content_type` ENUM('book','research_paper') NOT NULL DEFAULT 'book' AFTER `category_id`,
  ADD COLUMN `isbn` VARCHAR(50) NULL,
  ADD COLUMN `doi` VARCHAR(100) NULL,
  ADD COLUMN `pages` INT NULL,
  ADD COLUMN `format` ENUM('Paperback','eBook') DEFAULT 'eBook',
  ADD COLUMN `preview_file` VARCHAR(255) NULL;

ALTER TABLE `book_requests`
  ADD COLUMN `content_type` ENUM('book','research_paper') NOT NULL DEFAULT 'book' AFTER `category_id`,
  ADD COLUMN `isbn` VARCHAR(50) NULL,
  ADD COLUMN `doi` VARCHAR(100) NULL,
  ADD COLUMN `pages` INT NULL,
  ADD COLUMN `format` ENUM('Paperback','eBook') DEFAULT 'eBook',
  ADD COLUMN `preview_file` VARCHAR(255) NULL;
