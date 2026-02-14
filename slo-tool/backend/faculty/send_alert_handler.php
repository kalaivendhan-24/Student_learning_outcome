<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';
require_role(['faculty']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_message'])) {
    $messageId = (int) ($_POST['message_id'] ?? 0);
    if ($messageId > 0) {
        try {
            $del = $pdo->prepare('DELETE FROM messages WHERE id = ?');
            $del->execute([$messageId]);
            header('Location: send_alert.php?del=1');
            exit;
        } catch (PDOException $e) {
            header('Location: send_alert.php?err=delete_failed');
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_alert'])) {
    $studentId = (int) $_POST['student_id'];
    $subject = trim($_POST['subject']);
    $body = trim($_POST['body']);

    $parent = $pdo->prepare('SELECT id FROM parents WHERE student_id=? LIMIT 1');
    $parent->execute([$studentId]);
    $p = $parent->fetch();
    if ($p) {
        $stmt = $pdo->prepare('INSERT INTO messages (sender_id, parent_id, student_id, subject, body) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$_SESSION['user']['id'], (int)$p['id'], $studentId, $subject, $body]);
    }

    header('Location: send_alert.php?ok=1');
    exit;
}

$students = $pdo->query('SELECT id, reg_no, name FROM students ORDER BY reg_no')->fetchAll();
$messages = $pdo->query('SELECT m.id, m.sent_at, m.subject, m.body, s.reg_no, s.name AS student_name, p.name AS parent_name, u.name AS sender_name, u.role AS sender_role FROM messages m JOIN students s ON m.student_id=s.id JOIN parents p ON m.parent_id=p.id JOIN users u ON m.sender_id=u.id ORDER BY m.id DESC LIMIT 100')->fetchAll();
?>
