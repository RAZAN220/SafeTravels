<?php
// config/functions.php

function severityBadge($severity) {
    $map = [
        'low' => 'badge bg-success',
        'medium' => 'badge bg-warning text-dark',
        'high' => 'badge bg-danger',
        'critical' => 'badge bg-danger text-white'
    ];
    return "<span class='".($map[$severity] ?? 'badge bg-secondary')."'>".ucfirst($severity)."</span>";
}

function statusBadge($status) {
    $map = [
        'pending' => 'badge bg-warning text-dark',
        'verified' => 'badge bg-primary',
        'resolved' => 'badge bg-success',
        'dismissed' => 'badge bg-secondary'
    ];
    return "<span class='".($map[$status] ?? 'badge bg-secondary')."'>".ucfirst($status)."</span>";
}

function getDistance($lat1, $lon1, $lat2, $lon2) {
    if (!$lat1 || !$lon1 || !$lat2 || !$lon2) return null;
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    return round($miles * 1.609344, 1); // km
}

function getPlaceCategoryIcon($category) {
    $icons = [
        'hospital' => 'fa-hospital',
        'police' => 'fa-shield-halved',
        'hotel' => 'fa-hotel',
        'restaurant' => 'fa-utensils',
        'fuel' => 'fa-gas-pump',
        'atm' => 'fa-landmark',
        'pharmacy' => 'fa-prescription-bottle',
        'tourist_attraction' => 'fa-umbrella-beach',
        'other' => 'fa-location-dot'
    ];
    return $icons[$category] ?? 'fa-location-dot';
}

function getPlaceCategoryColor($category) {
    $colors = [
        'hospital' => 'text-danger',
        'police' => 'text-primary',
        'hotel' => 'text-warning',
        'restaurant' => 'text-success',
        'fuel' => 'text-info',
        'atm' => 'text-secondary',
        'pharmacy' => 'text-success',
        'tourist_attraction' => 'text-primary',
        'other' => 'text-muted'
    ];
    return $colors[$category] ?? 'text-muted';
}

function getUnreadCount($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn();
}

function getUserName($pdo, $id) {
    $stmt = $pdo->prepare("SELECT fullname FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn() ?: 'Unknown';
}
?>