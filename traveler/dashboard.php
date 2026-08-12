<?php
// traveler/dashboard.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireRole('traveler');
$user_id = $_SESSION['user_id'];

// Stats
$totalTrips = $pdo->prepare("SELECT COUNT(*) FROM travel_history WHERE user_id = ?");
$totalTrips->execute([$user_id]);
$totalTrips = $totalTrips->fetchColumn();

$totalAlerts = $pdo->query("SELECT COUNT(*) FROM safety_alerts WHERE is_active = 1 AND expires_at > NOW()")->fetchColumn();

$unreadNotifs = getUnreadCount($pdo, $user_id);

$recentAlerts = $pdo->query("SELECT * FROM safety_alerts WHERE is_active = 1 AND expires_at > NOW() ORDER BY severity DESC LIMIT 3")->fetchAll();

$nearbyEmergencies = $pdo->query("SELECT * FROM emergency_contacts WHERE is_active = 1 LIMIT 4")->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <header class="top-nav">
        <div><button class="toggle-sidebar" id="toggleSidebarBtn"><i class="fas fa-bars"></i></button><span class="d-none d-md-inline fw-semibold fs-6">Traveler Dashboard</span></div>
        <div class="user-dropdown">
            <a href="notifications.php" class="text-dark position-relative me-2">
                <i class="fas fa-bell fs-5"></i>
                <?php if($unreadNotifs > 0): ?><span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill"><?= $unreadNotifs ?></span><?php endif; ?>
            </a>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center gap-2 text-decoration-none text-dark" data-bs-toggle="dropdown">
                    <div class="avatar"><?= strtoupper(substr($_SESSION['fullname'],0,2)) ?></div>
                    <span class="d-none d-sm-inline fw-semibold"><?= $_SESSION['fullname'] ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                    <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </header>
    <div class="page-content">
        <div class="row g-4 mb-4">
            <div class="col-6 col-xl-3"><div class="stat-card d-flex justify-content-between"><div><div class="stat-number"><?= $totalTrips ?></div><div class="stat-label">Trips Planned</div></div><div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-plane"></i></div></div></div>
            <div class="col-6 col-xl-3"><div class="stat-card d-flex justify-content-between"><div><div class="stat-number"><?= $totalAlerts ?></div><div class="stat-label">Active Alerts</div></div><div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="fas fa-exclamation-triangle"></i></div></div></div>
            <div class="col-6 col-xl-3"><div class="stat-card d-flex justify-content-between"><div><div class="stat-number"><?= $unreadNotifs ?></div><div class="stat-label">Notifications</div></div><div class="stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-bell"></i></div></div></div>
            <div class="col-6 col-xl-3"><div class="stat-card d-flex justify-content-between"><div><div class="stat-number">4</div><div class="stat-label">Emergency Services</div></div><div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="fas fa-phone"></i></div></div></div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-3 mb-4">
            <div class="col-md-3"><a href="plan-trip.php" class="text-decoration-none"><div class="card-custom text-center p-4"><i class="fas fa-map fa-3x text-primary mb-2"></i><h6>Plan a Trip</h6></div></a></div>
            <div class="col-md-3"><a href="route-planner.php" class="text-decoration-none"><div class="card-custom text-center p-4"><i class="fas fa-route fa-3x text-success mb-2"></i><h6>Route Planner</h6></div></a></div>
            <div class="col-md-3"><a href="safety-alerts.php" class="text-decoration-none"><div class="card-custom text-center p-4"><i class="fas fa-exclamation-triangle fa-3x text-warning mb-2"></i><h6>Safety Alerts</h6></div></a></div>
            <div class="col-md-3"><a href="emergency.php" class="text-decoration-none"><div class="card-custom text-center p-4" style="border:2px solid #c62828;"><i class="fas fa-phone fa-3x text-danger mb-2"></i><h6>🚨 Emergency</h6></div></a></div>
        </div>

        <!-- Active Safety Alerts -->
        <div class="card-custom mb-4">
            <div class="card-header"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Active Safety Alerts</div>
            <div class="card-body">
                <?php if(empty($recentAlerts)): ?>
                    <p class="text-muted">No active safety alerts at this time. Stay safe!</p>
                <?php else: ?>
                    <?php foreach($recentAlerts as $alert): ?>
                        <div class="alert <?= $alert['severity'] == 'critical' ? 'alert-critical' : ($alert['severity'] == 'high' ? 'alert-high' : 'alert-medium') ?> d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= htmlspecialchars($alert['title']) ?></strong>
                                <p class="mb-0 small"><?= htmlspecialchars($alert['description']) ?></p>
                                <span class="small text-muted">📍 <?= $alert['location_name'] ?> • <?= severityBadge($alert['severity']) ?></span>
                            </div>
                            <span class="badge bg-light text-dark"><?= date('d M H:i', strtotime($alert['created_at'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <a href="safety-alerts.php" class="btn btn-sm btn-outline-primary">View All Alerts</a>
            </div>
        </div>

        <!-- Emergency Contacts -->
        <div class="card-custom">
            <div class="card-header"><i class="fas fa-phone text-danger me-2"></i>Emergency Contacts</div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach($nearbyEmergencies as $ec): ?>
                        <div class="col-md-3">
                            <div class="emergency-card text-center">
                                <i class="fas <?= $ec['category'] == 'police' ? 'fa-shield-halved' : ($ec['category'] == 'ambulance' ? 'fa-truck-medical' : ($ec['category'] == 'fire' ? 'fa-fire-extinguisher' : 'fa-phone')) ?> fa-2x text-danger mb-2"></i>
                                <h6><?= htmlspecialchars($ec['name']) ?></h6>
                                <p class="mb-0"><strong><?= $ec['phone'] ?></strong></p>
                                <small class="text-muted"><?= ucfirst($ec['category']) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>