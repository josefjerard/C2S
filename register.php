<?php
require_once __DIR__ . '/config.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');

    if (!preg_match('/^[A-Za-z0-9_.]{3,30}$/', $username)) {
        $errors[] = 'Username must be 3-30 characters (letters, numbers, dot, underscore).';
    } elseif (find_user_by_username($db_accounts, $username)) {
        $errors[] = 'That username is already taken.';
    }

    if (strlen($password) < 5) {
        $errors[] = 'Password must be at least 5 characters.';
    }

    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $userId = create_user($db_accounts, $username, $password);
        login_user(['id' => $userId, 'username' => $username]);
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
    <title>Register · C2S</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-body register-body">
<div class="login-wrapper register-wrapper">
    <form method="post" class="card login-card register-card">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <h1 class="login-title">C2S <span>Mentee Management System</span></h1>
        <p class="login-sub">Create a new account</p>
        <?php if ($errors): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?= e($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?= e($username) ?>" placeholder="Enter Username" autofocus required maxlength="30">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter Password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
        </div>
        <button type="submit" class="btn btn-primary login-btn">Register</button>
        <p class="login-note">A new account starts with a completely blank mentee management system.</p>
        <a href="login.php" class="login-alt">Back to Log In</a>
    </form>
</div>
</body>
</html>
