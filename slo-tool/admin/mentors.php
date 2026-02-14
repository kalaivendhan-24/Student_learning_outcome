<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = (int) $_POST['student_id'];
    $mentorId = (int) $_POST['mentor_id'];
    $stmt = $pdo->prepare('UPDATE students SET mentor_id = ? WHERE id = ?');
    $stmt->execute([$mentorId, $studentId]);
    header('Location: mentors.php?ok=1');
    exit;
}

$students = $pdo->query('SELECT s.id, s.reg_no, s.name, u.name AS mentor_name FROM students s LEFT JOIN users u ON s.mentor_id=u.id ORDER BY s.id DESC')->fetchAll();
$mentors = $pdo->query("SELECT id, name FROM users WHERE role='mentor'")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Mentor Assignment</title><link rel="stylesheet" href="/slo-tool/assets/css/ui.css"></head>
<body><div class="wrapper">
<aside class="sidebar"><h2>Admin Panel</h2><a href="dashboard.php">Dashboard</a><a href="users.php">Manage Users</a><a href="courses.php">Programs & Courses</a><a href="mentors.php">Mentor Assignment</a><a href="students.php">Student Registration</a><a href="/slo-tool/auth/logout.php">Logout</a></aside>
<main class="content"><h1>Assign Mentors</h1>
<?php if (isset($_GET['ok'])): ?><div class="alert success">Mentor assigned.</div><?php endif; ?>
<div class="card"><form method="post">
<label>Student</label><select name="student_id" required>
<?php foreach($students as $s): ?><option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['reg_no'].' - '.$s['name']) ?></option><?php endforeach; ?>
</select>
<label>Mentor</label><select name="mentor_id" required>
<?php foreach($mentors as $m): ?><option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option><?php endforeach; ?>
</select>
<button type="submit">Assign</button></form></div>
<div class="table-wrap"><table><thead><tr><th>Reg No</th><th>Student</th><th>Assigned Mentor</th></tr></thead><tbody>
<?php foreach($students as $s): ?><tr><td><?= htmlspecialchars($s['reg_no']) ?></td><td><?= htmlspecialchars($s['name']) ?></td><td><?= htmlspecialchars($s['mentor_name'] ?? 'Not Assigned') ?></td></tr><?php endforeach; ?>
</tbody></table></div>
</main></div><script src="/slo-tool/assets/js/dashboard.js"></script></body></html>
