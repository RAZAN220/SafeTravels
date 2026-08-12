<?php
// admin/incidents.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireRole('admin');

// Update incident status
if (isset($_GET['status']) && isset($_GET['id'])) {
    $status = $_GET['status'];
    $id = $_GET['id'];
    $pdo->prepare("UPDATE incidents SET status = ? WHERE id = ?")->execute([$status, $id]);
    header('Location: incidents.php');
    exit;
}

$incidents = $pdo->query("SELECT i.*, u.fullname as reporter FROM incidents i JOIN users u ON i.user_id = u.id ORDER BY i.created_at DESC")->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <header class="top-nav"><div><button class="toggle-sidebar" id="toggleSidebarBtn"><i class="fas fa-bars"></i></button><span class="fw-semibold">Manage Incidents</span></div></header>
    <div class="page-content">
        <div class="card-custom">
            <div class="card-header">Reported Incidents</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0" id="incidentsTable">
                    <thead><tr><th>ID</th><th>Title</th><th>Reporter</th><th>Type</th><th>Severity</th><th>Status</th><th>Location</th><th>Date</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach($incidents as $inc): ?>
                    <tr>
                        <td><?= $inc['id'] ?></td>
                        <td><?= htmlspecialchars($inc['title']) ?></td>
                        <td><?= $inc['reporter'] ?></td>
                        <td><?= ucfirst($inc['type']) ?></td>
                        <td><?= severityBadge($inc['severity']) ?></td>
                        <td><?= statusBadge($inc['status']) ?></td>
                        <td><?= htmlspecialchars($inc['location_name']) ?></td>
                        <td><?= date('d M Y', strtotime($inc['created_at'])) ?></td>
                        <td>
                            <?php if($inc['status'] == 'pending'): ?>
                                <a href="?id=<?= $inc['id'] ?>&status=verified" class="btn btn-sm btn-success">Verify</a>
                                <a href="?id=<?= $inc['id'] ?>&status=dismissed" class="btn btn-sm btn-danger">Dismiss</a>
                            <?php elseif($inc['status'] == 'verified'): ?>
                                <a href="?id=<?= $inc['id'] ?>&status=resolved" class="btn btn-sm btn-primary">Resolve</a>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?= ucfirst($inc['status']) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($incidents)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-3">No incidents reported.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() { $('#incidentsTable').DataTable({ pageLength: 10 }); });
</script>
<?php include '../includes/footer.php'; ?>