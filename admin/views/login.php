<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Car Workshop</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="login-wrap">
        <div class="login-box">
            <h2>🔐 Admin Login</h2>
            <?php if ($loginError): ?>
                <div class="msg error"><?php echo htmlspecialchars($loginError); ?></div>
            <?php endif; ?>
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
