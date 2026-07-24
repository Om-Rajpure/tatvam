-- Add price column to books table
ALTER TABLE books 
ADD COLUMN IF NOT EXISTS price DECIMAL(10,2) DEFAULT 0.00 AFTER description;

-- Update some books with sample prices for testing
UPDATE books SET price = 299.00 WHERE id = 1;
UPDATE books SET price = 399.00 WHERE id = 2;
UPDATE books SET price = 499.00 WHERE id = 3;
UPDATE books SET price = 199.00 WHERE id = 4;
UPDATE books SET price = 599.00 WHERE id = 5;
