<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_role(['mentor']);

$mentorId = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = (int) $_POST['student_id'];
    $problemStatement = trim($_POST['problem_statement'] ?? '');
    $solutionProvided = isset($_POST['solution_provided']) ? 1 : 0;
    $satisfactionStatus = trim($_POST['satisfaction_status'] ?? 'Not Met');
    $remark = trim($_POST['remark']);
    $actionPlan = trim($_POST['action_plan']);
    $validStatuses = ['Not Met', 'Partially Met', 'Met'];
    if (!in_array($satisfactionStatus, $validStatuses, true)) {
        $satisfactionStatus = 'Not Met';
    }
    $stmt = $pdo->prepare('INSERT INTO mentor_feedback (mentor_id, student_id, problem_statement, solution_provided, satisfaction_status, remark, action_plan) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$mentorId, $studentId, $problemStatement, $solutionProvided, $satisfactionStatus, $remark, $actionPlan]);
    header('Location: feedback.php?ok=1');
    exit;
}

$studentsStmt = $pdo->prepare('SELECT id, reg_no, name FROM students WHERE mentor_id=?');
$studentsStmt->execute([$mentorId]);
$students = $studentsStmt->fetchAll();
$feedStmt = $pdo->prepare('SELECT mf.created_at, s.reg_no, s.name, mf.problem_statement, mf.solution_provided, mf.satisfaction_status, mf.remark, mf.action_plan FROM mentor_feedback mf JOIN students s ON mf.student_id=s.id WHERE mf.mentor_id=? ORDER BY mf.id DESC');
$feedStmt->execute([$mentorId]);
$feedbacks = $feedStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Mentor Feedback</title><link rel="stylesheet" href="/slo-tool/assets/css/ui.css"></head>
<body><div class="wrapper"><aside class="sidebar"><h2>Mentor</h2><a href="dashboard.php">Dashboard</a><a href="feedback.php">Feedback & Action Plan</a><a href="messages.php">Parent Messages</a><a href="/slo-tool/auth/logout.php">Logout</a></aside>
<main class="content"><h1>Feedback & Action Plan</h1>
<?php if (isset($_GET['ok'])): ?><div class="alert success">Feedback saved.</div><?php endif; ?>
<div class="card"><form method="post">
<label>Student</label><select name="student_id"><?php foreach($students as $s): ?><option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['reg_no'].' - '.$s['name']) ?></option><?php endforeach; ?></select>
<label>Problem Statement</label><textarea name="problem_statement" required></textarea>
<label><input type="checkbox" name="solution_provided" value="1"> Solution Provided</label>
<label>Satisfaction Status</label>
<select name="satisfaction_status">
    <option value="Not Met">Not Met</option>
    <option value="Partially Met">Partially Met</option>
    <option value="Met">Met</option>
</select>
<label>Mentor Remark</label><textarea name="remark" required></textarea>
<label>Action Plan</label><textarea name="action_plan" required></textarea>
<button type="submit">Save Feedback</button>
</form></div>
<div class="card" id="realtime-summary">
<h3>Real-Time Topic Satisfaction</h3>
<p><strong>Solution Coverage:</strong> <span id="rt-solution-rate">0%</span></p>
<p><strong>Satisfaction Met:</strong> <span id="rt-met-rate">0%</span></p>
<p><strong>Last Updated:</strong> <span id="rt-updated-at">-</span></p>
</div>
<div class="table-wrap"><table><tr><th>Date</th><th>Student</th><th>Problem Statement</th><th>Solution?</th><th>Satisfaction</th><th>Remark</th><th>Action Plan</th></tr>
<?php foreach($feedbacks as $f): ?><tr><td><?= $f['created_at'] ?></td><td><?= htmlspecialchars($f['reg_no'].' - '.$f['name']) ?></td><td><?= htmlspecialchars($f['problem_statement'] ?? '') ?></td><td><?= (int)$f['solution_provided'] === 1 ? 'Yes' : 'No' ?></td><td><?= htmlspecialchars($f['satisfaction_status'] ?? 'Not Met') ?></td><td><?= htmlspecialchars($f['remark']) ?></td><td><?= htmlspecialchars($f['action_plan']) ?></td></tr><?php endforeach; ?>
</table></div>
</main></div><script src="/slo-tool/assets/js/dashboard.js"></script>
<script>
function updateRealtimeSummary() {
  fetch('/slo-tool/mentor/realtime_status.php', { cache: 'no-store' })
    .then(function (res) { return res.json(); })
    .then(function (data) {
      if (!data || !data.ok) return;
      document.getElementById('rt-solution-rate').textContent = Number(data.solution_coverage).toFixed(2) + '%';
      document.getElementById('rt-met-rate').textContent = Number(data.satisfaction_met_rate).toFixed(2) + '%';
      document.getElementById('rt-updated-at').textContent = data.updated_at;
    })
    .catch(function () {});
}

updateRealtimeSummary();
setInterval(updateRealtimeSummary, 5000);
</script>
</body></html>
