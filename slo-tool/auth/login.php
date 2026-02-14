<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /slo-tool/index.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare('SELECT id, name, role, email, password FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    header('Location: /slo-tool/index.php?error=1');
    exit;
}

$_SESSION['user'] = [
    'id' => (int) $user['id'],
    'name' => $user['name'],
    'role' => $user['role'],
    'email' => $user['email']
];

header('Location: ' . dashboard_by_role($user['role']));
exit;
?>
