# MODULE 02: BOOK MANAGEMENT SYSTEM

**Status:** ✅ COMPLETED (Existing) | 🔄 ENHANCEMENT REQUIRED  
**Version:** 1.0.0 → 2.0.0  
**Dependencies:** Module 01 (Admin Management)

---

## 1. MODULE OVERVIEW

### 1.1 Purpose
Complete book lifecycle management including CRUD operations and file handling.

### 1.2 Current Implementation Status
- ✅ Add new books
- ✅ Edit book details
- ✅ Delete books
- ✅ Upload book cover images
- ✅ Upload book files (PDF, DOCX, PPTX)
- ✅ Display books on storefront
- ❌ Book pricing (NEW REQUIREMENT)

---

## 2. DATABASE SCHEMA

### 2.1 Existing Table

**books**
```sql
CREATE TABLE books (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author_id INT(11) NOT NULL,
    description TEXT NOT NULL,
    category_id INT(11) NOT NULL,
    cover VARCHAR(255) NOT NULL,
    file VARCHAR(255) NOT NULL
);
```

### 2.2 Required Modification

**Add price column:**
```sql
ALTER TABLE books 
ADD COLUMN price DECIMAL(10,2) DEFAULT 0.00 AFTER description;
```

**Updated Schema:**
```sql
CREATE TABLE books (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author_id INT(11) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) DEFAULT 0.00,
    category_id INT(11) NOT NULL,
    cover VARCHAR(255) NOT NULL,
    file VARCHAR(255) NOT NULL
);
```

---

## 3. EXISTING FILES

### 3.1 Frontend Files
- `add-book.php` - Add book form
- `edit-book.php` - Edit book form
- `index.php` - Display books (storefront)
- `search.php` - Search books
- `category.php` - Books by category
- `author.php` - Books by author

### 3.2 Backend Files
- `php/add-book.php` - Add book handler
- `php/edit-book.php` - Edit book handler
- `php/delete-book.php` - Delete book handler
- `php/func-book.php` - Book helper functions
- `php/func-file-upload.php` - File upload handler
- `php/func-validation.php` - Validation functions

### 3.3 Upload Directories
- `uploads/cover/` - Book cover images
- `uploads/files/` - Book files

---

## 4. FUNCTIONAL SPECIFICATIONS

### 4.1 Add Book (Existing)
**File:** `add-book.php` → `php/add-book.php`
- **Input:** Title, Description, Author, Category, Cover Image, File
- **Validation:**
  - All fields required
  - Cover: jpg, jpeg, png
  - File: pdf, docx, pptx
- **Process:**
  - Upload cover to `uploads/cover/`
  - Upload file to `uploads/files/`
  - Generate unique filenames
  - Insert to database
- **Output:** Success/Error message

### 4.2 Edit Book (Existing)
**File:** `edit-book.php` → `php/edit-book.php`
- **Input:** Book ID, Updated fields
- **Options:**
  - Update data only
  - Update cover only
  - Update file only
  - Update both cover and file
- **Process:**
  - Delete old files if replaced
  - Upload new files
  - Update database
- **Output:** Success/Error message

### 4.3 Delete Book (Existing)
**File:** `php/delete-book.php`
- **Input:** Book ID
- **Process:**
  - Fetch book details
  - Delete cover file
  - Delete book file
  - Delete database record
- **Output:** Success/Error message

### 4.4 Display Books (Existing)
**File:** `index.php`
- **Display:**
  - Book cover
  - Title
  - Author name
  - Description
  - Category name
  - Open button
  - Download button

### 4.5 Search Books (Existing)
**File:** `search.php`
- **Input:** Search keyword
- **Search:** Title and Description (LIKE query)
- **Output:** Matching books

### 4.6 Filter by Category (Existing)
**File:** `category.php`
- **Input:** Category ID
- **Output:** Books in that category

### 4.7 Filter by Author (Existing)
**File:** `author.php`
- **Input:** Author ID
- **Output:** Books by that author

---

## 5. HELPER FUNCTIONS

### 5.1 Existing Functions (php/func-book.php)

```php
get_all_books($con)          // Get all books
get_book($con, $id)          // Get single book
search_books($con, $key)     // Search books
get_books_by_category($con, $id)  // Filter by category
get_books_by_author($con, $id)    // Filter by author
```

