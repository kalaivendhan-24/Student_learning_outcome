<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/auth.php';
require_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $deleteId = (int) ($_POST['user_id'] ?? 0);
    $currentUserId = (int) ($_SESSION['user']['id'] ?? 0);
    if ($deleteId > 0) {
        if ($deleteId === $currentUserId) {
            header('Location: users.php?err=self');
            exit;
        }
        try {
            $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$deleteId]);
            header('Location: users.php?del=1');
            exit;
        } catch (PDOException $e) {
            header('Location: users.php?err=delete_failed');
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $name = trim($_POST['name']);
    $role = $_POST['role'];
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('INSERT INTO users (name, role, email, password) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $role, $email, $password]);
    header('Location: users.php?ok=1');
    exit;
}

$users = $pdo->query('SELECT id, name, role, email FROM users ORDER BY id DESC')->fetchAll();
?>
