<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';
require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_student'])) {
    $studentId = (int) ($_POST['student_id'] ?? 0);
    if ($studentId > 0) {
        try {
            $stmt = $pdo->prepare('DELETE FROM students WHERE id = ?');
            $stmt->execute([$studentId]);
            header('Location: students.php?del=1');
            exit;
        } catch (PDOException $e) {
            header('Location: students.php?err=delete_failed');
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_student'])) {
    $regNo = trim($_POST['reg_no']);
    $name = trim($_POST['name']);
    $courseId = (int) $_POST['course_id'];
    $mentorId = $_POST['mentor_id'] ? (int)$_POST['mentor_id'] : null;
    $pname = trim($_POST['parent_name']);
    $pphone = trim($_POST['parent_phone']);
    $pemail = trim($_POST['parent_email']);

    $stmt = $pdo->prepare('INSERT INTO students (reg_no, name, course_id, mentor_id) VALUES (?, ?, ?, ?)');
    $stmt->execute([$regNo, $name, $courseId, $mentorId]);
    $studentId = (int)$pdo->lastInsertId();

    if ($pname && $pemail) {
        $ps = $pdo->prepare('INSERT INTO parents (student_id, name, phone, email) VALUES (?, ?, ?, ?)');
        $ps->execute([$studentId, $pname, $pphone, $pemail]);
    }

    header('Location: students.php?ok=1');
    exit;
}

$courses = $pdo->query('SELECT id, code, name FROM courses')->fetchAll();
$mentors = $pdo->query("SELECT id, name FROM users WHERE role='mentor'")->fetchAll();
$students = $pdo->query('SELECT s.id, s.reg_no, s.name, c.code as course, u.name as mentor FROM students s JOIN courses c ON s.course_id=c.id LEFT JOIN users u ON s.mentor_id=u.id ORDER BY s.id DESC')->fetchAll();
?>
