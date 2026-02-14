<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/obe.php';
require_role(['mentor']);

$mentorId = $_SESSION['user']['id'];
$stmt = $pdo->prepare('SELECT s.id, s.reg_no, s.name, ROUND(AVG(m.total_mark),2) AS avg_mark FROM students s LEFT JOIN marks m ON s.id=m.student_id WHERE s.mentor_id=? GROUP BY s.id ORDER BY s.reg_no');
$stmt->execute([$mentorId]);
$students = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Mentor Dashboard</title><link rel="stylesheet" href="/slo-tool/assets/css/ui.css"></head>
<body><div class="wrapper"><aside class="sidebar"><h2>Mentor</h2><a href="dashboard.php">Dashboard</a><a href="feedback.php">Feedback & Action Plan</a><a href="messages.php">Parent Messages</a><a href="/slo-tool/auth/logout.php">Logout</a></aside>
<main class="content"><h1>Assigned Students</h1>
<div class="table-wrap"><table><tr><th>Reg No</th><th>Name</th><th>Avg %</th><th>Risk</th></tr>
<?php foreach($students as $s): $avg=(float)($s['avg_mark'] ?? 0); $risk=risk_label($avg); ?>
<tr><td><?= htmlspecialchars($s['reg_no']) ?></td><td><?= htmlspecialchars($s['name']) ?></td><td><?= number_format($avg,2) ?></td><td><span class="badge <?= $risk ?>"><?= $risk ?></span></td></tr>
<?php endforeach; ?>
</table></div>
</main></div><script src="/slo-tool/assets/js/dashboard.js"></script></body></html>
