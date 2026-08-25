<?php
require_once __DIR__ . '/config.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    $user = find_admin_by_username($db_accounts, $username);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        $user = find_mentor_by_username($username);
    }

    if (!$user || !password_verify($password, $user['password_hash'])) {
        $error = 'Invalid username or password.';
    } else {
        login_user($user);
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log In · C2S</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-body">
<div class="login-wrapper">
    <form method="post" class="card login-card">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <h1 class="login-title">C2S <span>Mentee Management System</span></h1>
        <p class="login-sub">Sign in to continue</p>
        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" autofocus required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary login-btn">Log In</button>
        <a href="register.php" class="login-alt">Create a new account</a>
    </form>
</div>
</body>
</html>
