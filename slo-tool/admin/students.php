<?php require_once __DIR__ . '/../backend/admin/students_handler.php'; ?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Student Registration</title><link rel="stylesheet" href="/slo-tool/assets/css/ui.css"></head>
<body><div class="wrapper">
<aside class="sidebar"><h2>Admin Panel</h2><a href="dashboard.php">Dashboard</a><a href="users.php">Manage Users</a><a href="courses.php">Programs & Courses</a><a href="mentors.php">Mentor Assignment</a><a href="students.php">Student Registration</a><a href="/slo-tool/auth/logout.php">Logout</a></aside>
<main class="content"><h1>Student Registration</h1>
<?php if (isset($_GET['ok'])): ?><div class="alert success">Student and parent profile saved.</div><?php endif; ?>
<?php if (isset($_GET['del'])): ?><div class="alert success">Student deleted.</div><?php endif; ?>
<?php if (isset($_GET['err']) && $_GET['err'] === 'delete_failed'): ?><div class="alert warn">Unable to delete this student.</div><?php endif; ?>
<div class="card"><form method="post">
<input type="hidden" name="register_student" value="1">
<label>Register Number</label><input name="reg_no" required>
<label>Student Name</label><input name="name" required>
<label>Course</label><select name="course_id"><?php foreach($courses as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['code'].' - '.$c['name']) ?></option><?php endforeach; ?></select>
<label>Mentor</label><select name="mentor_id"><option value="">Not Assigned</option><?php foreach($mentors as $m): ?><option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option><?php endforeach; ?></select>
<label>Parent Name</label><input name="parent_name">
<label>Parent Phone</label><input name="parent_phone">
<label>Parent Email</label><input type="email" name="parent_email">
<button type="submit">Register Student</button>
</form></div>
<div class="table-wrap"><table><tr><th>Reg No</th><th>Name</th><th>Course</th><th>Mentor</th><th>Action</th></tr>
<?php foreach($students as $s): ?><tr><td><?= htmlspecialchars($s['reg_no']) ?></td><td><?= htmlspecialchars($s['name']) ?></td><td><?= htmlspecialchars($s['course']) ?></td><td><?= htmlspecialchars($s['mentor'] ?? 'Not Assigned') ?></td><td><form method="post" onsubmit="return confirm('Delete this student and related marks/feedback/messages?');"><input type="hidden" name="delete_student" value="1"><input type="hidden" name="student_id" value="<?= (int)$s['id'] ?>"><button type="submit">Delete</button></form></td></tr><?php endforeach; ?>
</table></div>
</main></div><script src="/slo-tool/assets/js/dashboard.js"></script></body></html>
