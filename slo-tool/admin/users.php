<?php require_once __DIR__ . '/../backend/admin/users_handler.php'; ?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Manage Users</title><link rel="stylesheet" href="/slo-tool/assets/css/ui.css"></head>
<body><div class="wrapper">
<aside class="sidebar"><h2>Admin Panel</h2><a href="dashboard.php">Dashboard</a><a href="users.php">Manage Users</a><a href="courses.php">Programs & Courses</a><a href="mentors.php">Mentor Assignment</a><a href="students.php">Student Registration</a><a href="/slo-tool/auth/logout.php">Logout</a></aside>
<main class="content">
<h1>Manage Users</h1>
<?php if (isset($_GET['ok'])): ?><div class="alert success">User added.</div><?php endif; ?>
<?php if (isset($_GET['del'])): ?><div class="alert success">User deleted.</div><?php endif; ?>
<?php if (isset($_GET['err']) && $_GET['err'] === 'self'): ?><div class="alert warn">You cannot delete your own account while logged in.</div><?php endif; ?>
<?php if (isset($_GET['err']) && $_GET['err'] === 'delete_failed'): ?><div class="alert warn">Unable to delete this user.</div><?php endif; ?>
<div class="card">
<form method="post">
<input type="hidden" name="create_user" value="1">
<label>Name</label><input name="name" required>
<label>Role</label>
<select name="role" required>
<option value="faculty">Faculty</option>
<option value="mentor">Mentor</option>
<option value="parent">Parent</option>
<option value="admin">Admin</option>
</select>
<label>Email</label><input type="email" name="email" required>
<label>Password</label><input type="password" name="password" required>
<button type="submit">Create User</button>
</form>
</div>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>Name</th><th>Role</th><th>Email</th><th>Action</th></tr></thead><tbody>
<?php foreach($users as $u): ?>
<tr>
<td><?= $u['id'] ?></td>
<td><?= htmlspecialchars($u['name']) ?></td>
<td><?= $u['role'] ?></td>
<td><?= htmlspecialchars($u['email']) ?></td>
<td>
<form method="post" onsubmit="return confirm('Delete this user? This action cannot be undone.');">
<input type="hidden" name="delete_user" value="1">
<input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
<button type="submit">Delete</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
</main></div><script src="/slo-tool/assets/js/dashboard.js"></script></body></html>
