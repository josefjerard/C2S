<?php
require_once __DIR__ . '/config.php';
require_login();

if (!is_admin()) {
    header('Location: index.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$mentor = find_mentor_by_id($id);

if (!$mentor || $mentor['username'] === 'admin') {
    set_flash('error', 'Mentor not found.');
    header('Location: index.php');
    exit;
}

$search = trim($_GET['q'] ?? '');
$statusFilter = $_GET['status'] ?? '';

$allMentees = $db_mentees->prepare('SELECT * FROM mentees WHERE user_id = :uid ORDER BY mentee_name ASC');
$allMentees->execute([':uid' => (int)$mentor['id']]);
$allMentees = $allMentees->fetchAll();

$totalMentees = count($allMentees);

$mentorMentees = array_values(array_filter(
    $allMentees,
    function (array $m) use ($search, $statusFilter): bool {
        if ($statusFilter !== '' && !in_array($statusFilter, STATUSES, true)) {
            return false;
        }
        if ($statusFilter !== '' && $m['status'] !== $statusFilter) {
            return false;
        }
        if ($search === '') {
            return true;
        }
        $haystack = mb_strtolower($m['mentee_name'] . ' ' . $m['potential_mentor'] . ' ' . $m['module_lesson'] . ' ' . $m['status']);
        return str_contains($haystack, mb_strtolower($search));
    }
));

if (isset($_GET['ajax'])) {
    require __DIR__ . '/includes/_mentor_mentees_table.php';
    exit;
}

$page_title = $mentor['username'];
require __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <h1><?= e($mentor['username']) ?></h1>
    <span class="badge badge-blue"><?= $totalMentees ?> <?= $totalMentees === 1 ? 'mentee' : 'mentees' ?></span>
</div>

<form method="get" class="filter-bar card" onsubmit="return false;">
    <input type="hidden" name="id" value="<?= (int)$mentor['id'] ?>">
    <input type="text" name="q" id="search_q" placeholder="Search name, mentor, module..." value="<?= e($search) ?>" autocomplete="off">
    <select name="status" id="filter_status">
        <option value="">All Statuses</option>
        <?php foreach (STATUSES as $option): ?>
            <option value="<?= e($option) ?>" <?= $statusFilter === $option ? 'selected' : '' ?>><?= e($option) ?></option>
        <?php endforeach; ?>
    </select>
</form>

<div id="table-container">
<?php require __DIR__ . '/includes/_mentor_mentees_table.php'; ?>
</div>

<script>
var searchInput = document.getElementById('search_q');
var statusSelect = document.getElementById('filter_status');
var tableContainer = document.getElementById('table-container');
var debounceTimer = null;

function currentUrl(includeAjax) {
    var params = new URLSearchParams();
    params.set('id', '<?= (int)$mentor['id'] ?>');
    if (searchInput.value.trim() !== '') {
        params.set('q', searchInput.value.trim());
    }
    if (statusSelect.value !== '') {
        params.set('status', statusSelect.value);
    }
    if (includeAjax) {
        params.set('ajax', '1');
    }
    return 'mentor.php?' + params.toString();
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
