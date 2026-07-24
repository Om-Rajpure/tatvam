<?php session_start(); 
if (isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'customer') {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tatvam Publication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; }
        .login-card { max-width: 450px; margin: 50px auto; background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .login-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 20px 20px 0 0; text-align: center; }
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="login-header">
                <h2 class="mb-0"><i class="bi bi-box-arrow-in-right"></i> Welcome Back</h2>
                <p class="mb-0 mt-2">Login to your account</p>
            </div>
            <div class="p-4">
                <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle"></i> <?=htmlspecialchars($_GET['error'])?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php } ?>
                <?php if (isset($_GET['success'])) { ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i> <?=htmlspecialchars($_GET['success'])?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php } ?>
                
                <form method="POST" action="php/user-auth.php">
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-envelope"></i> Email Address</label>
                        <input type="email" class="form-control" name="email" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-lock"></i> Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                </form>
                <div class="text-center">
                    <p class="mb-2">Don't have an account? <a href="register.php" class="text-decoration-none">Register here</a></p>
                    <a href="index.php" class="text-muted text-decoration-none"><i class="bi bi-arrow-left"></i> Back to Home</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
