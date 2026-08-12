<?php
// traveler/travel-history.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireRole('traveler');
$user_id = $_SESSION['user_id'];

$history = $pdo->prepare("SELECT * FROM travel_history WHERE user_id = ? ORDER BY start_date DESC");
$history->execute([$user_id]);
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <header class="top-nav"><div><button class="toggle-sidebar" id="toggleSidebarBtn"><i class="fas fa-bars"></i></button><span class="fw-semibold">Travel History</span></div></header>
    <div class="page-content">
        <div class="card-custom">
            <div class="card-header">My Trips</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0" id="historyTable">
                    <thead><tr><th>#</th><th>Destination</th><th>Start Date</th><th>End Date</th><th>Purpose</th><th>Notes</th></tr></thead>
                    <tbody>
                    <?php $i = 1; while($row = $history->fetch()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><strong><?= htmlspecialchars($row['destination']) ?></strong></td>
                        <td><?= date('d M Y', strtotime($row['start_date'])) ?></td>
                        <td><?= $row['end_date'] ? date('d M Y', strtotime($row['end_date'])) : '—' ?></td>
                        <td><?= htmlspecialchars($row['purpose']) ?></td>
                        <td><?= htmlspecialchars($row['notes']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if($history->rowCount() == 0): ?>
                    <tr><td colspan="6" class="text-center text-muted py-3">No trips planned yet. <a href="plan-trip.php">Plan a trip</a></td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() { $('#historyTable').DataTable({ pageLength: 10 }); });
</script>
<?php include '../includes/footer.php'; ?>