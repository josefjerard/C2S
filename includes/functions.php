<?php
declare(strict_types=1);

const STATUSES         = ['Active', 'Inactive', 'Transferred to Other Ministry'];
const TRAINING_FIELDS  = ['cldp_1', 'cldp_2', 'cldp_3', 'c2s_101'];
const CLDP_FIELDS      = ['cldp_1', 'cldp_2', 'cldp_3'];
const CLDP_STATUSES    = ['Unenrolled', 'Ongoing', 'Incomplete', 'Completed'];
const C2S_101_STATUSES = ['Lesson 1', 'Lesson 2', 'Lesson 3', 'Lesson 4', 'Lesson 5', 'Completed'];
const POTENTIAL_MENTOR_OPTIONS = ['Yes', 'No'];
const TOTAL_MODULES    = 4;
const LESSONS_PER_MODULE = 6;

function training_statuses(string $field): array
{
    return match ($field) {
        'cldp_1', 'cldp_2', 'cldp_3' => CLDP_STATUSES,
        'c2s_101'                    => C2S_101_STATUSES,
        default                      => [],
    };
}

function module_lesson_options(): array
{
    $grouped = [];
    foreach (range(1, TOTAL_MODULES) as $module) {
        $label = "Module {$module}";
        $grouped[$label] = array_map(
            fn(int $lesson): string => "{$label} - Lesson {$lesson}",
            range(1, LESSONS_PER_MODULE)
        );
    }
    return $grouped;
}

function module_lesson_values(): array
{
    $values = [];
    foreach (module_lesson_options() as $lessons) {
        foreach ($lessons as $lesson) {
            $values[] = $lesson;
        }
    }
    return $values;
}

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function is_logged_in(): bool
{
    return !empty($_SESSION['logged_in']);
}

function current_user_id(): int
{
    return (int)($_SESSION['user_id'] ?? 0);
}

function is_admin(): bool
{
    return ($_SESSION['username'] ?? '') === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['username'] = $user['username'];
}

function find_user_by_username(PDO $db_accounts, string $username): ?array
{
    $stmt = $db_accounts->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute([':username' => $username]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function create_user(PDO $db_accounts, string $username, string $password): int
{
    $stmt = $db_accounts->prepare('INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)');
    $stmt->execute([
        ':username'      => $username,
        ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
    ]);
    return (int)$db_accounts->lastInsertId();
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function render_flash(): void
{
    if (empty($_SESSION['flash'])) {
        return;
    }
    foreach ($_SESSION['flash'] as $flash) {
        $class = $flash['type'] === 'error' ? 'alert alert-error' : 'alert alert-success';
        echo '<div class="' . $class . '">' . e($flash['message']) . '</div>';
    }
    unset($_SESSION['flash']);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verify_csrf(): void
{
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', (string)$_POST['csrf'])) {
        http_response_code(403);
        exit('Invalid request.');
    }
}

function training_label(string $field): string
{
    $map = [
        'cldp_1'         => 'CLDP 1',
        'cldp_2'         => 'CLDP 2',
        'cldp_3'         => 'CLDP 3',
        'c2s_101'        => 'C2S 101',
    ];
    return $map[$field] ?? $field;
}

function badge_class(string $value): string
{
    if (preg_match('/^Lesson \d+$/', $value) === 1) {
        return 'badge-gray';
    }

    $map = [
        'Active'      => 'badge-green',
        'Completed'   => 'badge-blue',
        'Transferred to Other Ministry' => 'badge-amber',
        'On Hold'     => 'badge-amber',
        'Ongoing'     => 'badge-amber',
        'Incomplete'  => 'badge-red',
        'Inactive'    => 'badge-gray',
        'Unenrolled'  => 'badge-gray',
        'Not Started' => 'badge-gray',
    ];
    return $map[$value] ?? 'badge-gray';
}

function format_birthday(?string $birthday): string
{
    if (!$birthday) {
        return '';
    }
    return date('M j, Y', strtotime($birthday));
}

function compute_age(?string $birthday): ?int
{
    if (!$birthday) {
        return null;
    }
    try {
        $birth = new DateTime($birthday);
    } catch (Exception $e) {
        return null;
    }
    return (int)$birth->diff(new DateTime('today'))->y;
}

function collect_mentee_input(array $post): array
{
    $errors = [];

    $name = trim($post['mentee_name'] ?? '');
    if ($name === '') {
        $errors[] = 'Mentee name is required.';
    } elseif (mb_strlen($name) > 150) {
        $errors[] = 'Mentee name must not exceed 150 characters.';
    }

    $status = $post['status'] ?? 'Active';
    if (!in_array($status, STATUSES, true)) {
        $errors[] = 'Invalid status.';
    }

    $birthday = trim($post['birthday'] ?? '');
    if ($birthday !== '') {
        $date = DateTime::createFromFormat('Y-m-d', $birthday);
        if (!$date || $date->format('Y-m-d') !== $birthday) {
            $errors[] = 'Birthday must be a valid date.';
            $birthday = null;
        }
    } else {
        $birthday = null;
    }

    $contact = trim($post['contact_number'] ?? '');
    if ($contact !== '' && !preg_match('/^09\d{9}$/', $contact)) {
        $errors[] = 'Contact number must be 11 numbers starting with 09.';
    }

    $moduleLesson = trim($post['module_lesson'] ?? '');
    if ($moduleLesson !== '' && !in_array($moduleLesson, module_lesson_values(), true)) {
        $errors[] = 'Invalid module / lesson selection.';
    }

    $potentialMentor = $post['potential_mentor'] ?? 'No';
    if (!in_array($potentialMentor, POTENTIAL_MENTOR_OPTIONS, true)) {
        $errors[] = 'Invalid potential mentor selection.';
    }

    $data = [
        'mentee_name'      => $name,
        'status'           => $status,
        'contact_number'   => $contact,
        'birthday'         => $birthday,
        'address'          => trim($post['address'] ?? ''),
        'module_lesson'    => $moduleLesson,
        'potential_mentor' => $potentialMentor,
        'other_trainings'  => trim($post['other_trainings'] ?? ''),
        'remarks'          => trim($post['remarks'] ?? ''),
    ];

    foreach (TRAINING_FIELDS as $field) {
        $allowed = training_statuses($field);
        $value = $post[$field] ?? $allowed[0];
        $data[$field] = in_array($value, $allowed, true) ? $value : $allowed[0];
    }

    return [$errors, $data];
}
