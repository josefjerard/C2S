<?php
require_once __DIR__ . '/config.php';
require_login();

if (is_admin()) {
    set_flash('error', 'Admin accounts have view-only access.');
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

verify_csrf();

$id = (int)($_POST['id'] ?? 0);
$stmt = $db_mentees->prepare('DELETE FROM mentees WHERE id = :id AND user_id = :uid');
$stmt->execute([':id' => $id, ':uid' => current_user_id()]);

if ($stmt->rowCount() === 0) {
    set_flash('error', 'Mentee not found.');
} else {
    set_flash('success', 'Mentee was deleted.');
}

header('Location: index.php');
exit;
