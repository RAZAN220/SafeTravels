<?php
// admin/dashboard.php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../config/functions.php';
requireRole('admin');

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role='traveler'")->fetchColumn();
$totalAlerts = $pdo->query("SELECT COUNT(*) FROM safety_alerts WHERE is_active=1 AND (expires_at IS NULL OR expires_at > NOW())")->fetchColumn();
$totalIncidents = $pdo->query("SELECT COUNT(*) FROM incidents")->fetchColumn();
$pendingIncidents = $pdo->query("SELECT COUNT(*) FROM incidents WHERE status='pending'")->fetchColumn();
$totalPlaces = $pdo->query("SELECT COUNT(*) FROM places")->fetchColumn();

$recentAlerts = $pdo->query("SELECT * FROM safety_alerts ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentIncidents = $pdo->query("SELECT i.*, u.fullname as reporter FROM incidents i JOIN users u ON i.user_id = u.id ORDER BY i.created_at DESC LIMIT 5")->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <header class="top-nav"><div><button class="toggle-sidebar" id="toggleSidebarBtn"><i class="fas fa-bars"></i></button><span class="fw-semibold">Admin Dashboard</span></div></header>
    <div class="page-content">
        <div class="row g-4 mb-4">
            <div class="col-md-3"><div class="stat-card"><div class="stat-number"><?= $totalUsers ?></div><div class="stat-label">Travelers</div></div></div>
            <div class="col-md-3"><div class="stat-card"><div class="stat-number"><?= $totalAlerts ?></div><div class="stat-label">Active Alerts</div></div></div>
            <div class="col-md-3"><div class="stat-card"><div class="stat-number"><?= $pendingIncidents ?></div><div class="stat-label">Pending Incidents</div></div></div>
            <div class="col-md-3"><div class="stat-card"><div class="stat-number"><?= $totalPlaces ?></div><div class="stat-label">Places</div></div></div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card-custom">
                    <div class="card-header">Recent Safety Alerts</div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php foreach($recentAlerts as $alert): ?>
                                <li class="list-group-item d-flex justify-content-between">
                                    <div>
                                        <?= severityBadge($alert['severity']) ?>
                                        <strong><?= htmlspecialchars($alert['title']) ?></strong>
                                        <small class="text-muted d-block"><?= htmlspecialchars($alert['location_name']) ?></small>
                                    </div>
                                    <small><?= date('d M Y', strtotime($alert['created_at'])) ?></small>
                                </li>
                            <?php endforeach; ?>
                            <?php if(empty($recentAlerts)): ?>
                                <li class="list-group-item text-muted">No alerts yet</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-custom">
                    <div class="card-header">Recent Incident Reports</div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php foreach($recentIncidents as $inc): ?>
                                <li class="list-group-item d-flex justify-content-between">
                                    <div>
                                        <span class="badge <?= $inc['status']=='pending'?'bg-warning':($inc['status']=='verified'?'bg-primary':'bg-success') ?>"><?= ucfirst($inc['status']) ?></span>
                                        <strong><?= htmlspecialchars($inc['title']) ?></strong>
                                        <small class="text-muted d-block">by <?= $inc['reporter'] ?></small>
                                    </div>
                                    <small><?= date('d M Y', strtotime($inc['created_at'])) ?></small>
                                </li>
                            <?php endforeach; ?>
                            <?php if(empty($recentIncidents)): ?>
                                <li class="list-group-item text-muted">No incidents reported</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>