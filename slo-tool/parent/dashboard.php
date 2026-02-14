<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/obe.php';
require_role(['parent']);

$parentUserId = $_SESSION['user']['id'];
$pstmt = $pdo->prepare('SELECT p.id, p.student_id, p.name as parent_name, s.reg_no, s.name as student_name FROM parents p JOIN users u ON p.email=u.email JOIN students s ON p.student_id=s.id WHERE u.id=? LIMIT 1');
$pstmt->execute([$parentUserId]);
$profile = $pstmt->fetch();
if (!$profile) { exit('Parent profile not linked to a student.'); }

$marksStmt = $pdo->prepare('SELECT c.code, co.code as co_code, m.total_mark FROM marks m JOIN courses c ON m.course_id=c.id JOIN course_outcomes co ON m.co_id=co.id WHERE m.student_id=? ORDER BY m.id DESC');
$marksStmt->execute([(int)$profile['student_id']]);
$marks = $marksStmt->fetchAll();
$avgStmt = $pdo->prepare('SELECT ROUND(AVG(total_mark),2) as avg_mark FROM marks WHERE student_id=?');
$avgStmt->execute([(int)$profile['student_id']]);
$avg = (float) ($avgStmt->fetch()['avg_mark'] ?? 0);
$risk = risk_label($avg);

$msgStmt = $pdo->prepare('SELECT m.sent_at, m.subject, m.body, u.name as sender FROM messages m JOIN users u ON m.sender_id=u.id WHERE m.parent_id=? ORDER BY m.id DESC');
$msgStmt->execute([(int)$profile['id']]);
$messages = $msgStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Parent Dashboard</title><link rel="stylesheet" href="/slo-tool/assets/css/ui.css"></head>
<body><div class="wrapper"><aside class="sidebar"><h2>Parent</h2><a href="dashboard.php">Dashboard</a><a href="/slo-tool/auth/logout.php">Logout</a></aside>
<main class="content"><h1>Student Performance Overview</h1>
<div class="card-grid">
<div class="card"><h3>Student</h3><p><?= htmlspecialchars($profile['student_name']) ?> (<?= htmlspecialchars($profile['reg_no']) ?>)</p></div>
<div class="card"><h3>Average Score</h3><div class="kpi"><?= number_format($avg,2) ?>%</div></div>
<div class="card"><h3>Risk Level</h3><span class="badge <?= $risk ?>"><?= $risk ?></span></div>
<div class="card"><h3>Improvement Suggestion</h3><p><?= htmlspecialchars(predictive_alert($avg)) ?></p></div>
</div>
<div class="card"><h3>Marks</h3><div class="table-wrap"><table><tr><th>Course</th><th>CO</th><th>Total %</th></tr>
<?php foreach($marks as $m): ?><tr><td><?= htmlspecialchars($m['code']) ?></td><td><?= htmlspecialchars($m['co_code']) ?></td><td><?= $m['total_mark'] ?></td></tr><?php endforeach; ?></table></div></div>
<div class="card"><h3>Messages & Alerts</h3><div class="table-wrap"><table><tr><th>Date</th><th>From</th><th>Subject</th><th>Message</th></tr>
<?php foreach($messages as $msg): ?><tr><td><?= $msg['sent_at'] ?></td><td><?= htmlspecialchars($msg['sender']) ?></td><td><?= htmlspecialchars($msg['subject']) ?></td><td><?= htmlspecialchars($msg['body']) ?></td></tr><?php endforeach; ?></table></div></div>
</main></div><script src="/slo-tool/assets/js/dashboard.js"></script></body></html>
