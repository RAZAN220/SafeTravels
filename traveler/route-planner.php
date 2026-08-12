<?php
// traveler/route-planner.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireRole('traveler');

// Fetch some pre-defined locations for demo
$locations = [
    ['name' => 'Colombo Fort', 'lat' => 6.9344, 'lng' => 79.8427],
    ['name' => 'Kandy City', 'lat' => 7.2906, 'lng' => 80.6337],
    ['name' => 'Galle Fort', 'lat' => 6.0535, 'lng' => 80.2210],
    ['name' => 'Nuwara Eliya', 'lat' => 6.9497, 'lng' => 80.7891],
    ['name' => 'Ella', 'lat' => 6.8667, 'lng' => 81.0500],
    ['name' => 'Sigiriya', 'lat' => 7.9569, 'lng' => 80.7597],
];
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <header class="top-nav"><div><button class="toggle-sidebar" id="toggleSidebarBtn"><i class="fas fa-bars"></i></button><span class="fw-semibold">Route Planner</span></div></header>
    <div class="page-content">
        <div class="card-custom">
            <div class="card-header">Plan Your Route</div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-5">
                        <label class="form-label">Start Location</label>
                        <select id="startLocation" class="form-select">
                            <?php foreach($locations as $loc): ?>
                                <option value="<?= $loc['lat'] ?>,<?= $loc['lng'] ?>"><?= $loc['name'] ?></option>
                            <?php endforeach; ?>
                            <option value="custom">Custom</option>
                        </select>
                        <input type="text" id="customStart" class="form-control mt-2" placeholder="Enter custom start" style="display:none;">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">End Location</label>
                        <select id="endLocation" class="form-select">
                            <?php foreach($locations as $loc): ?>
                                <option value="<?= $loc['lat'] ?>,<?= $loc['lng'] ?>"><?= $loc['name'] ?></option>
                            <?php endforeach; ?>
                            <option value="custom">Custom</option>
                        </select>
                        <input type="text" id="customEnd" class="form-control mt-2" placeholder="Enter custom end" style="display:none;">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100" id="showRouteBtn">Show Route</button>
                    </div>
                </div>
                <div id="routeMap" class="map-container" style="height:450px;"></div>
                <div id="routeInfo" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>
<script>
    // Initialize map
    var map = L.map('routeMap').setView([7.8731, 80.7718], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    var routeLayer = L.layerGroup().addTo(map);
    var markers = L.markerClusterGroup().addTo(map);

    // Function to get coordinates from input
    function getCoords(value) {
        if (value === 'custom') return null;
        var parts = value.split(',');
        if (parts.length === 2) {
            return { lat: parseFloat(parts[0]), lng: parseFloat(parts[1]) };
        }
        return null;
    }

    // Show/hide custom inputs
    document.getElementById('startLocation').addEventListener('change', function() {
        document.getElementById('customStart').style.display = this.value === 'custom' ? 'block' : 'none';
    });
    document.getElementById('endLocation').addEventListener('change', function() {
        document.getElementById('customEnd').style.display = this.value === 'custom' ? 'block' : 'none';
    });

    document.getElementById('showRouteBtn').addEventListener('click', function() {
        var startVal = document.getElementById('startLocation').value;
        var endVal = document.getElementById('endLocation').value;
        var start = getCoords(startVal);
        var end = getCoords(endVal);

        if (!start) {
            // Try custom start
            var customStart = document.getElementById('customStart').value;
            if (customStart) {
                // Could geocode, but for demo we assume they entered coordinates
                var parts = customStart.split(',');
                if (parts.length === 2) {
                    start = { lat: parseFloat(parts[0]), lng: parseFloat(parts[1]) };
                } else {
                    alert('Please enter valid coordinates (lat,lng)');
                    return;
                }
            } else {
                alert('Please select or enter start location');
                return;
            }
        }

        if (!end) {
            var customEnd = document.getElementById('customEnd').value;
            if (customEnd) {
                var parts = customEnd.split(',');
                if (parts.length === 2) {
                    end = { lat: parseFloat(parts[0]), lng: parseFloat(parts[1]) };
                } else {
                    alert('Please enter valid coordinates (lat,lng)');
                    return;
                }
            } else {
                alert('Please select or enter end location');
                return;
            }
        }

        // Clear previous route
        routeLayer.clearLayers();
        markers.clearLayers();

        // Add markers
        L.marker([start.lat, start.lng]).addTo(markers).bindPopup('Start');
        L.marker([end.lat, end.lng]).addTo(markers).bindPopup('End');

        // Draw a simple straight line (for demo, real route would use OSRM or Google Directions)
        var latlngs = [
            [start.lat, start.lng],
            [end.lat, end.lng]
        ];
        var polyline = L.polyline(latlngs, { color: '#1a73e8', weight: 4 }).addTo(routeLayer);

        // Fit map to route
        map.fitBounds(polyline.getBounds(), { padding: [50, 50] });

        // Calculate distance (simple Euclidean)
        var dist = getDistance(start.lat, start.lng, end.lat, end.lng);
        document.getElementById('routeInfo').innerHTML = `
            <div class="alert alert-success">
                <strong>Route Summary</strong><br>
                Start: (${start.lat}, ${start.lng})<br>
                End: (${end.lat}, ${end.lng})<br>
                Estimated Distance: ${dist} km<br>
                <small class="text-muted">Note: This is a straight-line estimate. Actual road distance may vary.</small>
            </div>
        `;
    });

    function getDistance(lat1, lon1, lat2, lon2) {
        var R = 6371;
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLon = (lon2 - lon1) * Math.PI / 180;
        var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return (R * c).toFixed(1);
    }
</script>
<?php include '../includes/footer.php'; ?>