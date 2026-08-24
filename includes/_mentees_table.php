<div class="table-wrap card">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Status</th>
                <th>Age</th>
                <th>Birthday</th>
                <th>Module / Lesson</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$mentees): ?>
                <tr>
                    <td colspan="6" class="empty">No mentees found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($mentees as $m): ?>
                    <tr>
                        <td><a class="row-link" href="view.php?id=<?= (int)$m['id'] ?>"><?= e($m['mentee_name']) ?></a></td>
                        <td><span class="badge <?= badge_class($m['status']) ?>"><?= e($m['status']) ?></span></td>
                        <td><?= e((string)(compute_age($m['birthday']) ?? '')) ?></td>
                        <td><?= e(format_birthday($m['birthday'])) ?></td>
                        <td><?= e($m['module_lesson'] !== '' ? $m['module_lesson'] : 'Not yet started') ?></td>
                        <td class="cell-truncate" title="<?= e($m['remarks']) ?>"><?= e($m['remarks']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
