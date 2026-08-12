<?php
// traveler/plan-trip.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireRole('traveler');
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destination = $_POST['destination'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $purpose = $_POST['purpose'];
    $notes = $_POST['notes'];

    $stmt = $pdo->prepare("INSERT INTO travel_history (user_id, destination, start_date, end_date, purpose, notes) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$user_id, $destination, $start_date, $end_date, $purpose, $notes]);

    // Send notification
    $notif = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message) VALUES (?, 'info', 'Trip Planned', ?)");
    $notif->execute([$user_id, "Your trip to $destination is planned from $start_date to $end_date."]);

    echo "<script>Swal.fire('Trip Planned!','Your trip has been saved.','success').then(()=>window.location='travel-history.php');</script>";
}

$destinations = ['Kandy', 'Galle', 'Colombo', 'Nuwara Eliya', 'Ella', 'Sigiriya', 'Trincomalee', 'Jaffna', 'Anuradhapura', 'Polonnaruwa'];
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <header class="top-nav"><div><button class="toggle-sidebar" id="toggleSidebarBtn"><i class="fas fa-bars"></i></button><span class="fw-semibold">Plan a Trip</span></div></header>
    <div class="page-content">
        <div class="row">
            <div class="col-lg-8">
                <div class="card-custom">
                    <div class="card-header">Plan Your Next Trip</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Destination</label>
                                    <input list="destinations" name="destination" class="form-control" placeholder="Enter destination" required>
                                    <datalist id="destinations">
                                        <?php foreach($destinations as $d): ?>
                                            <option value="<?= $d ?>"><?= $d ?></option>
                                        <?php endforeach; ?>
                                    </datalist>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Purpose</label>
                                    <input type="text" name="purpose" class="form-control" placeholder="e.g., Vacation, Business">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input type="date" name="end_date" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Any special requirements or notes..."></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Save Trip Plan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-custom">
                    <div class="card-header">💡 Travel Tips</div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check-circle text-success me-2"></i> Check safety alerts for your destination</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Save emergency contacts</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Share your itinerary with someone</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Keep emergency cash and documents</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Download offline maps</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>