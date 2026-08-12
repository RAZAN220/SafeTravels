<?php
// api/report_incident.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $_SESSION['user_id'];
$title = $data['title'] ?? '';
$description = $data['description'] ?? '';
$type = $data['type'] ?? 'other';
$severity = $data['severity'] ?? 'medium';
$lat = $data['lat'] ?? 0;
$lng = $data['lng'] ?? 0;
$location_name = $data['location_name'] ?? '';

if (!$title || !$lat || !$lng) {
    echo json_encode(['error' => 'Title, latitude, and longitude required']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO incidents (user_id, title, description, type, severity, location_lat, location_lng, location_name) VALUES (?,?,?,?,?,?,?,?)");
$stmt->execute([$user_id, $title, $description, $type, $severity, $lat, $lng, $location_name]);

echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
?>