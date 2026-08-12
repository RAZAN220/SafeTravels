<?php
// traveler/emergency.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireRole('traveler');

$emergencies = $pdo->query("SELECT * FROM emergency_contacts WHERE is_active = 1 ORDER BY category")->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <header class="top-nav"><div><button class="toggle-sidebar" id="toggleSidebarBtn"><i class="fas fa-bars"></i></button><span class="fw-semibold">🚨 Emergency Assistance</span></div></header>
    <div class="page-content">
        <div class="alert alert-danger text-center">
            <h4><i class="fas fa-exclamation-triangle"></i> In case of emergency, call the nearest service immediately</h4>
            <p class="mb-0">For life-threatening emergencies, dial <strong>119</strong> (Police) or <strong>110</strong> (Ambulance) or <strong>112</strong> (Fire)</p>
        </div>

        <div class="row g-4">
            <?php foreach($emergencies as $ec): ?>
                <div class="col-md-3">
                    <div class="emergency-card text-center">
                        <i class="fas <?= $ec['category'] == 'police' ? 'fa-shield-halved' : ($ec['category'] == 'ambulance' ? 'fa-truck-medical' : ($ec['category'] == 'fire' ? 'fa-fire-extinguisher' : 'fa-phone')) ?> fa-3x text-danger mb-3"></i>
                        <h5><?= htmlspecialchars($ec['name']) ?></h5>
                        <p class="display-6 fw-bold"><?= $ec['phone'] ?></p>
                        <p class="text-muted small"><?= ucfirst($ec['category']) ?></p>
                        <?php if($ec['description']): ?>
                            <p class="small"><?= htmlspecialchars($ec['description']) ?></p>
                        <?php endif; ?>
                        <a href="tel:<?= $ec['phone'] ?>" class="btn btn-danger btn-lg w-100"><i class="fas fa-phone me-2"></i>Call Now</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card-custom mt-4">
            <div class="card-header">Safety Tips</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6><i class="fas fa-check-circle text-success me-2"></i>Before You Travel</h6>
                        <ul>
                            <li>Share your itinerary with family</li>
                            <li>Keep emergency contacts handy</li>
                            <li>Check travel advisories</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="fas fa-check-circle text-success me-2"></i>During Travel</h6>
                        <ul>
                            <li>Stay aware of surroundings</li>
                            <li>Avoid isolated areas at night</li>
                            <li>Keep valuables secure</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6><i class="fas fa-check-circle text-success me-2"></i>In Emergency</h6>
                        <ul>
                            <li>Call emergency services immediately</li>
                            <li>Move to a safe location</li>
                            <li>Contact your embassy if abroad</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>