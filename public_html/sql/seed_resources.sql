/* ============================================================================
   TATVAM PUBLICATION — RESOURCE SEED SCRIPT
   REQ-20: Integrate provided resources (books, research papers, authors)
   Safe to run multiple times — IDEMPOTENT (INSERT IGNORE / duplicate checks)
   ============================================================================

   Resources Provided:
   1. "The Golden River final 30-june hard.pdf"  → Book
   2. "Contemporary research perspective in commerce.pdf" → Research Paper
   3. "Digital Transformation Driving Economic Growth.pdf" → Book/Research Paper

   IMPORTANT: Run AFTER the complete_database_schema.sql has been applied.
   ============================================================================ */

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. ENSURE REQUIRED CATEGORIES EXIST
-- ============================================================

INSERT IGNORE INTO `categories` (`name`) VALUES
  ('Commerce & Business'),
  ('Digital Transformation'),
  ('Literature & Fiction'),
  ('Economics'),
  ('Social Sciences'),
  ('Technology'),
  ('Academic Research');

-- ============================================================
-- 2. ENSURE AN ADMIN-SEEDED AUTHOR EXISTS FOR OFFICIAL CONTENT
--    (not linked to a user account — user_id NULL for seeded content)
-- ============================================================

-- Author for "The Golden River" (literary/fiction)
INSERT IGNORE INTO `authors` (`name`, `about`, `qualification`, `designation`, `organization`, `user_id`)
SELECT 'Tatvam Publication Editorial', 'Official publication by Tatvam Publication editorial board.', 'Editorial Board', 'Publisher', 'Tatvam Publication', NULL
WHERE NOT EXISTS (
  SELECT 1 FROM `authors` WHERE `name` = 'Tatvam Publication Editorial'
);

-- ============================================================
-- 3. SEED BOOKS — THE GOLDEN RIVER
-- ============================================================
-- Only insert if a book with this title does not already exist

INSERT INTO `books`
  (`title`, `author_id`, `description`, `category_id`, `cover`, `file`, `preview_file`,
   `price`, `format`, `pages`, `content_type`, `status`)
SELECT
  'The Golden River',
  (SELECT `id` FROM `authors` WHERE `name` = 'Tatvam Publication Editorial' LIMIT 1),
  'The Golden River is a compelling literary work that explores themes of perseverance, identity, and the human journey. Published by Tatvam Publication, this book reflects on the transformative power of experience and the pursuit of meaning in everyday life. A richly written narrative that speaks to readers across generations.',
  (SELECT `id` FROM `categories` WHERE `name` = 'Literature & Fiction' LIMIT 1),
  'cover_golden_river.jpg',
  'the-golden-river.pdf',
  NULL,
  299.00,
  'Paperback',
  NULL,
  'book',
  'Published'
WHERE NOT EXISTS (
  SELECT 1 FROM `books` WHERE LOWER(TRIM(`title`)) = LOWER(TRIM('The Golden River'))
);

-- ============================================================
-- 4. SEED RESEARCH PAPERS — CONTEMPORARY RESEARCH IN COMMERCE
-- ============================================================

INSERT INTO `books`
  (`title`, `author_id`, `description`, `category_id`, `cover`, `file`, `preview_file`,
   `price`, `format`, `pages`, `content_type`, `status`, `doi`)
SELECT
  'Contemporary Research Perspective in Commerce',
  (SELECT `id` FROM `authors` WHERE `name` = 'Tatvam Publication Editorial' LIMIT 1),
  'This research paper presents contemporary perspectives in the field of commerce, examining evolving trends in trade, digital commerce, financial systems, and consumer behaviour. The paper synthesizes recent empirical findings and theoretical frameworks to offer a comprehensive overview of the current state of commercial research and emerging paradigms shaping the discipline.',
  (SELECT `id` FROM `categories` WHERE `name` = 'Commerce & Business' LIMIT 1),
  'cover_commerce_research.jpg',
  'contemporary-research-commerce.pdf',
  NULL,
  0.00,
  'eBook',
  NULL,
  'research_paper',
  'Published',
  NULL
WHERE NOT EXISTS (
  SELECT 1 FROM `books` WHERE LOWER(TRIM(`title`)) = LOWER(TRIM('Contemporary Research Perspective in Commerce'))
);

-- ============================================================
-- 5. SEED BOOKS — DIGITAL TRANSFORMATION DRIVING ECONOMIC GROWTH
-- ============================================================

INSERT INTO `books`
  (`title`, `author_id`, `description`, `category_id`, `cover`, `file`, `preview_file`,
   `price`, `format`, `pages`, `content_type`, `status`)
SELECT
  'Digital Transformation Driving Economic Growth',
  (SELECT `id` FROM `authors` WHERE `name` = 'Tatvam Publication Editorial' LIMIT 1),
  'This comprehensive work examines the profound role of digital transformation in reshaping economic landscapes across industries and nations. Drawing on case studies, empirical data, and policy analysis, the book explores how technology adoption, digital infrastructure, e-governance, and innovation ecosystems are catalysing sustainable economic growth. An essential reference for policymakers, researchers, and business leaders.',
  (SELECT `id` FROM `categories` WHERE `name` = 'Digital Transformation' LIMIT 1),
  'cover_digital_transformation.jpg',
  'digital-transformation-economic-growth.pdf',
  NULL,
  499.00,
  'eBook',
  NULL,
  'book',
  'Published'
WHERE NOT EXISTS (
  SELECT 1 FROM `books` WHERE LOWER(TRIM(`title`)) = LOWER(TRIM('Digital Transformation Driving Economic Growth'))
);

-- ============================================================
-- 6. VERIFY (optional — comment out if not needed)
-- ============================================================

SELECT 'Categories seeded:' as info, COUNT(*) as total FROM `categories`;
SELECT 'Authors seeded:' as info, COUNT(*) as total FROM `authors`;
SELECT 'Books/Papers seeded:' as info, COUNT(*) as total FROM `books`;
SELECT `id`, `title`, `content_type`, `price`, `status` FROM `books` ORDER BY `id` DESC LIMIT 10;

SET FOREIGN_KEY_CHECKS = 1;
