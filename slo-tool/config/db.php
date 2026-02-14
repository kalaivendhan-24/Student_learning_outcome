<?php
$host = 'localhost';
$db   = 'slo_tool';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    exit('Database connection failed: ' . $e->getMessage());
}

function table_exists(PDO $pdo, string $tableName): bool {
    $stmt = $pdo->prepare('SHOW TABLES LIKE ?');
    $stmt->execute([$tableName]);
    return (bool) $stmt->fetchColumn();
}

function ensure_mentor_feedback_extensions(PDO $pdo): void {
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;
    if (!table_exists($pdo, 'mentor_feedback')) {
        return;
    }

    $columnStmt = $pdo->query('SHOW COLUMNS FROM mentor_feedback');
    $columns = [];
    foreach ($columnStmt->fetchAll() as $col) {
        $columns[$col['Field']] = true;
    }

    if (!isset($columns['problem_statement'])) {
        $pdo->exec('ALTER TABLE mentor_feedback ADD COLUMN problem_statement TEXT NULL AFTER student_id');
    }
    if (!isset($columns['solution_provided'])) {
        $pdo->exec('ALTER TABLE mentor_feedback ADD COLUMN solution_provided TINYINT(1) NOT NULL DEFAULT 0 AFTER problem_statement');
    }
    if (!isset($columns['satisfaction_status'])) {
        $pdo->exec("ALTER TABLE mentor_feedback ADD COLUMN satisfaction_status ENUM('Not Met','Partially Met','Met') NOT NULL DEFAULT 'Not Met' AFTER solution_provided");
    }
}

function ensure_attainment_mark_link(PDO $pdo): void {
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;
    if (!table_exists($pdo, 'attainment_results')) {
        return;
    }

    $columnStmt = $pdo->query('SHOW COLUMNS FROM attainment_results');
    $columns = [];
    foreach ($columnStmt->fetchAll() as $col) {
        $columns[$col['Field']] = true;
    }
    if (!isset($columns['mark_id'])) {
        $pdo->exec('ALTER TABLE attainment_results ADD COLUMN mark_id INT NULL AFTER id');
        $pdo->exec('ALTER TABLE attainment_results ADD INDEX idx_attainment_mark_id (mark_id)');
    }

    $fkStmt = $pdo->prepare("
        SELECT CONSTRAINT_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'attainment_results'
          AND COLUMN_NAME = 'mark_id'
          AND REFERENCED_TABLE_NAME = 'marks'
        LIMIT 1
    ");
    $fkStmt->execute();
    $fkName = $fkStmt->fetchColumn();
    if (!$fkName) {
        $pdo->exec('ALTER TABLE attainment_results ADD CONSTRAINT fk_attainment_mark FOREIGN KEY (mark_id) REFERENCES marks(id) ON DELETE CASCADE');
    }
}

ensure_mentor_feedback_extensions($pdo);
ensure_attainment_mark_link($pdo);
?>
