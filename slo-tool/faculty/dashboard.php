<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/obe.php';
require_role(['faculty']);

$stats = $pdo->query('SELECT AVG(total_mark) as avg_mark FROM marks')->fetch();
$avg = (float) ($stats['avg_mark'] ?? 0);
$risk = risk_label($avg);
$alert = predictive_alert($avg);
$totalCourses = (int) $pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn();
$totalStudents = (int) $pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$avgCoAttainment = (float) ($pdo->query('SELECT AVG(co_attainment) FROM attainment_results')->fetchColumn() ?: 0);
$avgPoAttainment = (float) ($pdo->query('SELECT AVG(po_attainment) FROM attainment_results')->fetchColumn() ?: 0);

$attainment = $pdo->query('SELECT co.code AS co_code, AVG(ar.co_attainment) AS avg_att FROM attainment_results ar JOIN course_outcomes co ON ar.co_id=co.id GROUP BY ar.co_id ORDER BY ar.co_id')->fetchAll();
$labels = array_map(fn($r) => $r['co_code'], $attainment);
$vals = array_map(fn($r) => (float)$r['avg_att'], $attainment);
$recentMarks = $pdo->query('SELECT m.id, s.reg_no, s.name, c.code AS course_code, co.code AS co_code, m.total_mark FROM marks m JOIN students s ON m.student_id=s.id JOIN courses c ON m.course_id=c.id JOIN course_outcomes co ON m.co_id=co.id ORDER BY m.id DESC LIMIT 8')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Faculty Dashboard</title><link rel="stylesheet" href="/slo-tool/assets/css/ui.css"></head>
<body><div class="wrapper">
<aside class="sidebar"><h2>Faculty</h2><a href="dashboard.php">Dashboard</a><a href="co_mapping.php">CO-PO-PSO Mapping</a><a href="marks.php">Marks Entry</a><a href="reports.php">Reports</a><a href="send_alert.php">Send Alert</a><a href="/slo-tool/auth/logout.php">Logout</a></aside>
<main class="content">
<div class="card-grid">
<div class="card stat-card" data-tooltip="Total courses currently mapped in the system"><h3>Total Courses</h3><div class="kpi"><?= $totalCourses ?></div><span class="trend-note">Curriculum footprint</span></div>
<div class="card stat-card" data-tooltip="Total students linked to this academic cycle"><h3>Students</h3><div class="kpi"><?= $totalStudents ?></div><span class="trend-note">Academic population</span></div>
<div class="card stat-card"><h3>Average CO Attainment</h3><div class="kpi"><?= number_format($avgCoAttainment,2) ?>%</div><span class="trend-note">Outcome-level performance</span></div>
<div class="card stat-card"><h3>Average PO Attainment</h3><div class="kpi"><?= number_format($avgPoAttainment,2) ?>%</div><span class="trend-note">Program-level performance</span></div>
</div>
<div class="card-grid" style="margin-top:1rem;">
<div class="card"><h3>Attainment Progress</h3>
<div class="progress-block"><div class="progress-head"><span>CO Attainment</span><strong><?= number_format($avgCoAttainment,1) ?>%</strong></div><div class="progress-track"><div class="progress-fill" data-value="<?= number_format($avgCoAttainment,2,'.','') ?>"></div></div></div>
<div class="progress-block"><div class="progress-head"><span>PO Attainment</span><strong><?= number_format($avgPoAttainment,1) ?>%</strong></div><div class="progress-track"><div class="progress-fill" data-value="<?= number_format($avgPoAttainment,2,'.','') ?>"></div></div></div>
</div>
<div class="card"><h3>Performance Snapshot</h3><div class="kpi"><?= number_format($avg,2) ?>%</div><p><span class="badge <?= $risk ?>"><?= $risk ?></span></p><p><?= htmlspecialchars($alert) ?></p></div>
</div>
<div class="chart-box">
<h3>CO Attainment Trend</h3>
<canvas id="coChart" width="800" height="320"></canvas>
</div>
<div class="card" style="margin-top:1rem;">
<h3>Quick Delete: Recent Marks</h3>
<div class="table-wrap"><table><thead><tr><th>Reg No</th><th>Name</th><th>Course</th><th>CO</th><th>Total %</th><th>Action</th></tr></thead><tbody>
<?php foreach($recentMarks as $row): ?><tr><td><?= htmlspecialchars($row['reg_no']) ?></td><td><?= htmlspecialchars($row['name']) ?></td><td><?= htmlspecialchars($row['course_code']) ?></td><td><?= htmlspecialchars($row['co_code']) ?></td><td><?= number_format((float)$row['total_mark'],2) ?></td><td><form method="post" action="marks.php" onsubmit="return confirm('Delete this marks entry and linked attainment values?');"><input type="hidden" name="delete_mark" value="1"><input type="hidden" name="mark_id" value="<?= (int)$row['id'] ?>"><button type="submit">Delete</button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
</div>
</main></div>
<script src="/slo-tool/assets/js/dashboard.js"></script>
<script>
const labels = <?= json_encode($labels) ?>;
const values = <?= json_encode($vals) ?>;
drawBarChart('coChart', labels, values, '#5e3df2');
</script>
</body></html>
