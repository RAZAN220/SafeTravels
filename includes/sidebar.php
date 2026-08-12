<?php
// includes/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? 'traveler';
$unread = getUnreadCount($pdo, $_SESSION['user_id'] ?? 0);
?>
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-shield-halved"></i>
        <div>
            <span>Safe Travels</span>
            <small>Travel Safety App</small>
        </div>
    </div>
    <div class="sidebar-menu">
        <div class="menu-label">Main</div>
        <a href="../index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>"><i class="fas fa-home"></i> Dashboard</a>

        <?php if ($role === 'traveler'): ?>
            <a href="../traveler/plan-trip.php" class="<?= $current_page == 'plan-trip.php' ? 'active' : '' ?>"><i class="fas fa-map"></i> Plan Trip</a>
            <a href="../traveler/route-planner.php" class="<?= $current_page == 'route-planner.php' ? 'active' : '' ?>"><i class="fas fa-route"></i> Route Planner</a>
            <a href="../traveler/safety-alerts.php" class="<?= $current_page == 'safety-alerts.php' ? 'active' : '' ?>"><i class="fas fa-exclamation-triangle"></i> Safety Alerts</a>
            <a href="../traveler/nearby-places.php" class="<?= $current_page == 'nearby-places.php' ? 'active' : '' ?>"><i class="fas fa-location-dot"></i> Nearby Places</a>
            <a href="../traveler/emergency.php" class="<?= $current_page == 'emergency.php' ? 'active' : '' ?>"><i class="fas fa-phone"></i> Emergency</a>
            <a href="../traveler/report-incident.php" class="<?= $current_page == 'report-incident.php' ? 'active' : '' ?>"><i class="fas fa-flag"></i> Report Incident</a>
            <a href="../traveler/travel-history.php" class="<?= $current_page == 'travel-history.php' ? 'active' : '' ?>"><i class="fas fa-clock-rotate-left"></i> Travel History</a>

        <?php elseif ($role === 'admin'): ?>
            <a href="../admin/dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a href="../admin/users.php" class="<?= $current_page == 'users.php' ? 'active' : '' ?>"><i class="fas fa-users"></i> Users</a>
            <a href="../admin/alerts.php" class="<?= $current_page == 'alerts.php' ? 'active' : '' ?>"><i class="fas fa-exclamation-triangle"></i> Safety Alerts</a>
            <a href="../admin/incidents.php" class="<?= $current_page == 'incidents.php' ? 'active' : '' ?>"><i class="fas fa-flag"></i> Incidents</a>
            <a href="../admin/places.php" class="<?= $current_page == 'places.php' ? 'active' : '' ?>"><i class="fas fa-location-dot"></i> Places</a>
            <a href="../admin/reports.php" class="<?= $current_page == 'reports.php' ? 'active' : '' ?>"><i class="fas fa-chart-bar"></i> Reports</a>
        <?php endif; ?>

        <div class="menu-label mt-3">Account</div>
        <a href="../traveler/profile.php" class="<?= $current_page == 'profile.php' ? 'active' : '' ?>"><i class="fas fa-user"></i> Profile</a>
        <a href="../traveler/notifications.php" class="<?= $current_page == 'notifications.php' ? 'active' : '' ?>">
            <i class="fas fa-bell"></i> Notifications
            <?php if($unread > 0): ?>
                <span class="badge bg-danger ms-2"><?= $unread ?></span>
            <?php endif; ?>
        </a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</nav>