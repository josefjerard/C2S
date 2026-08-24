<?php
require_once __DIR__ . '/config.php';
require_login();

$errors = [];
$mentee = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    [$errors, $data] = collect_mentee_input($_POST);

    if (!$errors) {
        $data['user_id'] = current_user_id();
        $columns = array_keys($data);
        $sql = 'INSERT INTO mentees (' . implode(', ', $columns) . ') VALUES (:' . implode(', :', $columns) . ')';
        $stmt = $db_mentees->prepare($sql);
        $stmt->execute($data);

        set_flash('success', 'Mentee "' . $data['mentee_name'] . '" was added successfully.');
        header('Location: index.php');
        exit;
    }

    $mentee = $_POST;
}

$page_title = 'Add Mentee';
require __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <h1>Add Mentee</h1>
</div>

<?php $formContext = 'create'; ?>
<?php require __DIR__ . '/includes/_form_fields.php'; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
