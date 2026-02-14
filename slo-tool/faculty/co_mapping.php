<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_role(['faculty']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_co'])) {
    $stmt = $pdo->prepare('INSERT INTO course_outcomes (course_id, code, description, target_level) VALUES (?, ?, ?, ?)');
    $stmt->execute([(int)$_POST['course_id'], trim($_POST['code']), trim($_POST['description']), (int)$_POST['target_level']]);
    header('Location: co_mapping.php?ok=1');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_po'])) {
    $stmt = $pdo->prepare('INSERT INTO program_outcomes (code, description, outcome_type) VALUES (?, ?, ?)');
    $stmt->execute([trim($_POST['po_code']), trim($_POST['po_desc']), $_POST['outcome_type']]);
    header('Location: co_mapping.php?ok=1');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['map_co'])) {
    $stmt = $pdo->prepare('INSERT INTO co_po_mapping (co_id, po_id, weight) VALUES (?, ?, ?)');
    $stmt->execute([(int)$_POST['co_id'], (int)$_POST['po_id'], (int)$_POST['weight']]);
    header('Location: co_mapping.php?ok=1');
    exit;
}

$courses = $pdo->query('SELECT id, code, name FROM courses')->fetchAll();
$cos = $pdo->query('SELECT id, code FROM course_outcomes')->fetchAll();
$pos = $pdo->query('SELECT id, code, outcome_type FROM program_outcomes')->fetchAll();
$mappings = $pdo->query('SELECT c.code as co_code, p.code as po_code, p.outcome_type, m.weight FROM co_po_mapping m JOIN course_outcomes c ON m.co_id=c.id JOIN program_outcomes p ON m.po_id=p.id ORDER BY c.code')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>CO Mapping</title><link rel="stylesheet" href="/slo-tool/assets/css/ui.css"></head>
<body><div class="wrapper"><aside class="sidebar"><h2>Faculty</h2><a href="dashboard.php">Dashboard</a><a href="co_mapping.php">CO-PO-PSO Mapping</a><a href="marks.php">Marks Entry</a><a href="reports.php">Reports</a><a href="send_alert.php">Send Alert</a><a href="/slo-tool/auth/logout.php">Logout</a></aside>
<main class="content"><h1>CO / PO / PSO Management</h1>
<?php if (isset($_GET['ok'])): ?><div class="alert success">Saved successfully.</div><?php endif; ?>
<div class="card-grid">
<div class="card"><h3>Create Course Outcome</h3><form method="post">
<input type="hidden" name="create_co" value="1">
<label>Course</label><select name="course_id"><?php foreach($courses as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['code'].' - '.$c['name']) ?></option><?php endforeach; ?></select>
<label>CO Code</label><input name="code" placeholder="CO1" required>
<label>Description</label><textarea name="description" required></textarea>
<label>Target Level (1-3)</label><input type="number" min="1" max="3" name="target_level" required>
<button type="submit">Add CO</button>
</form></div>
<div class="card"><h3>Create PO / PSO</h3><form method="post">
<input type="hidden" name="create_po" value="1">
<label>Code</label><input name="po_code" placeholder="PO1 / PSO1" required>
<label>Description</label><textarea name="po_desc" required></textarea>
<label>Type</label><select name="outcome_type"><option value="PO">PO</option><option value="PSO">PSO</option></select>
<button type="submit">Add Outcome</button>
</form></div>
<div class="card"><h3>Map CO to PO/PSO</h3><form method="post">
<input type="hidden" name="map_co" value="1">
<label>CO</label><select name="co_id"><?php foreach($cos as $co): ?><option value="<?= $co['id'] ?>"><?= htmlspecialchars($co['code']) ?></option><?php endforeach; ?></select>
<label>PO / PSO</label><select name="po_id"><?php foreach($pos as $po): ?><option value="<?= $po['id'] ?>"><?= htmlspecialchars($po['code'].' ('.$po['outcome_type'].')') ?></option><?php endforeach; ?></select>
<label>Weight (1-3)</label><input type="number" min="1" max="3" name="weight" required>
<button type="submit">Save Mapping</button>
</form></div>
</div>
<div class="table-wrap"><table><thead><tr><th>CO</th><th>PO/PSO</th><th>Type</th><th>Weight</th></tr></thead><tbody>
<?php foreach($mappings as $m): ?><tr><td><?= htmlspecialchars($m['co_code']) ?></td><td><?= htmlspecialchars($m['po_code']) ?></td><td><?= htmlspecialchars($m['outcome_type']) ?></td><td><?= $m['weight'] ?></td></tr><?php endforeach; ?>
</tbody></table></div>
</main></div><script src="/slo-tool/assets/js/dashboard.js"></script></body></html>
