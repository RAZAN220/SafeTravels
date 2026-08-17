<?php
// admin/incidents.php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../config/functions.php';
requireRole('admin');

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Update incident status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }
    
    $status = $_POST['status'] ?? '';
    $id = $_POST['id'] ?? '';
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
                        <td><?= htmlspecialchars($inc['reporter']) ?></td>
                        <td><?= ucfirst($inc['type']) ?></td>
                        <td><?= severityBadge($inc['severity']) ?></td>
                        <td><?= statusBadge($inc['status']) ?></td>
                        <td><?= htmlspecialchars($inc['location_name']) ?></td>
                        <td><?= date('d M Y', strtotime($inc['created_at'])) ?></td>
                        <td>
                            <?php if($inc['status'] == 'pending'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="id" value="<?= $inc['id'] ?>">
                                    <input type="hidden" name="status" value="verified">
                                    <input type="hidden" name="update_status" value="1">
                                    <button type="submit" class="btn btn-sm btn-success">Verify</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="id" value="<?= $inc['id'] ?>">
                                    <input type="hidden" name="status" value="dismissed">
                                    <input type="hidden" name="update_status" value="1">
                                    <button type="submit" class="btn btn-sm btn-danger">Dismiss</button>
                                </form>
                            <?php elseif($inc['status'] == 'verified'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="id" value="<?= $inc['id'] ?>">
                                    <input type="hidden" name="status" value="resolved">
                                    <input type="hidden" name="update_status" value="1">
                                    <button type="submit" class="btn btn-sm btn-primary">Resolve</button>
                                </form>
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