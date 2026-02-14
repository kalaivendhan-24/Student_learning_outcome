<?php
declare(strict_types=1);

require_once __DIR__ . '/config/db.php';

$requiredTables = [
    'users',
    'students',
    'parents',
    'courses',
    'course_outcomes',
    'program_outcomes',
    'co_po_mapping',
    'marks',
    'attainment_results',
    'mentor_feedback',
    'messages'
];

$checks = [];

// Basic runtime checks.
$checks[] = [
    'name' => 'PHP Version >= 8.0',
    'ok' => version_compare(PHP_VERSION, '8.0.0', '>='),
    'detail' => 'Current: ' . PHP_VERSION
];

$checks[] = [
    'name' => 'PDO Extension Loaded',
    'ok' => extension_loaded('pdo'),
    'detail' => extension_loaded('pdo') ? 'Available' : 'Missing'
];

$checks[] = [
    'name' => 'PDO MySQL Extension Loaded',
    'ok' => extension_loaded('pdo_mysql'),
    'detail' => extension_loaded('pdo_mysql') ? 'Available' : 'Missing'
];

$dbOk = false;
$dbDetail = 'Not tested';

try {
    $pdo->query('SELECT 1');
    $dbOk = true;
    $dbDetail = 'Connection successful';
} catch (Throwable $e) {
    $dbDetail = 'Connection failed: ' . $e->getMessage();
}

$checks[] = [
    'name' => 'Database Connection',
    'ok' => $dbOk,
    'detail' => $dbDetail
];

$tableStatus = [];
$allTablesFound = true;

if ($dbOk) {
    $stmt = $pdo->query('SHOW TABLES');
    $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $existingMap = array_fill_keys($existing, true);

    foreach ($requiredTables as $table) {
        $ok = isset($existingMap[$table]);
        if (!$ok) {
            $allTablesFound = false;
        }
        $tableStatus[] = [
            'table' => $table,
            'ok' => $ok
        ];
    }
} else {
    $allTablesFound = false;
    foreach ($requiredTables as $table) {
        $tableStatus[] = [
            'table' => $table,
            'ok' => false
        ];
    }
}

$checks[] = [
    'name' => 'Required Tables Present',
    'ok' => $allTablesFound,
    'detail' => $allTablesFound ? 'All required tables found' : 'One or more tables are missing'
];

$allOk = true;
foreach ($checks as $c) {
    if (!$c['ok']) {
        $allOk = false;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLO Tool Setup Checker</title>
    <link rel="stylesheet" href="/slo-tool/assets/css/ui.css">
</head>
<body>
<div class="wrapper">
    <aside class="sidebar">
        <h2>Setup Checker</h2>
        <a href="/slo-tool/index.php">Go to Login</a>
    </aside>
    <main class="content">
        <h1>Localhost Installation Check</h1>
        <div class="alert <?= $allOk ? 'success' : 'warn' ?>">
            <?= $allOk ? 'System is ready. You can use the application.' : 'Setup is incomplete. Review failed checks below.' ?>
        </div>

        <div class="card">
            <h3>Environment Checks</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Check</th>
                        <th>Status</th>
                        <th>Detail</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($checks as $check): ?>
                        <tr>
                            <td><?= htmlspecialchars($check['name']) ?></td>
                            <td>
                                <span class="badge <?= $check['ok'] ? 'Green' : 'Red' ?>">
                                    <?= $check['ok'] ? 'PASS' : 'FAIL' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($check['detail']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h3>Database Tables</h3>
            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Table</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tableStatus as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['table']) ?></td>
                            <td>
                                <span class="badge <?= $row['ok'] ? 'Green' : 'Red' ?>">
                                    <?= $row['ok'] ? 'FOUND' : 'MISSING' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h3>Next Steps</h3>
            <p>1. If DB checks fail, import <code>database/schema.sql</code>.</p>
            <p>2. Confirm credentials in <code>config/db.php</code>.</p>
            <p>3. Open <code>/slo-tool/index.php</code> and log in.</p>
        </div>
    </main>
</div>
<script src="/slo-tool/assets/js/dashboard.js"></script>
</body>
</html>
