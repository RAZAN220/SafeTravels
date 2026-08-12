<?php
// index.php
require_once 'config/database.php';
require_once 'config/auth.php';
requireLogin();

$role = $_SESSION['role'];
if ($role === 'admin') {
    header('Location: admin/dashboard.php');
    exit;
}
header('Location: traveler/dashboard.php');
exit;
?>