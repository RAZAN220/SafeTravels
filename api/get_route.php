<?php
// api/get_route.php (simplified – just returns distance)
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../config/functions.php';
requireLogin();

$from_lat = $_GET['from_lat'] ?? 0;
$from_lng = $_GET['from_lng'] ?? 0;
$to_lat = $_GET['to_lat'] ?? 0;
$to_lng = $_GET['to_lng'] ?? 0;

if (!$from_lat || !$from_lng || !$to_lat || !$to_lng) {
    echo json_encode(['error' => 'All coordinates required']);
    exit;
}

$distance = getDistance($from_lat, $from_lng, $to_lat, $to_lng);
echo json_encode([
    'distance_km' => $distance,
    'from' => ['lat' => $from_lat, 'lng' => $from_lng],
    'to' => ['lat' => $to_lat, 'lng' => $to_lng]
]);
?>