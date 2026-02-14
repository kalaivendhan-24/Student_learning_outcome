<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_role(['mentor']);

$mentorId = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = (int) $_POST['student_id'];
    $subject = trim($_POST['subject']);
    $body = trim($_POST['body']);

    $parent = $pdo->prepare('SELECT id FROM parents WHERE student_id=? LIMIT 1');
    $parent->execute([$studentId]);
    $p = $parent->fetch();
    if ($p) {
        $ins = $pdo->prepare('INSERT INTO messages (sender_id, parent_id, student_id, subject, body) VALUES (?, ?, ?, ?, ?)');
        $ins->execute([$mentorId, (int)$p['id'], $studentId, $subject, $body]);
    }

    header('Location: messages.php?ok=1');
    exit;
}

$students = $pdo->prepare('SELECT id, reg_no, name FROM students WHERE mentor_id=? ORDER BY reg_no');
$students->execute([$mentorId]);
$studentList = $students->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Mentor Messages</title><link rel="stylesheet" href="/slo-tool/assets/css/ui.css"></head>
<body><div class="wrapper"><aside class="sidebar"><h2>Mentor</h2><a href="dashboard.php">Dashboard</a><a href="feedback.php">Feedback & Action Plan</a><a href="messages.php">Parent Messages</a><a href="/slo-tool/auth/logout.php">Logout</a></aside>
<main class="content"><h1>Communicate with Parent</h1>
<?php if (isset($_GET['ok'])): ?><div class="alert success">Message sent.</div><?php endif; ?>
<div class="card"><form method="post">
<label>Student</label><select name="student_id"><?php foreach($studentList as $s): ?><option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['reg_no'].' - '.$s['name']) ?></option><?php endforeach; ?></select>
<label>Subject</label><input name="subject" required>
<label>Message</label><textarea name="body" required></textarea>
<button type="submit">Send Message</button>
</form></div>
</main></div><script src="/slo-tool/assets/js/dashboard.js"></script></body></html>
