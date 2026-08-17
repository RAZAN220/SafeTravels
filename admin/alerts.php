<?php
// admin/alerts.php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../config/functions.php';
requireRole('admin');

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }
    
    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare("INSERT INTO safety_alerts (title, description, location_lat, location_lng, location_name, severity, category, expires_at, created_by) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$_POST['title'], $_POST['description'], $_POST['lat'], $_POST['lng'], $_POST['location_name'], $_POST['severity'], $_POST['category'], $_POST['expires_at'], $_SESSION['user_id']]);
        echo "<script>Swal.fire('Added','Alert posted','success')</script>";
    } elseif (isset($_POST['edit'])) {
        $stmt = $pdo->prepare("UPDATE safety_alerts SET title=?, description=?, location_lat=?, location_lng=?, location_name=?, severity=?, category=?, expires_at=?, is_active=? WHERE id=?");
        $stmt->execute([$_POST['title'], $_POST['description'], $_POST['lat'], $_POST['lng'], $_POST['location_name'], $_POST['severity'], $_POST['category'], $_POST['expires_at'], $_POST['is_active'], $_POST['id']]);
        echo "<script>Swal.fire('Updated','Alert updated','success')</script>";
    } elseif (isset($_POST['delete'])) {
        $pdo->prepare("DELETE FROM safety_alerts WHERE id=?")->execute([$_POST['delete']]);
        header('Location: alerts.php');
        exit;
    }
}

$alerts = $pdo->query("SELECT * FROM safety_alerts ORDER BY created_at DESC")->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <header class="top-nav"><div><button class="toggle-sidebar" id="toggleSidebarBtn"><i class="fas fa-bars"></i></button><span class="fw-semibold">Safety Alerts</span></div></header>
    <div class="page-content">
        <div class="card-custom mb-4">
            <div class="card-header">Post New Alert</div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="col-md-6"><input type="text" name="title" class="form-control" placeholder="Alert Title" required maxlength="255"></div>
                    <div class="col-md-6"><input type="text" name="location_name" class="form-control" placeholder="Location Name" maxlength="255"></div>
                    <div class="col-md-4"><input type="number" step="any" name="lat" class="form-control" placeholder="Latitude" required></div>
                    <div class="col-md-4"><input type="number" step="any" name="lng" class="form-control" placeholder="Longitude" required></div>
                    <div class="col-md-4">
                        <select name="severity" class="form-select">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="category" class="form-select">
                            <option value="weather">Weather</option>
                            <option value="crime">Crime</option>
                            <option value="accident">Accident</option>
                            <option value="road">Road</option>
                            <option value="health">Health</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-4"><input type="datetime-local" name="expires_at" class="form-control" placeholder="Expires at"></div>
                    <div class="col-12"><textarea name="description" class="form-control" rows="3" placeholder="Description" required></textarea></div>
                    <div class="col-12"><button type="submit" name="add" class="btn btn-primary">Post Alert</button></div>
                </form>
            </div>
        </div>

        <div class="card-custom">
            <div class="card-header">All Alerts</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0" id="alertsTable">
                    <thead><tr><th>ID</th><th>Title</th><th>Location</th><th>Severity</th><th>Expires</th><th>Active</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach($alerts as $a): ?>
                    <tr>
                        <td><?= $a['id'] ?></td>
                        <td><?= htmlspecialchars($a['title']) ?></td>
                        <td><?= htmlspecialchars($a['location_name']) ?></td>
                        <td><?= severityBadge($a['severity']) ?></td>
                        <td><?= $a['expires_at'] ? date('d M Y', strtotime($a['expires_at'])) : 'Never' ?></td>
                        <td><?= $a['is_active'] ? '✅' : '❌' ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $a['id'] ?>">Edit</button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete?');">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="delete" value="<?= $a['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $a['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header"><h5>Edit Alert</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                    <div class="modal-body">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                        <div class="mb-2"><input type="text" name="title" class="form-control" value="<?= htmlspecialchars($a['title']) ?>" required maxlength="255"></div>
                                        <div class="mb-2"><input type="text" name="location_name" class="form-control" value="<?= htmlspecialchars($a['location_name']) ?>" maxlength="255"></div>
                                        <div class="mb-2"><input type="number" step="any" name="lat" class="form-control" value="<?= $a['location_lat'] ?>" required></div>
                                        <div class="mb-2"><input type="number" step="any" name="lng" class="form-control" value="<?= $a['location_lng'] ?>" required></div>
                                        <div class="mb-2">
                                            <select name="severity" class="form-select">
                                                <option value="low" <?= $a['severity']=='low'?'selected':'' ?>>Low</option>
                                                <option value="medium" <?= $a['severity']=='medium'?'selected':'' ?>>Medium</option>
                                                <option value="high" <?= $a['severity']=='high'?'selected':'' ?>>High</option>
                                                <option value="critical" <?= $a['severity']=='critical'?'selected':'' ?>>Critical</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <select name="category" class="form-select">
                                                <option value="weather" <?= $a['category']=='weather'?'selected':'' ?>>Weather</option>
                                                <option value="crime" <?= $a['category']=='crime'?'selected':'' ?>>Crime</option>
                                                <option value="accident" <?= $a['category']=='accident'?'selected':'' ?>>Accident</option>
                                                <option value="road" <?= $a['category']=='road'?'selected':'' ?>>Road</option>
                                                <option value="health" <?= $a['category']=='health'?'selected':'' ?>>Health</option>
                                                <option value="other" <?= $a['category']=='other'?'selected':'' ?>>Other</option>
                                            </select>
                                        </div>
                                        <div class="mb-2"><input type="datetime-local" name="expires_at" class="form-control" value="<?= $a['expires_at'] ? date('Y-m-d\TH:i', strtotime($a['expires_at'])) : '' ?>"></div>
                                        <div class="mb-2"><textarea name="description" class="form-control" rows="2" required><?= htmlspecialchars($a['description']) ?></textarea></div>
                                        <div class="mb-2">
                                            <select name="is_active" class="form-select">
                                                <option value="1" <?= $a['is_active']?'selected':'' ?>>Active</option>
                                                <option value="0" <?= !$a['is_active']?'selected':'' ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer"><button type="submit" name="edit" class="btn btn-primary">Update</button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if(empty($alerts)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">No alerts.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() { $('#alertsTable').DataTable({ pageLength: 10 }); });
</script>
<?php include '../includes/footer.php'; ?>