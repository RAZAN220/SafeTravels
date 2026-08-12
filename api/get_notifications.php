<?php
// api/get_notifications.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
echo json_encode($stmt->fetchAll());
?>