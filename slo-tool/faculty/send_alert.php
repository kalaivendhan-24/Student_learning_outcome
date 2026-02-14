<?php require_once __DIR__ . '/../backend/faculty/send_alert_handler.php'; ?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Send Alerts</title><link rel="stylesheet" href="/slo-tool/assets/css/ui.css"></head>
<body><div class="wrapper"><aside class="sidebar"><h2>Faculty</h2><a href="dashboard.php">Dashboard</a><a href="co_mapping.php">CO-PO-PSO Mapping</a><a href="marks.php">Marks Entry</a><a href="reports.php">Reports</a><a href="send_alert.php">Send Alert</a><a href="/slo-tool/auth/logout.php">Logout</a></aside>
<main class="content"><h1>Send Academic Alert to Parent</h1>
<?php if (isset($_GET['ok'])): ?><div class="alert success">Alert sent to parent.</div><?php endif; ?>
<?php if (isset($_GET['del'])): ?><div class="alert success">Message deleted.</div><?php endif; ?>
<?php if (isset($_GET['err']) && $_GET['err'] === 'delete_failed'): ?><div class="alert warn">Unable to delete message.</div><?php endif; ?>
<div class="card"><form method="post">
<input type="hidden" name="send_alert" value="1">
<label>Student</label><select name="student_id"><?php foreach($students as $s): ?><option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['reg_no'].' - '.$s['name']) ?></option><?php endforeach; ?></select>
<label>Subject</label><input name="subject" required>
<label>Message</label><textarea name="body" required></textarea>
<button type="submit">Send Alert</button>
</form></div>
<div class="card">
<h3>Messages Sent to Parents</h3>
<div class="table-wrap"><table><thead><tr><th>Date</th><th>Student</th><th>Parent</th><th>From</th><th>Subject</th><th>Message</th><th>Action</th></tr></thead><tbody>
<?php foreach($messages as $m): ?><tr><td><?= htmlspecialchars($m['sent_at']) ?></td><td><?= htmlspecialchars($m['reg_no'].' - '.$m['student_name']) ?></td><td><?= htmlspecialchars($m['parent_name']) ?></td><td><?= htmlspecialchars($m['sender_name'].' ('.$m['sender_role'].')') ?></td><td><?= htmlspecialchars($m['subject']) ?></td><td><?= htmlspecialchars($m['body']) ?></td><td><form method="post" onsubmit="return confirm('Delete this parent message?');"><input type="hidden" name="delete_message" value="1"><input type="hidden" name="message_id" value="<?= (int)$m['id'] ?>"><button type="submit">Delete</button></form></td></tr><?php endforeach; ?>
</tbody></table></div>
</div>
</main></div><script src="/slo-tool/assets/js/dashboard.js"></script></body></html>
