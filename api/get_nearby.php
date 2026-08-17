<?php
// api/get_nearby.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireLogin();

$lat = $_GET['lat'] ?? 0;
$lng = $_GET['lng'] ?? 0;
$category = $_GET['category'] ?? 'all';
$radius = $_GET['radius'] ?? 10; // km

if (!$lat || !$lng) {
    echo json_encode(['error' => 'Latitude and longitude required']);
    exit;
}

$query = "SELECT *, 
          ( 6371 * acos( cos(radians(?)) * cos(radians(location_lat)) * cos(radians(location_lng) - radians(?)) + sin(radians(?)) * sin(radians(location_lat)) ) ) AS distance 
          FROM places 
          WHERE is_active = 1";
$params = [$lat, $lng, $lat];
if ($category !== 'all') {
    $query .= " AND category = ?";
    $params[] = $category;
}
$query .= " HAVING distance < ? ORDER BY distance";
$params[] = $radius;

$stmt = $pdo->prepare($query);
$stmt->execute($params);
echo json_encode($stmt->fetchAll());
?>