### 5.2 File Upload Functions (php/func-file-upload.php)

```php
upload_file($files, $allowed_exs, $path)  // Upload and validate files
```

### 5.3 Validation Functions (php/func-validation.php)

```php
is_empty($var, $text, $location, $ms, $data)  // Check empty fields
```

---

## 6. ENHANCEMENT SCOPE (New Development)

### 6.1 Database Changes
- ✅ Add `price` column to books table

### 6.2 Frontend Modifications

**add-book.php:**
```php
// Add price input field
<input type="number" step="0.01" name="book_price" placeholder="0.00">
```

**edit-book.php:**
```php
// Add price input field with current value
<input type="number" step="0.01" name="book_price" value="<?=$book['price']?>">
```

**index.php:**
```php
// Display price
if ($book['price'] > 0) {
    echo "₹ " . number_format($book['price'], 2);
    // Show "Buy Now" button instead of "Download"
} else {
    echo "Free";
    // Show "Download" button
}
```

**admin.php:**
```php
// Add price column in books table
<td>₹ <?=number_format($book['price'], 2)?></td>
```

### 6.3 Backend Modifications

**php/add-book.php:**
```php
// Add price handling
$price = $_POST['book_price'];
// Validate price (non-negative)
// Insert with price
```

**php/edit-book.php:**
```php
// Add price update
$price = $_POST['book_price'];
// Update query includes price
```

### 6.4 New Helper Functions

**php/func-book.php:**
```php
get_free_books($con)         // Get books where price = 0
get_paid_books($con)         // Get books where price > 0
get_books_by_price_range($con, $min, $max)  // Filter by price
```

---

## 7. SECURITY CONSIDERATIONS

### 7.1 Existing Security
- ✅ File type validation
- ✅ Unique filename generation
- ✅ Prepared statements
- ✅ Admin authentication required

### 7.2 Additional Security (New)
- Validate price is numeric and non-negative
- Prevent price manipulation
- Sanitize price input

---

## 8. TESTING CHECKLIST

### 8.1 Existing Features
- ✅ Admin can add book with all fields
- ✅ Admin can edit book details
- ✅ Admin can delete book
- ✅ Files upload correctly
- ✅ Books display on storefront
- ✅ Search works correctly
- ✅ Category filter works
- ✅ Author filter works

### 8.2 New Features Testing
- ⏳ Admin can set book price
- ⏳ Price displays correctly on storefront
- ⏳ Free books show "Free" label
- ⏳ Paid books show price and "Buy Now"
- ⏳ Price validation works
- ⏳ Price updates correctly

---

## 9. FILE UPLOAD SPECIFICATIONS

### 9.1 Allowed File Types
**Cover Images:**
- jpg, jpeg, png
- Max size: 5MB (recommended)

**Book Files:**
- pdf, docx, pptx
- Max size: 50MB (recommended)

### 9.2 File Naming Convention
- Format: `uniqid()` + `.extension`
- Example: `63f8a9b2c1d4e.pdf`

---

## 10. API ENDPOINTS

### 10.1 Existing
- `POST php/add-book.php` - Add new book
- `POST php/edit-book.php` - Update book
- `GET php/delete-book.php?id=` - Delete book

### 10.2 Required (New)
- None (existing endpoints will be modified)

---

## 11. INTEGRATION POINTS

### 11.1 Current Integrations
- Module 03: Author Management
- Module 04: Category Management

### 11.2 New Integrations (Required)
- Module 06: Shopping Cart (price data)
- Module 08: Order Management (book details)
- Module 10: Download Management (access control)

---

## 12. MIGRATION SCRIPT

```sql
-- Add price column to existing books
ALTER TABLE books 
ADD COLUMN price DECIMAL(10,2) DEFAULT 0.00 AFTER description;

-- Set all existing books as free
UPDATE books SET price = 0.00;
```

---

## 13. MAINTENANCE NOTES

### 13.1 Known Issues
- None reported

### 13.2 Future Improvements
- Bulk price update
- Price history tracking
- Discount management
- Dynamic pricing
- Price comparison

---

**Last Updated:** 2024  
**Module Owner:** Book Management Team
