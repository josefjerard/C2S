<?php require_once __DIR__ . '/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($page_title) ? e($page_title) . ' · C2S' : 'C2S Mentee Management' ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <div class="topbar-inner">
        <a href="index.php" class="brand">C2S <span>Mentee Management System</span></a>
        <input type="checkbox" id="nav-toggle" class="nav-toggle-input" aria-label="Toggle navigation">
        <label for="nav-toggle" class="hamburger" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </label>
        <nav class="nav">
            <span class="nav-user"><?= e($_SESSION['username'] ?? '') ?></span>
            <?php if (!is_admin()): ?>
                <a href="create.php" class="btn btn-primary btn-sm">+ Add Mentee</a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-secondary btn-sm">Log Out</a>
        </nav>
    </div>
</header>
<main class="container">
<?php render_flash(); ?>
