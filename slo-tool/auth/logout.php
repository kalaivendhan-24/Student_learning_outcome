<?php
require_once __DIR__ . '/../config/auth.php';
session_destroy();
header('Location: /slo-tool/index.php');
exit;
?>
