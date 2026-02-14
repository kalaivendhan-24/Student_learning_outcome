<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/obe.php';
require_role(['faculty']);

$coReport = $pdo->query('SELECT co.code, ROUND(AVG(ar.co_attainment),2) AS avg_att FROM attainment_results ar JOIN course_outcomes co ON ar.co_id=co.id GROUP BY ar.co_id ORDER BY co.code')->fetchAll();
$poReport = $pdo->query('SELECT po.code, po.outcome_type, ROUND(AVG(ar.po_attainment),2) AS avg_att FROM attainment_results ar JOIN program_outcomes po ON ar.po_id=po.id GROUP BY ar.po_id ORDER BY po.code')->fetchAll();
$studentProgress = $pdo->query('SELECT s.reg_no, s.name, ROUND(AVG(m.total_mark),2) avg_mark FROM marks m JOIN students s ON m.student_id=s.id GROUP BY s.id ORDER BY avg_mark DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Reports</title><link rel="stylesheet" href="/slo-tool/assets/css/ui.css"></head>
<body><div class="wrapper"><aside class="sidebar"><h2>Faculty</h2><a href="dashboard.php">Dashboard</a><a href="co_mapping.php">CO-PO-PSO Mapping</a><a href="marks.php">Marks Entry</a><a href="reports.php">Reports</a><a href="send_alert.php">Send Alert</a><a href="/slo-tool/auth/logout.php">Logout</a></aside>
<main class="content"><h1>Attainment Reports</h1>
<div class="card" id="topic-satisfaction-card">
<h3>Real-Time Topic Satisfaction Check</h3>
<p><strong>Problem Statements with Solution:</strong> <span id="topic-solution-rate">0%</span></p>
<p><strong>Problem Statements Marked "Met":</strong> <span id="topic-met-rate">0%</span></p>
<p><strong>Last Updated:</strong> <span id="topic-updated-at">-</span></p>
</div>
<div class="card-grid">
<div class="card"><h3>CO Attainment Report</h3><div class="table-wrap"><table><tr><th>CO</th><th>Average</th></tr><?php foreach($coReport as $r): ?><tr><td><?= htmlspecialchars($r['code']) ?></td><td><?= $r['avg_att'] ?></td></tr><?php endforeach; ?></table></div></div>
<div class="card"><h3>PO / PSO Attainment Report</h3><div class="table-wrap"><table><tr><th>PO/PSO</th><th>Type</th><th>Average</th></tr><?php foreach($poReport as $r): ?><tr><td><?= htmlspecialchars($r['code']) ?></td><td><?= htmlspecialchars($r['outcome_type']) ?></td><td><?= $r['avg_att'] ?></td></tr><?php endforeach; ?></table></div></div>
</div>
<div class="card"><h3>Student Progress Report</h3><div class="table-wrap"><table><tr><th>Reg No</th><th>Name</th><th>Avg %</th><th>Risk</th></tr>
<?php foreach($studentProgress as $r): $risk=risk_label((float)$r['avg_mark']); ?>
<tr><td><?= htmlspecialchars($r['reg_no']) ?></td><td><?= htmlspecialchars($r['name']) ?></td><td><?= $r['avg_mark'] ?></td><td><span class="badge <?= $risk ?>"><?= $risk ?></span></td></tr>
<?php endforeach; ?></table></div></div>
</main></div><script src="/slo-tool/assets/js/dashboard.js"></script>
<script>
function refreshTopicCheck() {
  fetch('/slo-tool/mentor/realtime_status.php', { cache: 'no-store' })
    .then(function (res) { return res.json(); })
    .then(function (data) {
      if (!data || !data.ok) return;
      document.getElementById('topic-solution-rate').textContent = Number(data.solution_coverage).toFixed(2) + '%';
      document.getElementById('topic-met-rate').textContent = Number(data.satisfaction_met_rate).toFixed(2) + '%';
      document.getElementById('topic-updated-at').textContent = data.updated_at;
    })
    .catch(function () {});
}
refreshTopicCheck();
setInterval(refreshTopicCheck, 5000);
</script>
</body></html>
