<?php
declare(strict_types=1);

session_start();

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');

define('DB_ACCOUNTS', 'c2s_accounts');
define('DB_MENTEES', 'c2s_mentees');

define('MENTORS_CSV', __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'mentors.csv');

function db_connect(string $dbName): PDO
{
    return new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . $dbName . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
}

try {
    $db_accounts = db_connect(DB_ACCOUNTS);
    $db_mentees  = db_connect(DB_MENTEES);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database connection failed: ' . $e->getMessage());
}

require_once __DIR__ . '/includes/functions.php';
