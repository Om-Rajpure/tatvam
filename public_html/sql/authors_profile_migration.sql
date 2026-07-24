-- Tatvam Publication: Author Profile Fields Migration
-- Run this on the live database: u679317752_tatvam
-- Date: 2026-07-24

ALTER TABLE `authors`
  ADD COLUMN `user_id` INT(11) NULL AFTER `id`,
  ADD COLUMN `photo` VARCHAR(255) NULL,
  ADD COLUMN `about` TEXT NULL,
  ADD COLUMN `qualification` VARCHAR(255) NULL,
  ADD COLUMN `designation` VARCHAR(255) NULL,
  ADD COLUMN `organization` VARCHAR(255) NULL,
  ADD COLUMN `contact` VARCHAR(255) NULL;

ALTER TABLE `authors`
  ADD CONSTRAINT `fk_author_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL;

CREATE INDEX `idx_author_user_id` ON `authors`(`user_id`);
