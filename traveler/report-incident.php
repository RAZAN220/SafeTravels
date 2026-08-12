<?php
// traveler/report-incident.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireRole('traveler');
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $severity = $_POST['severity'];
    $location_name = $_POST['location_name'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];

    $stmt = $pdo->prepare("INSERT INTO incidents (user_id, title, description, type, severity, location_name, location_lat, location_lng) 
                           VALUES (?,?,?,?,?,?,?,?)");
    $stmt->execute([$user_id, $title, $description, $type, $severity, $location_name, $lat, $lng]);

    // Notify admin (optional: create notification for admin users)
    echo "<script>Swal.fire('Reported','Your incident has been reported. Admin will review it.','success').then(()=>window.location='travel-history.php');</script>";
}

$types = ['accident','theft','harassment','medical','other'];
$severities = ['low','medium','high','critical'];
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <header class="top-nav"><div><button class="toggle-sidebar" id="toggleSidebarBtn"><i class="fas fa-bars"></i></button><span class="fw-semibold">Report Incident</span></div></header>
    <div class="page-content">
        <div class="card-custom">
            <div class="card-header">Report a Safety Incident</div>
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Incident Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="">Select type</option>
                                <?php foreach($types as $t): ?>
                                    <option value="<?= $t ?>"><?= ucfirst($t) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Severity</label>
                            <select name="severity" class="form-select" required>
                                <option value="medium">Medium</option>
                                <?php foreach($severities as $s): ?>
                                    <option value="<?= $s ?>"><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Location Name</label>
                            <input type="text" name="location_name" class="form-control" placeholder="e.g., Kandy City Center">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Latitude</label>
                            <input type="number" step="any" name="lat" class="form-control" placeholder="7.2906" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Longitude</label>
                            <input type="number" step="any" name="lng" class="form-control" placeholder="80.6337" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit Report</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="getLocation()"><i class="fas fa-location-dot me-2"></i>Get My Location</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(pos) {
                document.querySelector('input[name="lat"]').value = pos.coords.latitude;
                document.querySelector('input[name="lng"]').value = pos.coords.longitude;
                Swal.fire('Location captured', 'Latitude and longitude filled automatically.', 'success');
            }, function() {
                Swal.fire('Error', 'Unable to get location. Please enter manually.', 'error');
            });
        } else {
            Swal.fire('Error', 'Geolocation not supported.', 'error');
        }
    }
</script>
<?php include '../includes/footer.php'; ?>