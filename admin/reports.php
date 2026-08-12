<?php
// admin/reports.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireRole('admin');

// Monthly alerts
$months = [];
$alertCounts = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $months[] = date('M Y', strtotime($month));
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM safety_alerts WHERE DATE_FORMAT(created_at, '%Y-%m') = ?");
    $stmt->execute([$month]);
    $alertCounts[] = $stmt->fetchColumn();
}

// Status breakdown for incidents
$statusData = [];
$statusLabels = ['pending','verified','resolved','dismissed'];
foreach ($statusLabels as $s) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM incidents WHERE status = ?");
    $stmt->execute([$s]);
    $statusData[] = $stmt->fetchColumn();
}

// Category breakdown for places
$catData = [];
$catLabels = ['hospital','police','hotel','restaurant','fuel','atm','pharmacy','tourist_attraction','other'];
foreach ($catLabels as $cat) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM places WHERE category = ?");
    $stmt->execute([$cat]);
    $catData[] = $stmt->fetchColumn();
}
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <header class="top-nav"><div><button class="toggle-sidebar" id="toggleSidebarBtn"><i class="fas fa-bars"></i></button><span class="fw-semibold">Reports & Analytics</span></div></header>
    <div class="page-content">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card-custom">
                    <div class="card-header">Monthly Safety Alerts</div>
                    <div class="card-body">
                        <canvas id="alertChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-custom">
                    <div class="card-header">Incident Status</div>
                    <div class="card-body">
                        <canvas id="statusChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 mt-3">
            <div class="col-md-12">
                <div class="card-custom">
                    <div class="card-header">Place Categories Distribution</div>
                    <div class="card-body">
                        <canvas id="categoryChart" height="180"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Alert chart
    const ctx1 = document.getElementById('alertChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [{
                label: 'Alerts Posted',
                data: <?= json_encode($alertCounts) ?>,
                backgroundColor: 'rgba(26,115,232,0.6)',
                borderColor: '#1a73e8',
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    // Status chart
    const ctx2 = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_map('ucfirst', $statusLabels)) ?>,
            datasets: [{
                data: <?= json_encode($statusData) ?>,
                backgroundColor: ['#ffc107', '#0d6efd', '#198754', '#dc3545'],
                borderWidth: 2,
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, cutout: '70%' }
    });

    // Category chart
    const ctx3 = document.getElementById('categoryChart').getContext('2d');
    const catLabels = <?= json_encode(array_map('ucfirst', $catLabels)) ?>;
    const catData = <?= json_encode($catData) ?>;
    new Chart(ctx3, {
        type: 'bar',
        data: {
            labels: catLabels,
            datasets: [{
                label: 'Places',
                data: catData,
                backgroundColor: ['#1a73e8', '#0d6efd', '#28a745', '#ffc107', '#17a2b8', '#6c757d', '#dc3545', '#fd7e14', '#20c997'],
                borderRadius: 6,
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
</script>
<?php include '../includes/footer.php'; ?>