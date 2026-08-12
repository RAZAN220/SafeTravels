<?php
// admin/users.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireRole('admin');

$users = $pdo->query("SELECT id, fullname, email, phone, role, is_verified, created_at FROM users WHERE role='traveler' ORDER BY created_at DESC")->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <header class="top-nav"><div><button class="toggle-sidebar" id="toggleSidebarBtn"><i class="fas fa-bars"></i></button><span class="fw-semibold">Manage Travelers</span></div></header>
    <div class="page-content">
        <div class="card-custom">
            <div class="card-header">All Travelers</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0" id="usersTable">
                    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Verified</th><th>Joined</th></tr></thead>
                    <tbody>
                    <?php foreach($users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= $u['fullname'] ?></td>
                        <td><?= $u['email'] ?></td>
                        <td><?= $u['phone'] ?></td>
                        <td><?= $u['is_verified'] ? '✅' : '❌' ?></td>
                        <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($users)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-3">No travelers registered.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() { $('#usersTable').DataTable({ pageLength: 10 }); });
</script>
<?php include '../includes/footer.php'; ?>