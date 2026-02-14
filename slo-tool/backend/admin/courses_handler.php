<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';
require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_course'])) {
    $courseId = (int) ($_POST['course_id'] ?? 0);
    if ($courseId > 0) {
        try {
            $stmt = $pdo->prepare('DELETE FROM courses WHERE id = ?');
            $stmt->execute([$courseId]);
            header('Location: courses.php?del=1');
            exit;
        } catch (PDOException $e) {
            header('Location: courses.php?err=in_use');
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $name = trim($_POST['name']);
    $code = trim($_POST['code']);
    $program = trim($_POST['program']);
    $stmt = $pdo->prepare('INSERT INTO courses (code, name, program_name) VALUES (?, ?, ?)');
    $stmt->execute([$code, $name, $program]);
    header('Location: courses.php?ok=1');
    exit;
}

$courses = $pdo->query('SELECT * FROM courses ORDER BY id DESC')->fetchAll();
?>
