# Tatvam Publication - Database Schema Analysis & Justification Report

## Executive Summary
This document provides a comprehensive technical analysis of the **Tatvam Publication** Core PHP + MySQL project. By scanning every PHP file, functions module (`php/func-*.php`), administrative endpoint, and SQL migration, we have reverse-engineered the complete MySQL database schema. 

The accompanying SQL file [`public_html/sql/complete_database_schema.sql`](file:///c:/xampp/htdocs/tatvampublication/public_html/sql/complete_database_schema.sql) reconstructs all missing tables, foreign key constraints, indexes, and sample seed data required to run the application seamlessly without losing existing data.

---

## Entity Relationship Overview

```
                      +-------------------+
                      |      admin        |
                      +-------------------+
                               |
                               | (verifies/approves)
                               v
+------------------+   +-------------------+   +--------------------+
|      users       |-->|      authors      |-->|       books        |
+------------------+   +-------------------+   +--------------------+
  |      |                      |                 |           |
  |      |                      v                 v           |
  |      |             +-------------------+ +------------+  |
  |      |             |  research_papers  | | categories |  |
  |      |             +-------------------+ +------------+  |
  |      |                                                    |
  |      +---------------------+                              |
  |                            |                              |
  v                            v                              v
+------------------+   +-------------------+        +--------------------+
|       cart       |   |   book_requests   |        |     downloads      |
+------------------+   +-------------------+        +--------------------+
  |                            | (scanner check)               ^
  v                            v                               | (grants access)
+------------------+   +-------------------+                   |
|      orders      |   | publication_scans |                   |
+------------------+   +-------------------+                   |
  |                                                            |
  v                                                            |
+------------------+-------------------------------------------+
|     payments     |
+------------------+
```

---

## Detailed Table Justifications & Source Code References

### 1. `admin`
* **Status**: Existing Core Table
* **Code Reference**: `php/auth.php`, `admin.php`
* **Justification**: Stores system administrator credentials for accessing the administrative dashboard.
* **Key Columns**: `id` (PK), `email` (UNIQUE), `password` (hashed).

### 2. `users`
* **Status**: Existing / Enhanced Table
* **Code Reference**: `register.php`, `user-login.php`, `php/func-user.php`, `php/func-order.php`
* **Justification**: Manages customer and author accounts. Stores customer names, emails, phones, and hashed passwords.
* **Key Columns**: `id` (PK), `email` (UNIQUE), `phone`, `created_at`, `updated_at`.

### 3. `categories`
* **Status**: Existing Core Table
* **Code Reference**: `categories.php`, `add-category.php`, `php/func-category.php`
* **Justification**: Groups books and research papers into academic and domain subjects.
* **Key Columns**: `id` (PK), `name` (UNIQUE).

### 4. `authors`
* **Status**: Enhanced Table
* **Code Reference**: `authors.php`, `author.php`, `add-author.php`, `php/func-author.php`, `php/create-author-profile.php`
* **Justification**: Stores extended professional profiles for authors linked to registered user accounts (`user_id`).
* **Key Columns**: `id` (PK), `user_id` (FK to `users.id`), `name`, `photo`, `about`, `qualification`, `designation`, `organization`, `contact`.

### 5. `books`
* **Status**: Enhanced Table
* **Code Reference**: `books.php`, `book-detail.php`, `add-book.php`, `php/func-book.php`, `download.php`
* **Justification**: Primary catalogue for published books. Stores file references, preview PDFs, ISBN, DOI, page count, format (eBook/Paperback), and price.
* **Key Columns**: `id` (PK), `author_id` (FK to `authors.id`), `category_id` (FK to `categories.id`), `price`, `cover`, `file`, `preview_file`, `status`, `content_type`.

### 6. `research_papers`
* **Status**: NEW Table
* **Code Reference**: Prompt Module Requirement & `php/func-author.php` (`get_books_by_author_typed`)
* **Justification**: Provides dedicated schema support for academic research paper submissions, storing abstracts, keywords, DOIs, page counts, and research PDF previews separate from standard multi-chapter books.
* **Key Columns**: `id` (PK), `title`, `abstract`, `pdf`, `preview_pdf`, `doi`, `keywords`, `category_id` (FK), `author_id` (FK), `price`, `status`.

### 7. `book_requests`
* **Status**: Enhanced Table
* **Code Reference**: `admin-book-requests.php`, `admin-view-request.php`, `my-book-requests.php`, `php/func-book-request.php`
* **Justification**: Tracks author manuscript submission workflows, publishing fee assessments, payment status, and admin approval state.
* **Key Columns**: `id` (PK), `user_id` (FK), `title`, `category_id` (FK), `status` (`pending`, `approved`, `payment_pending`, `published`, `rejected`), `publishing_fee`, `payment_status`, `transaction_id`, `scanner_status`.

### 8. `publication_scans`
* **Status**: NEW Table
* **Code Reference**: Prompt Scanner Module & `php/func-scanner.php`
* **Justification**: Records automated and manual plagiarism and content quality scans for submitted manuscripts before admin approval.
* **Key Columns**: `id` (PK), `publication_id`, `publication_type`, `plagiarism_percentage`, `scanner_name`, `scan_status`, `report_path`.

### 9. `cart`
* **Status**: Existing / Enhanced Table
* **Code Reference**: `cart.php`, `php/func-cart.php`, `php/add-to-cart.php`, `debug-cart.php`
* **Justification**: Maintains user active shopping cart items before checkout.
* **Key Columns**: `id` (PK), `user_id` (FK), `book_id` (FK), `publication_type`, `publication_id`, `quantity`, `unit_price`, `subtotal`.

### 10. `orders`
* **Status**: Enhanced Table
* **Code Reference**: `checkout.php`, `admin-orders.php`, `my-orders.php`, `order-details.php`, `php/func-order.php`
* **Justification**: Main header record for purchases. `func-order.php` explicitly inserts `full_name`, `email`, and `phone` directly into `orders` during cart checkout.
* **Key Columns**: `id` (PK), `user_id` (FK), `order_number` (UNIQUE), `full_name`, `email`, `phone`, `total_amount`, `status`, `payment_status`, `invoice_number`.

### 11. `order_items`
* **Status**: Enhanced Table
* **Code Reference**: `admin-order-details.php`, `order-details.php`, `php/func-order.php`
* **Justification**: Stores line-item breakdowns of books and papers purchased within an order.
* **Key Columns**: `id` (PK), `order_id` (FK), `book_id` (FK), `publication_type`, `price`, `subtotal`.

### 12. `payments`
* **Status**: Enhanced Table
* **Code Reference**: `admin-verify-payments.php`, `payment.php`, `pay-publishing-fee.php`, `php/func-payment.php`
* **Justification**: Logs UPI payment details, transaction IDs, UPI IDs, verification timestamps, and admin verifier IDs (`verified_by`).
* **Key Columns**: `id` (PK), `order_id` (FK), `transaction_id` (UNIQUE), `upi_id`, `amount`, `status`, `verified_by`, `verified_at`.

### 13. `downloads`
* **Status**: NEW Table
* **Code Reference**: `download.php`, `SCOPE_DOCUMENTATION.md` Section 3.1
* **Justification**: Controls file access permissions. Non-registered or unpaid users can only download `preview_pdf`. Paid users or free books (`price = 0`) get full PDF download rights with access logging (`download_count`, `last_downloaded`).
* **Key Columns**: `id` (PK), `user_id` (FK), `book_id` (FK), `order_id` (FK), `preview_pdf`, `full_pdf`, `preview_page_limit`, `download_count`, `last_downloaded`.

### 14. `admin_approvals`
* **Status**: NEW Table
* **Code Reference**: Admin Workflow Requirement & `php/func-book-request.php`
* **Justification**: Maintains an audit log of all admin approvals, rejections, payment verifications, and publishing actions.
* **Key Columns**: `id` (PK), `publication_id`, `publication_type`, `admin_id` (FK), `action`, `comments`, `created_at`.

---

## Instructions for Installation / Execution

1. Open phpMyAdmin or MySQL CLI connected to your database (e.g. `online_book_store_db` or `u679317752_tatvam`).
2. Run the SQL script:
   ```bash
   mysql -u root -p online_book_store_db < public_html/sql/complete_database_schema.sql
   ```
3. Or in phpMyAdmin:
   - Select your target database.
   - Click **Import** $\rightarrow$ **Choose File** $\rightarrow$ select `complete_database_schema.sql`.
   - Click **Go**.
