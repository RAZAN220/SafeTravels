<?php
// traveler/safety-alerts.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireRole('traveler');

// Get all active alerts
$alerts = $pdo->query("SELECT * FROM safety_alerts WHERE is_active = 1 AND (expires_at IS NULL OR expires_at > NOW()) ORDER BY severity DESC, created_at DESC")->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <header class="top-nav"><div><button class="toggle-sidebar" id="toggleSidebarBtn"><i class="fas fa-bars"></i></button><span class="fw-semibold">Safety Alerts</span></div></header>
    <div class="page-content">
        <div class="card-custom">
            <div class="card-header"><i class="fas fa-exclamation-triangle me-2"></i>Active Safety Alerts</div>
            <div class="card-body">
                <?php if(empty($alerts)): ?>
                    <div class="alert alert-success">No active safety alerts at this time. Stay safe!</div>
                <?php else: ?>
                    <?php foreach($alerts as $alert): ?>
                        <div class="alert <?= $alert['severity'] == 'critical' ? 'alert-critical' : ($alert['severity'] == 'high' ? 'alert-high' : 'alert-medium') ?>">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5><?= htmlspecialchars($alert['title']) ?></h5>
                                    <p><?= nl2br(htmlspecialchars($alert['description'])) ?></p>
                                    <div>
                                        <?= severityBadge($alert['severity']) ?>
                                        <span class="badge bg-light text-dark">📍 <?= htmlspecialchars($alert['location_name'] ?? 'Unknown location') ?></span>
                                        <span class="badge bg-light text-dark">📂 <?= ucfirst($alert['category']) ?></span>
                                    </div>
                                    <small class="text-muted">Posted: <?= date('d M Y H:i', strtotime($alert['created_at'])) ?></small>
                                    <?php if($alert['expires_at']): ?>
                                        <small class="text-muted ms-2">Expires: <?= date('d M Y H:i', strtotime($alert['expires_at'])) ?></small>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <!-- Show on map? Could add a button -->
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Map view of alerts -->
        <?php if(!empty($alerts)): ?>
        <div class="card-custom mt-4">
            <div class="card-header">Alert Locations on Map</div>
            <div class="card-body">
                <div id="alertMap" class="map-container" style="height:350px;"></div>
            </div>
        </div>
        <script>
            var map = L.map('alertMap').setView([7.8731, 80.7718], 8);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            var alerts = <?= json_encode($alerts) ?>;
            alerts.forEach(function(alert) {
                var lat = parseFloat(alert.location_lat);
                var lng = parseFloat(alert.location_lng);
                if (lat && lng) {
                    var color = alert.severity === 'critical' ? 'red' : (alert.severity === 'high' ? 'orange' : 'yellow');
                    L.circle([lat, lng], {
                        color: color,
                        fillColor: color,
                        fillOpacity: 0.5,
                        radius: 500
                    }).addTo(map)
                    .bindPopup('<strong>'+alert.title+'</strong><br>'+alert.description+'<br><small>Severity: '+alert.severity+'</small>');
                }
            });
        </script>
        <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>