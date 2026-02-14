<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_role(['mentor', 'faculty', 'admin']);

header('Content-Type: application/json');

$mentorId = $_SESSION['user']['id'] ?? 0;
$role = $_SESSION['user']['role'] ?? '';

if ($role === 'mentor') {
    $stmt = $pdo->prepare('SELECT COUNT(*) AS total, SUM(CASE WHEN solution_provided = 1 THEN 1 ELSE 0 END) AS solved, SUM(CASE WHEN satisfaction_status = "Met" THEN 1 ELSE 0 END) AS met FROM mentor_feedback WHERE mentor_id = ?');
    $stmt->execute([(int)$mentorId]);
    $row = $stmt->fetch();
} else {
    $stmt = $pdo->query('SELECT COUNT(*) AS total, SUM(CASE WHEN solution_provided = 1 THEN 1 ELSE 0 END) AS solved, SUM(CASE WHEN satisfaction_status = "Met" THEN 1 ELSE 0 END) AS met FROM mentor_feedback');
    $row = $stmt->fetch();
}

$total = (int)($row['total'] ?? 0);
$solved = (int)($row['solved'] ?? 0);
$met = (int)($row['met'] ?? 0);

$solutionCoverage = $total > 0 ? round(($solved / $total) * 100, 2) : 0;
$satisfactionMetRate = $total > 0 ? round(($met / $total) * 100, 2) : 0;

echo json_encode([
    'ok' => true,
    'total_entries' => $total,
    'solutions_provided' => $solved,
    'satisfaction_met' => $met,
    'solution_coverage' => $solutionCoverage,
    'satisfaction_met_rate' => $satisfactionMetRate,
    'updated_at' => date('Y-m-d H:i:s')
]);
?>
