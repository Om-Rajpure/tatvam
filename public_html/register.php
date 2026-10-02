<?php
session_start();
if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
    header("Location: index.php");
    exit;
}
$redirect_param = isset($_GET['redirect']) ? urlencode($_GET['redirect']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — Tatvam Publication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(160deg, var(--primary) 0%, var(--secondary) 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .auth-card {
            max-width: 480px;
            width: 100%;
            margin: 48px auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 16px 48px rgba(0,0,0,0.22);
            overflow: hidden;
        }
        .auth-header {
            background: var(--primary);
            padding: 32px 32px 24px;
            text-align: center;
        }
        .auth-header h2 {
            color: #fff;
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.7rem;
            margin-bottom: 4px;
        }
        .auth-header p {
            color: rgba(255,255,255,0.7);
            margin: 0;
            font-size: 14px;
        }
        .auth-body {
            padding: 32px;
        }
        .brand-link img {
            height: 36px;
            filter: brightness(0) invert(1);
            display: block;
            margin: 0 auto 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-card">
            <div class="auth-header">
                <?php
                $logo_path = __DIR__ . '/img/logo-tatvam.png';
                if (file_exists($logo_path)): ?>
                    <div class="brand-link">
                        <img src="img/logo-tatvam.png" alt="Tatvam Publication">
                    </div>
                <?php endif; ?>
                <h2>Create Account</h2>
                <p>Join the Tatvam Publication community</p>
            </div>

            <div class="auth-body">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($_GET['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="php/register.php<?= $redirect_param ? '?redirect=' . $redirect_param : '' ?>">
                    <div class="mb-3">
                        <label class="form-label" for="fullname-field">Full Name</label>
                        <input type="text" class="form-control" id="fullname-field" name="full_name" required
                               placeholder="Your full name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="reg-email-field">Email Address</label>
                        <input type="email" class="form-control" id="reg-email-field" name="email" required
                               placeholder="you@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="phone-field">Phone Number</label>
                        <input type="tel" class="form-control" id="phone-field" name="phone" required
                               placeholder="+91 XXXXX XXXXX">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="reg-password-field">Password</label>
                        <input type="password" class="form-control" id="reg-password-field" name="password" required
                               placeholder="Create a password">
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="confirm-password-field">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm-password-field" name="confirm_password" required
                               placeholder="Re-enter your password">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3" id="register-btn">
                        <i class="bi bi-person-plus"></i> Create Account
                    </button>
                </form>

                <div class="text-center" style="font-size:14px;">
                    <p class="mb-2">
                        Already have an account?
                        <a href="user-login.php<?= $redirect_param ? '?redirect=' . $redirect_param : '' ?>" class="fw-semibold">Sign in</a>
                    </p>
                    <a href="index.php" class="text-muted text-decoration-none" style="font-size:13px;">
                        <i class="bi bi-arrow-left"></i> Back to Homepage
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
