<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'customer') {
    header("Location: user-login.php");
    exit;
}

include "db_conn.php";
include "php/func-category.php";
include "php/func-author.php";

if (function_exists('get_author_by_user_id')) {
    $existing_author = get_author_by_user_id($conn, $_SESSION['user_id']);
    if (!$existing_author) {
        header("Location: author-registration.php?redirect=submit-book.php");
        exit;
    }
}

$categories = get_all_categories($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Book for Publishing - Tatvam Publication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php $current_page = 'submit-book.php'; include "php/navbar.php"; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-upload"></i> Submit Book for Publishing</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
                        <?php endif; ?>
                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
                        <?php endif; ?>

                        <form action="php/submit-book-request.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Content Type *</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="content_type" id="type_book" value="book" checked>
                                    <label class="btn btn-outline-primary" for="type_book"><i class="bi bi-book"></i> Book</label>
                                    <input type="radio" class="btn-check" name="content_type" id="type_paper" value="research_paper">
                                    <label class="btn btn-outline-primary" for="type_paper"><i class="bi bi-journal-text"></i> Research Paper</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Book Title *</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Author Name *</label>
                                <input type="text" class="form-control" name="author_name" required>
                                <small class="text-muted">Enter your name or pen name</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description *</label>
                                <textarea class="form-control" name="description" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Category *</label>
                                <select class="form-control" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- ISBN (shown for Books) -->
                            <div class="mb-3" id="isbn-field">
                                <label class="form-label">ISBN</label>
                                <input type="text" class="form-control" name="isbn" placeholder="e.g. 978-3-16-148410-0">
                                <small class="text-muted">International Standard Book Number (optional at submission)</small>
                            </div>

                            <!-- DOI (shown for Research Papers) -->
                            <div class="mb-3" id="doi-field" style="display:none;">
                                <label class="form-label">DOI</label>
                                <input type="text" class="form-control" name="doi" placeholder="e.g. 10.1000/xyz123">
                                <small class="text-muted">Digital Object Identifier (optional at submission)</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Price (₹) *</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="price" value="0.00" required>
                                <small class="text-muted">Set 0 for free books</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Number of Pages *</label>
                                        <input type="number" class="form-control" name="pages" min="1" required placeholder="e.g. 250">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Format *</label>
                                        <select class="form-control" name="format" required>
                                            <option value="eBook">eBook (Digital PDF)</option>
                                            <option value="Paperback">Paperback</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Book Cover Image *</label>
                                <input type="file" class="form-control" name="cover" accept="image/*" required>
                                <small class="text-muted">Accepted: All image formats (JPG, JPEG, PNG, GIF, etc.)</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Book File (PDF) *</label>
                                <input type="file" class="form-control" name="file" accept=".pdf" required>
                                <small class="text-muted">Accepted: PDF only</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Preview PDF (Optional but Recommended)</label>
                                <input type="file" class="form-control" name="preview_file" accept=".pdf">
                                <small class="text-muted">Upload a preview version (e.g., up to Table of Contents) for users who haven't purchased yet. If not provided, unregistered users won't see a preview.</small>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Your book will be reviewed by our admin team. You'll be notified once it's approved and published.
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-send"></i> Submit for Review
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeInputs = document.querySelectorAll('input[name="content_type"]');
        const isbnField = document.getElementById('isbn-field');
        const doiField = document.getElementById('doi-field');
        typeInputs.forEach(function(input) {
            input.addEventListener('change', function() {
                if (this.value === 'research_paper') {
                    isbnField.style.display = 'none';
                    doiField.style.display = 'block';
                } else {
                    isbnField.style.display = 'block';
                    doiField.style.display = 'none';
                }
            });
        });
    });
    </script>
</body>
</html>
