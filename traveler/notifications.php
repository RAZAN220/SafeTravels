<?php
// traveler/notifications.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireRole('traveler');
$user_id = $_SESSION['user_id'];

if (isset($_GET['mark_all'])) {
    $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")->execute([$user_id]);
    header('Location: notifications.php');
    exit;
}
if (isset($_GET['read'])) {
    $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?")->execute([$_GET['read'], $user_id]);
    header('Location: notifications.php');
    exit;
}

$notifications = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$notifications->execute([$user_id]);
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <header class="top-nav"><div><button class="toggle-sidebar" id="toggleSidebarBtn"><i class="fas fa-bars"></i></button><span class="fw-semibold">Notifications</span></div></header>
    <div class="page-content">
        <div class="d-flex justify-content-between mb-3">
            <h5>All Notifications</h5>
            <a href="?mark_all=1" class="btn btn-sm btn-outline-primary">Mark All as Read</a>
        </div>
        <?php if($notifications->rowCount() == 0): ?>
            <div class="alert alert-info">No notifications.</div>
        <?php else: ?>
            <div class="list-group">
                <?php while($row = $notifications->fetch()): ?>
                    <a href="<?= $row['link'] ?? '#' ?>" class="list-group-item list-group-item-action <?= $row['is_read'] ? '' : 'bg-light' ?>">
                        <div class="d-flex justify-content-between">
                            <div>
                                <span class="badge <?= $row['type'] == 'alert' ? 'bg-danger' : ($row['type'] == 'match' ? 'bg-success' : 'bg-primary') ?> me-2"><?= ucfirst($row['type']) ?></span>
                                <strong><?= $row['title'] ?></strong>
                            </div>
                            <small class="text-muted"><?= date('d M Y H:i', strtotime($row['created_at'])) ?></small>
                        </div>
                        <p class="mb-0 mt-1"><?= $row['message'] ?></p>
                        <?php if(!$row['is_read']): ?>
                            <a href="?read=<?= $row['id'] ?>" class="btn btn-sm btn-outline-secondary mt-1">Mark as Read</a>
                        <?php endif; ?>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>