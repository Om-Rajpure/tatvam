# Cart Functionality - Quick Setup Guide

## Issue Fixed
Cart wasn't working because:
1. Cart table didn't exist in database
2. Books table missing price column
3. All books had price = 0 (free)

## Setup Steps

### Step 1: Run Test Page
Visit: `http://localhost/bookstore/test-cart.php`

This will automatically:
- ✅ Create cart table if missing
- ✅ Add price column to books if missing
- ✅ Add sample prices to books
- ✅ Show current session status

### Step 2: Verify Setup
The test page will show:
- Cart table status
- Books with prices
- Your login status

### Step 3: Test Cart
1. **Login** as a customer user
2. **Go to homepage** - You'll see books with prices
3. **Click cart icon** (+ button) on a paid book
4. **View cart** - Should show the book
5. **Remove/Clear** - Test these functions

## What's Fixed

### Homepage (index.php)
- ✅ Shows actual book prices
- ✅ "Add to Cart" button for paid books (price > 0)
- ✅ "Download" button for free books (price = 0)
- ✅ Cart icon shows item count
- ✅ "In Cart" indicator for already added books

### Cart Page (cart.php)
- ✅ Shows all cart items
- ✅ Displays book details
- ✅ Shows total amount
- ✅ Remove individual items
- ✅ Clear entire cart
- ✅ Empty cart message

### Add to Cart (php/add-to-cart.php)
- ✅ Validates user login
- ✅ Checks book exists
- ✅ Prevents free books in cart
- ✅ Prevents duplicates
- ✅ Success/Error messages

## Database Tables

### Cart Table
```sql
cart (
    id, 
    user_id, 
    book_id, 
    created_at
)
```

### Books Table (Updated)
```sql
books (
    id,
    title,
    author_id,
    description,
    price,  <-- NEW COLUMN
    category_id,
    cover,
    file
)
```

## Testing Checklist

- [ ] Visit test-cart.php
- [ ] Verify cart table exists
- [ ] Verify books have prices
- [ ] Login as customer
- [ ] See "Add to Cart" buttons
- [ ] Add book to cart
- [ ] See cart count badge
- [ ] View cart page
- [ ] See book in cart
- [ ] Remove item works
- [ ] Clear cart works
- [ ] Free books download directly

## Troubleshooting

### "Cart table doesn't exist"
- Run: `test-cart.php` - it will create it automatically
- Or manually run: `sql/cart_table.sql`

### "All books show as Free"
- Run: `test-cart.php` - it will add sample prices
- Or manually run: `sql/add_book_prices.sql`

### "Add to Cart not working"
- Check you're logged in as customer (not admin)
- Check book has price > 0
- Check cart table exists

### "Cart count not showing"
- Verify you're logged in
- Check session: `$_SESSION['user_type'] = 'customer'`
- Refresh page after adding items

## Next Steps

1. ✅ Cart functionality working
2. ⏳ Add more books with prices (via admin)
3. ⏳ Proceed to Module 07 (Checkout & Orders)
4. ⏳ Implement payment system

---

**Status**: ✅ FIXED & WORKING  
**Test URL**: http://localhost/bookstore/test-cart.php
