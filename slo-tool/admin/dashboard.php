<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_role(['admin']);

$totalUsers = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalStudents = $pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$totalCourses = $pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn();
$totalMentors = $pdo->query("SELECT COUNT(*) FROM users WHERE role='mentor'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title><link rel="stylesheet" href="/slo-tool/assets/css/ui.css">
</head><body>
<div class="wrapper">
  <aside class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="users.php">Manage Users</a>
    <a href="courses.php">Programs & Courses</a>
    <a href="mentors.php">Mentor Assignment</a>
    <a href="students.php">Student Registration</a>
    <a href="/slo-tool/auth/logout.php">Logout</a>
  </aside>
  <main class="content">
    <h1>Institution Analytics</h1>
    <div class="card-grid">
      <div class="card"><h3>Total Users</h3><div class="kpi"><?= $totalUsers ?></div></div>
      <div class="card"><h3>Total Students</h3><div class="kpi"><?= $totalStudents ?></div></div>
      <div class="card"><h3>Total Courses</h3><div class="kpi"><?= $totalCourses ?></div></div>
      <div class="card"><h3>Total Mentors</h3><div class="kpi"><?= $totalMentors ?></div></div>
    </div>
  </main>
</div>
<script src="/slo-tool/assets/js/dashboard.js"></script>
</body></html>
