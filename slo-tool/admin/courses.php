<?php require_once __DIR__ . '/../backend/admin/courses_handler.php'; ?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Programs & Courses</title><link rel="stylesheet" href="/slo-tool/assets/css/ui.css"></head>
<body><div class="wrapper">
<aside class="sidebar"><h2>Admin Panel</h2><a href="dashboard.php">Dashboard</a><a href="users.php">Manage Users</a><a href="courses.php">Programs & Courses</a><a href="mentors.php">Mentor Assignment</a><a href="students.php">Student Registration</a><a href="/slo-tool/auth/logout.php">Logout</a></aside>
<main class="content"><h1>Programs & Courses</h1>
<?php if (isset($_GET['ok'])): ?><div class="alert success">Course added.</div><?php endif; ?>
<?php if (isset($_GET['del'])): ?><div class="alert success">Course deleted.</div><?php endif; ?>
<?php if (isset($_GET['err']) && $_GET['err'] === 'in_use'): ?><div class="alert warn">Cannot delete: this course is linked to students.</div><?php endif; ?>
<div class="card"><form method="post">
<input type="hidden" name="add_course" value="1">
<label>Course Code</label><input name="code" required>
<label>Course Name</label><input name="name" required>
<label>Program Name</label><input name="program" required>
<button type="submit">Add Course</button></form></div>
<div class="table-wrap"><table><thead><tr><th>Code</th><th>Name</th><th>Program</th><th>Action</th></tr></thead><tbody>
<?php foreach($courses as $c): ?><tr><td><?= htmlspecialchars($c['code']) ?></td><td><?= htmlspecialchars($c['name']) ?></td><td><?= htmlspecialchars($c['program_name']) ?></td><td><form method="post" onsubmit="return confirm('Delete this course? If linked records exist, deletion may be blocked.');"><input type="hidden" name="delete_course" value="1"><input type="hidden" name="course_id" value="<?= (int)$c['id'] ?>"><button type="submit">Delete</button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
</main></div><script src="/slo-tool/assets/js/dashboard.js"></script></body></html>
