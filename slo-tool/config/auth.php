<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: /slo-tool/index.php');
        exit;
    }
}

function require_role(array $roles): void {
    require_login();
    if (!in_array($_SESSION['user']['role'], $roles, true)) {
        http_response_code(403);
        exit('Access denied.');
    }
}

function dashboard_by_role(string $role): string {
    return match($role) {
        'admin' => '/slo-tool/admin/dashboard.php',
        'faculty' => '/slo-tool/faculty/dashboard.php',
        'mentor' => '/slo-tool/mentor/dashboard.php',
        'parent' => '/slo-tool/parent/dashboard.php',
        default => '/slo-tool/index.php'
    };
}
?>
