# MODULE 04: CATEGORY MANAGEMENT SYSTEM

**Status:** ✅ COMPLETED (Existing)  
**Version:** 1.0.0  
**Dependencies:** Module 01 (Admin Management)

---

## 1. MODULE OVERVIEW

### 1.1 Purpose
Manage book categories with CRUD operations.

### 1.2 Current Implementation Status
- ✅ Add new categories
- ✅ Edit category names
- ✅ Delete categories
- ✅ Display categories list
- ✅ Filter books by category

---

## 2. DATABASE SCHEMA

### 2.1 Existing Table

**categories**
```sql
CREATE TABLE categories (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL
);
```

---

## 3. EXISTING FILES

### 3.1 Frontend Files
- `add-category.php` - Add category form
- `edit-category.php` - Edit category form
- `category.php` - Books by category page

### 3.2 Backend Files
- `php/add-category.php` - Add category handler
- `php/edit-category.php` - Edit category handler
- `php/delete-category.php` - Delete category handler
- `php/func-category.php` - Category helper functions

---

## 4. FUNCTIONAL SPECIFICATIONS

### 4.1 Add Category
**File:** `add-category.php` → `php/add-category.php`
- **Input:** Category Name
- **Validation:** Name not empty
- **Process:** Insert to database
- **Output:** Success/Error message

### 4.2 Edit Category
**File:** `edit-category.php` → `php/edit-category.php`
- **Input:** Category ID, Updated Name
- **Validation:** Name not empty
- **Process:** Update database
- **Output:** Success/Error message

### 4.3 Delete Category
**File:** `php/delete-category.php`
- **Input:** Category ID
- **Process:** Delete from database
- **Output:** Success/Error message
- **Note:** Should check for books before deletion

### 4.4 Display Categories
**File:** `admin.php`
- **Display:** Table with category names and actions

### 4.5 Books by Category
**File:** `category.php`
- **Input:** Category ID
- **Display:** All books in that category

---

## 5. HELPER FUNCTIONS

### 5.1 Existing Functions (php/func-category.php)

```php
get_all_categories($con)     // Get all categories
get_category($con, $id)      // Get single category
```

---

## 6. ENHANCEMENT SCOPE (New Development)

### 6.1 Recommended Improvements
- Add category description
- Add category icon/image
- Category hierarchy (parent-child)
- Prevent deletion if books exist
- Category statistics (book count)

### 6.2 Database Enhancement (Optional)

```sql
ALTER TABLE categories 
ADD COLUMN description TEXT AFTER name,
ADD COLUMN icon VARCHAR(255) AFTER description,
ADD COLUMN parent_id INT(11) DEFAULT NULL AFTER icon,
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
```

---

## 7. TESTING CHECKLIST

### 7.1 Existing Features
- ✅ Admin can add category
- ✅ Admin can edit category name
- ✅ Admin can delete category
- ✅ Categories display in admin panel
- ✅ Books filter by category works

---

## 8. API ENDPOINTS

### 8.1 Existing
- `POST php/add-category.php` - Add category
- `POST php/edit-category.php` - Update category
- `GET php/delete-category.php?id=` - Delete category

---

## 9. INTEGRATION POINTS

### 9.1 Current Integrations
- Module 02: Book Management (category_id foreign key)

---

**Last Updated:** 2024  
**Module Owner:** Content Management Team
