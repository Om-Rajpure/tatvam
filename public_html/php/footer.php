<?php
/**
 * TATVAM PUBLICATION — SHARED FOOTER COMPONENT
 * Include at the bottom of every public-facing page (before </body>).
 */
$current_year = date('Y');
?>

<!-- ====== FOOTER ====== -->
<footer class="footer">
    <div class="container">
        <div class="row g-5">
            <!-- Brand column -->
            <div class="col-lg-4">
                <?php
                $logo_path = __DIR__ . '/../img/logo-tatvam.png';
                if (file_exists($logo_path)): ?>
                    <div class="footer-brand mb-3">
                        <img src="img/logo-tatvam.png" alt="Tatvam Publication">
                    </div>
                <?php else: ?>
                    <div class="footer-brand-text mb-2">Tatvam Publication</div>
                <?php endif; ?>
                <p>A trusted platform for discovering, reading, purchasing, and publishing academic books and research papers.</p>
                <div class="social-links mt-3">
                    <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                </div>
            </div>

            <!-- Quick links -->
            <div class="col-sm-6 col-lg-2">
                <h5>Explore</h5>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="books.php?type=book">Books</a></li>
                    <li><a href="books.php?type=research_paper">Research Papers</a></li>
                    <li><a href="categories.php">Categories</a></li>
                    <li><a href="authors.php">Authors</a></li>
                </ul>
            </div>

            <!-- Publish / Account -->
            <div class="col-sm-6 col-lg-2">
                <h5>Publish</h5>
                <ul class="footer-links">
                    <?php if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer'): ?>
                        <li><a href="submit-book.php">Submit a Book</a></li>
                        <li><a href="submit-book.php">Submit a Paper</a></li>
                        <li><a href="my-book-requests.php">My Submissions</a></li>
                    <?php else: ?>
                        <li><a href="user-login.php?redirect=submit-book.php">Publish a Book</a></li>
                        <li><a href="user-login.php?redirect=submit-book.php">Publish a Paper</a></li>
                        <li><a href="register.php">Create Account</a></li>
                    <?php endif; ?>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>

            <!-- Contact + Newsletter -->
            <div class="col-lg-4">
                <h5>Stay Updated</h5>
                <p style="margin-bottom:14px;">Subscribe to receive updates on new publications and announcements.</p>
                <form class="newsletter-form" onsubmit="return false;">
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Your email address" aria-label="Email address">
                        <button class="btn" type="submit">Subscribe</button>
                    </div>
                </form>
                <div class="mt-4">
                    <p style="font-size:13px; color:rgba(255,255,255,0.55); margin-bottom:4px;">
                        <i class="bi bi-envelope me-1"></i> info@tatvampublication.com
                    </p>
                    <p style="font-size:13px; color:rgba(255,255,255,0.55); margin-bottom:0;">
                        <i class="bi bi-geo-alt me-1"></i> India
                    </p>
                </div>
            </div>
        </div>

        <hr>

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <p class="mb-0" style="font-size:13px;">&copy; <?= $current_year ?> Tatvam Publication. All rights reserved.</p>
            <div class="d-flex gap-3">
                <a href="reference.php" style="font-size:12.5px; color:rgba(255,255,255,0.45); text-decoration:none;">Reference</a>
                <a href="about.php" style="font-size:12.5px; color:rgba(255,255,255,0.45); text-decoration:none;">About</a>
                <a href="contact.php" style="font-size:12.5px; color:rgba(255,255,255,0.45); text-decoration:none;">Contact</a>
            </div>
        </div>
    </div>
</footer>
