<?php if (!$mentorMentees): ?>
    <div class="card empty-note">No mentees found.</div>
<?php else: ?>
    <div class="card">
        <div class="table-wrap">
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
                    <?php foreach ($mentorMentees as $m): ?>
                        <tr>
                            <td><a href="view.php?id=<?= (int)$m['id'] ?>"><?= e($m['mentee_name']) ?></a></td>
                            <td><span class="badge <?= badge_class($m['status']) ?>"><?= e($m['status']) ?></span></td>
                            <td><?= e($m['module_lesson'] !== '' ? $m['module_lesson'] : 'Not yet started') ?></td>
                            <td><?= e($m['potential_mentor']) ?></td>
                            <td class="cell-truncate" title="<?= e($m['remarks']) ?>"><?= e($m['remarks']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
