<?php
require_once __DIR__ . '/config.php';
require_login();

if (is_admin()) {
    $totalMentees = (int)$db_mentees->query('SELECT COUNT(*) FROM mentees')->fetchColumn();

    $mentorStmt = $db_accounts->prepare('SELECT id, username FROM users WHERE username <> :admin ORDER BY username ASC');
    $mentorStmt->execute([':admin' => 'admin']);
    $mentors = $mentorStmt->fetchAll();

    $menteesByMentor = [];
    foreach ($db_mentees->query('SELECT * FROM mentees ORDER BY mentee_name ASC') as $row) {
        $menteesByMentor[(int)$row['user_id']][] = $row;
    }

    $page_title = 'Dashboard';
    require __DIR__ . '/includes/header.php';
    ?>

    <div class="stat-grid">
        <div class="card stat">
            <span class="stat-value"><?= $totalMentees ?></span>
            <span class="stat-label">Total Mentees</span>
        </div>
        <div class="card stat">
            <span class="stat-value text-blue"><?= count($mentors) ?></span>
            <span class="stat-label">Total Mentors</span>
        </div>
    </div>

    <?php if (!$mentors): ?>
        <div class="card empty-note">No mentors registered yet.</div>
    <?php else: ?>
        <div class="mentor-list">
            <?php foreach ($mentors as $mentor): ?>
                <?php $mentorMentees = $menteesByMentor[(int)$mentor['id']] ?? []; ?>
                <div class="card mentor-card">
                    <div class="mentor-head">
                        <h2 class="mentor-name"><?= e($mentor['username']) ?></h2>
                        <span class="badge badge-blue"><?= count($mentorMentees) ?> <?= count($mentorMentees) === 1 ? 'mentee' : 'mentees' ?></span>
                    </div>
                    <?php if (!$mentorMentees): ?>
                        <p class="empty-note">No mentees yet.</p>
                    <?php else: ?>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Module / Lesson</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($mentorMentees as $m): ?>
                                        <tr>
                                            <td><?= e($m['mentee_name']) ?></td>
                                            <td><span class="badge <?= badge_class($m['status']) ?>"><?= e($m['status']) ?></span></td>
                                            <td><?= e($m['module_lesson'] !== '' ? $m['module_lesson'] : 'Not yet started') ?></td>
                                            <td class="cell-truncate" title="<?= e($m['remarks']) ?>"><?= e($m['remarks']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php require __DIR__ . '/includes/footer.php';
    exit;
}

$search = trim($_GET['q'] ?? '');
$statusFilter = $_GET['status'] ?? '';

$sql = 'SELECT * FROM mentees';
$where = ['user_id = :uid'];
$params = [':uid' => current_user_id()];

if ($search !== '') {
    $where[] = '(mentee_name LIKE :q1 OR potential_mentor LIKE :q2 OR module_lesson LIKE :q3 OR contact_number LIKE :q4)';
    $params[':q1'] = '%' . $search . '%';
    $params[':q2'] = '%' . $search . '%';
    $params[':q3'] = '%' . $search . '%';
    $params[':q4'] = '%' . $search . '%';
}
if ($statusFilter !== '' && in_array($statusFilter, STATUSES, true)) {
    $where[] = 'status = :status';
    $params[':status'] = $statusFilter;
}
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY mentee_name ASC';

$stmt = $db_mentees->prepare($sql);
$stmt->execute($params);
$mentees = $stmt->fetchAll();

if (isset($_GET['ajax'])) {
    require __DIR__ . '/includes/_mentees_table.php';
    exit;
}

$statsStmt = $db_mentees->prepare(
    "SELECT COUNT(*) AS total,
            COALESCE(SUM(status = 'Active'), 0) AS active,
            COALESCE(SUM(status = 'Inactive'), 0) AS inactive,
            COALESCE(SUM(status = 'Transferred to Other Ministry'), 0) AS transferred
     FROM mentees
     WHERE user_id = :uid"
);
$statsStmt->execute([':uid' => current_user_id()]);
$stats = $statsStmt->fetch();

$page_title = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>

<div class="stat-grid">
    <div class="card stat">
        <span class="stat-value"><?= (int)$stats['total'] ?></span>
        <span class="stat-label">Total Mentees</span>
    </div>
    <div class="card stat">
        <span class="stat-value text-green"><?= (int)$stats['active'] ?></span>
        <span class="stat-label">Active</span>
    </div>
    <div class="card stat">
        <span class="stat-value text-gray"><?= (int)$stats['inactive'] ?></span>
        <span class="stat-label">Inactive</span>
    </div>
    <div class="card stat">
        <span class="stat-value text-amber"><?= (int)$stats['transferred'] ?></span>
        <span class="stat-label">Transferred</span>
    </div>
</div>

<form method="get" class="filter-bar card" onsubmit="return false;">
    <input type="text" name="q" id="search_q" placeholder="Search name, mentor, module..." value="<?= e($search) ?>" autocomplete="off">
    <select name="status" id="filter_status">
        <option value="">All Statuses</option>
        <?php foreach (STATUSES as $option): ?>
            <option value="<?= e($option) ?>" <?= $statusFilter === $option ? 'selected' : '' ?>><?= e($option) ?></option>
        <?php endforeach; ?>
    </select>
</form>

<div id="table-container">
<?php require __DIR__ . '/includes/_mentees_table.php'; ?>
</div>

<script>
var searchInput = document.getElementById('search_q');
var statusSelect = document.getElementById('filter_status');
var tableContainer = document.getElementById('table-container');
var debounceTimer = null;

function currentUrl(includeAjax) {
    var params = new URLSearchParams();
    if (searchInput.value.trim() !== '') {
        params.set('q', searchInput.value.trim());
    }
    if (statusSelect.value !== '') {
        params.set('status', statusSelect.value);
    }
    if (includeAjax) {
        params.set('ajax', '1');
    }
    var qs = params.toString();
    return 'index.php' + (qs ? '?' + qs : '');
}

function loadResults() {
    fetch(currentUrl(true))
        .then(function (response) {
            return response.text();
        })
        .then(function (html) {
            tableContainer.innerHTML = html;
        });
}

function syncAddressBar() {
    window.history.replaceState(null, '', currentUrl(false));
}

searchInput.addEventListener('input', function () {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function () {
        syncAddressBar();
        loadResults();
    }, 200);
});

statusSelect.addEventListener('change', function () {
    clearTimeout(debounceTimer);
    syncAddressBar();
    loadResults();
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
