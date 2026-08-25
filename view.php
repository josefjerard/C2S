<?php
require_once __DIR__ . '/config.php';
require_login();

$id = (int)($_GET['id'] ?? 0);

if (is_admin()) {
    $stmt = $db_mentees->prepare('SELECT * FROM mentees WHERE id = :id');
    $stmt->execute([':id' => $id]);
} else {
    $stmt = $db_mentees->prepare('SELECT * FROM mentees WHERE id = :id AND user_id = :uid');
    $stmt->execute([':id' => $id, ':uid' => current_user_id()]);
}
$mentee = $stmt->fetch();

if (!$mentee) {
    set_flash('error', 'Mentee not found.');
    header('Location: index.php');
    exit;
}

$backLink = is_admin() ? 'mentor.php?id=' . (int)$mentee['user_id'] : 'index.php';
$backLabel = is_admin() ? 'Back to Mentor' : 'Back to List';

$page_title = $mentee['mentee_name'];
require __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <h1><?= e($mentee['mentee_name']) ?></h1>
    <span class="badge <?= badge_class($mentee['status']) ?>"><?= e($mentee['status']) ?></span>
</div>

<div class="card detail-card">
    <h2 class="form-section">Personal Information</h2>
    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">Age</span>
            <span><?= e((string)(compute_age($mentee['birthday']) ?? '')) ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Birthday</span>
            <span><?= e(format_birthday($mentee['birthday'])) ?></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Contact Number</span>
            <span><?= e($mentee['contact_number']) ?></span>
        </div>
        <div class="detail-item detail-full">
            <span class="detail-label">Address</span>
            <span><?= e($mentee['address']) ?></span>
        </div>
    </div>

    <h2 class="form-section">Mentoring &amp; Trainings</h2>
    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">Module / Lesson</span>
            <span><?= e($mentee['module_lesson'] !== '' ? $mentee['module_lesson'] : 'Not yet started') ?></span>
        </div>
        <?php foreach (TRAINING_FIELDS as $field): ?>
            <div class="detail-item">
                <span class="detail-label"><?= e(training_label($field)) ?></span>
                <span class="badge <?= badge_class($mentee[$field]) ?>"><?= e($mentee[$field]) ?></span>
            </div>
        <?php endforeach; ?>
        <div class="detail-item">
            <span class="detail-label">Potential Mentor</span>
            <span><?= e($mentee['potential_mentor']) ?></span>
        </div>
        <div class="detail-item detail-full">
            <span class="detail-label">Other Trainings</span>
            <span><?= e($mentee['other_trainings']) ?></span>
        </div>
        <div class="detail-item detail-full">
            <span class="detail-label">Remarks</span>
            <span><?= e($mentee['remarks']) ?></span>
        </div>
    </div>
</div>

<div class="form-actions">
    <?php if (!is_admin()): ?>
        <a href="edit.php?id=<?= (int)$mentee['id'] ?>" class="btn btn-primary">Edit Mentee</a>
        <form method="post" action="delete.php" onsubmit="return confirm('Delete this mentee?');">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="id" value="<?= (int)$mentee['id'] ?>">
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    <?php else: ?>
        <a href="<?= e($backLink) ?>" class="btn btn-secondary"><?= e($backLabel) ?></a>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
