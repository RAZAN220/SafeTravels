<?php
// api/get_alerts.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

$lat = $_GET['lat'] ?? 0;
$lng = $_GET['lng'] ?? 0;
$radius = $_GET['radius'] ?? 50;

if (!$lat || !$lng) {
    echo json_encode(['error' => 'Latitude and longitude required']);
    exit;
}

$query = "SELECT *, 
          ( 6371 * acos( cos(radians(?)) * cos(radians(location_lat)) * cos(radians(location_lng) - radians(?)) + sin(radians(?)) * sin(radians(location_lat)) ) ) AS distance 
          FROM safety_alerts 
          WHERE is_active = 1 AND (expires_at IS NULL OR expires_at > NOW())";
$params = [$lat, $lng, $lat];
$query .= " HAVING distance < ? ORDER BY severity DESC, distance";
$params[] = $radius;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
echo json_encode($stmt->fetchAll());
?>