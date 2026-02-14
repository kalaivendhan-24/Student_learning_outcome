<?php require_once __DIR__ . '/../backend/faculty/marks_handler.php'; ?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Marks Entry</title><link rel="stylesheet" href="/slo-tool/assets/css/ui.css"></head>
<body><div class="wrapper"><aside class="sidebar"><h2>Faculty</h2><a href="dashboard.php">Dashboard</a><a href="co_mapping.php">CO-PO-PSO Mapping</a><a href="marks.php">Marks Entry</a><a href="reports.php">Reports</a><a href="send_alert.php">Send Alert</a><a href="/slo-tool/auth/logout.php">Logout</a></aside>
<main class="content"><h1>Student Marks Entry</h1>
<?php if (isset($_GET['ok'])): ?><div class="alert success">Marks and attainment saved.</div><?php endif; ?>
<?php if (isset($_GET['del'])): ?><div class="alert success">Marks entry deleted.</div><?php endif; ?>
<?php if (isset($_GET['err']) && $_GET['err'] === 'not_found'): ?><div class="alert warn">Mark entry not found.</div><?php endif; ?>
<?php if (isset($_GET['err']) && $_GET['err'] === 'delete_failed'): ?><div class="alert warn">Unable to delete marks entry.</div><?php endif; ?>
<div class="card"><form method="post">
<input type="hidden" name="save_marks" value="1">
<label>Student</label><select name="student_id"><?php foreach($students as $s): ?><option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['reg_no'].' - '.$s['name']) ?></option><?php endforeach; ?></select>
<label>Course</label><select name="course_id"><?php foreach($courses as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['code']) ?></option><?php endforeach; ?></select>
<label>CO</label><select name="co_id"><?php foreach($cos as $co): ?><option value="<?= $co['id'] ?>"><?= htmlspecialchars($co['code']) ?></option><?php endforeach; ?></select>
<label>Internal (100)</label><input type="number" step="0.01" max="100" min="0" name="internal_mark" required>
<label>Assignment (100)</label><input type="number" step="0.01" max="100" min="0" name="assignment_mark" required>
<label>Exam (100)</label><input type="number" step="0.01" max="100" min="0" name="exam_mark" required>
<button type="submit">Save Marks</button>
</form></div>
<div class="table-wrap"><table><thead><tr><th>Reg No</th><th>Name</th><th>Course</th><th>CO</th><th>Total %</th><th>Action</th></tr></thead><tbody>
<?php foreach($recent as $r): ?><tr><td><?= htmlspecialchars($r['reg_no']) ?></td><td><?= htmlspecialchars($r['name']) ?></td><td><?= htmlspecialchars($r['code']) ?></td><td><?= htmlspecialchars($r['co_code']) ?></td><td><?= $r['total_mark'] ?></td><td><form method="post" onsubmit="return confirm('Delete this marks entry and linked attainment values?');"><input type="hidden" name="delete_mark" value="1"><input type="hidden" name="mark_id" value="<?= (int)$r['id'] ?>"><button type="submit">Delete</button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
</main></div><script src="/slo-tool/assets/js/dashboard.js"></script></body></html>
