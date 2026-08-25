<?php
require_once __DIR__ . '/config.php';
require_login();

if (is_admin()) {
    set_flash('error', 'Admin accounts have view-only access.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $db_mentees->prepare('SELECT * FROM mentees WHERE id = :id');
$stmt->execute([':id' => $id]);
$existing = $stmt->fetch();

if (!$existing) {
    set_flash('error', 'Mentee not found.');
    header('Location: index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    [$errors, $data] = collect_mentee_input($_POST);

    if (!$errors) {
        $set = implode(', ', array_map(fn($col) => "$col = :$col", array_keys($data)));
        $stmt = $db_mentees->prepare('UPDATE mentees SET ' . $set . ' WHERE id = :id AND user_id = :uid');
        $data['id'] = $id;
        $data['uid'] = current_user_id();
        $stmt->execute($data);

        set_flash('success', 'Mentee "' . $data['mentee_name'] . '" was updated successfully.');
        header('Location: index.php');
        exit;
    }

    $mentee = $_POST;
} else {
    $mentee = $existing;
}

$editing = true;
$page_title = 'Edit Mentee';
require __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <h1>Edit: <?= e($existing['mentee_name']) ?></h1>
</div>

<?php require __DIR__ . '/includes/_form_fields.php'; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
