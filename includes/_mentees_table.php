<div class="table-wrap card">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Status</th>
                <th>Module / Lesson</th>
                <th>Potential Mentor</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$mentees): ?>
                <tr>
                    <td colspan="5" class="empty">No mentees found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($mentees as $m): ?>
                    <tr>
                        <td data-label="Name"><a class="row-link" href="view.php?id=<?= (int)$m['id'] ?>"><?= e($m['mentee_name']) ?></a></td>
                        <td data-label="Status"><span class="badge <?= badge_class($m['status']) ?>"><?= e($m['status']) ?></span></td>
                        <td data-label="Module / Lesson"><?= e($m['module_lesson'] !== '' ? $m['module_lesson'] : 'Not yet started') ?></td>
                        <td data-label="Potential Mentor"><?= e($m['potential_mentor']) ?></td>
                        <td data-label="Remarks" class="cell-truncate" title="<?= e($m['remarks']) ?>"><?= e($m['remarks']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
