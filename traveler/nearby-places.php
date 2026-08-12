<?php
// traveler/nearby-places.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireRole('traveler');

$category = $_GET['category'] ?? 'all';
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM places WHERE is_active = 1";
$params = [];
if ($category !== 'all') {
    $query .= " AND category = ?";
    $params[] = $category;
}
if ($search) {
    $query .= " AND (name LIKE ? OR address LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$query .= " ORDER BY name";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$places = $stmt->fetchAll();

$categories = ['hospital','police','hotel','restaurant','fuel','atm','pharmacy','tourist_attraction','other'];
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <header class="top-nav"><div><button class="toggle-sidebar" id="toggleSidebarBtn"><i class="fas fa-bars"></i></button><span class="fw-semibold">Nearby Places</span></div></header>
    <div class="page-content">
        <div class="card-custom mb-4">
            <div class="card-header">Filter Places</div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <select name="category" class="form-select">
                            <option value="all">All Categories</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat ?>" <?= $category==$cat?'selected':'' ?>><?= ucfirst($cat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or address..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-custom">
            <div class="card-header">Places (<?= count($places) ?>)</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0" id="placesTable">
                    <thead><tr><th>Icon</th><th>Name</th><th>Category</th><th>Address</th><th>Phone</th><th>Rating</th></tr></thead>
                    <tbody>
                    <?php foreach($places as $p): ?>
                    <tr>
                        <td><i class="fas <?= getPlaceCategoryIcon($p['category']) ?> <?= getPlaceCategoryColor($p['category']) ?> fa-2x"></i></td>
                        <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                        <td><span class="badge bg-secondary"><?= ucfirst($p['category']) ?></span></td>
                        <td><?= htmlspecialchars($p['address']) ?></td>
                        <td><?= $p['phone'] ?: 'N/A' ?></td>
                        <td><?= $p['rating'] ? number_format($p['rating'],1).' ⭐' : '—' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($places)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-3">No places found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Map view -->
        <div class="card-custom mt-4">
            <div class="card-header">Places on Map</div>
            <div class="card-body">
                <div id="placesMap" class="map-container" style="height:350px;"></div>
            </div>
        </div>
        <script>
            var map = L.map('placesMap').setView([7.8731, 80.7718], 8);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            var places = <?= json_encode($places) ?>;
            var markers = L.markerClusterGroup().addTo(map);
            places.forEach(function(place) {
                var lat = parseFloat(place.location_lat);
                var lng = parseFloat(place.location_lng);
                if (lat && lng) {
                    var iconColor = place.category === 'hospital' ? 'red' : (place.category === 'police' ? 'blue' : 'green');
                    var marker = L.marker([lat, lng]).bindPopup(
                        '<strong>'+place.name+'</strong><br>'+
                        place.address+'<br>'+
                        (place.phone ? '📞 '+place.phone : '') +
                        '<br><span class="badge bg-secondary">'+place.category+'</span>'
                    );
                    markers.addLayer(marker);
                }
            });
        </script>
    </div>
</div>
<script>
    $(document).ready(function() { $('#placesTable').DataTable({ pageLength: 10 }); });
</script>
<?php include '../includes/footer.php'; ?>