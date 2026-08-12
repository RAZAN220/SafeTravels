<?php
// admin/places.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare("INSERT INTO places (name, category, address, location_lat, location_lng, phone, website, rating) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$_POST['name'], $_POST['category'], $_POST['address'], $_POST['lat'], $_POST['lng'], $_POST['phone'], $_POST['website'], $_POST['rating']]);
        echo "<script>Swal.fire('Added','Place added','success')</script>";
    } elseif (isset($_POST['edit'])) {
        $stmt = $pdo->prepare("UPDATE places SET name=?, category=?, address=?, location_lat=?, location_lng=?, phone=?, website=?, rating=?, is_active=? WHERE id=?");
        $stmt->execute([$_POST['name'], $_POST['category'], $_POST['address'], $_POST['lat'], $_POST['lng'], $_POST['phone'], $_POST['website'], $_POST['rating'], $_POST['is_active'], $_POST['id']]);
        echo "<script>Swal.fire('Updated','Place updated','success')</script>";
    } elseif (isset($_GET['delete'])) {
        $pdo->prepare("DELETE FROM places WHERE id=?")->execute([$_GET['delete']]);
        header('Location: places.php');
        exit;
    }
}

$places = $pdo->query("SELECT * FROM places ORDER BY name")->fetchAll();
$categories = ['hospital','police','hotel','restaurant','fuel','atm','pharmacy','tourist_attraction','other'];
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <header class="top-nav"><div><button class="toggle-sidebar" id="toggleSidebarBtn"><i class="fas fa-bars"></i></button><span class="fw-semibold">Manage Places</span></div></header>
    <div class="page-content">
        <div class="card-custom mb-4">
            <div class="card-header">Add New Place</div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-4"><input type="text" name="name" class="form-control" placeholder="Name" required></div>
                    <div class="col-md-3">
                        <select name="category" class="form-select">
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat ?>"><?= ucfirst($cat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5"><input type="text" name="address" class="form-control" placeholder="Address"></div>
                    <div class="col-md-3"><input type="number" step="any" name="lat" class="form-control" placeholder="Latitude" required></div>
                    <div class="col-md-3"><input type="number" step="any" name="lng" class="form-control" placeholder="Longitude" required></div>
                    <div class="col-md-3"><input type="text" name="phone" class="form-control" placeholder="Phone"></div>
                    <div class="col-md-3"><input type="text" name="website" class="form-control" placeholder="Website"></div>
                    <div class="col-md-3"><input type="number" step="0.1" name="rating" class="form-control" placeholder="Rating (0-5)" min="0" max="5"></div>
                    <div class="col-12"><button type="submit" name="add" class="btn btn-primary">Add Place</button></div>
                </form>
            </div>
        </div>

        <div class="card-custom">
            <div class="card-header">All Places</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0" id="placesTable">
                    <thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Address</th><th>Phone</th><th>Rating</th><th>Active</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach($places as $p): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= ucfirst($p['category']) ?></td>
                        <td><?= htmlspecialchars($p['address']) ?></td>
                        <td><?= $p['phone'] ?></td>
                        <td><?= $p['rating'] ? number_format($p['rating'],1) : '—' ?></td>
                        <td><?= $p['is_active'] ? '✅' : '❌' ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $p['id'] ?>">Edit</button>
                            <a href="?delete=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Delete</a>
                        </td>
                    </tr>
                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $p['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header"><h5>Edit Place</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <div class="mb-2"><input type="text" name="name" class="form-control" value="<?= $p['name'] ?>" required></div>
                                        <div class="mb-2">
                                            <select name="category" class="form-select">
                                                <?php foreach($categories as $cat): ?>
                                                    <option value="<?= $cat ?>" <?= $cat==$p['category']?'selected':'' ?>><?= ucfirst($cat) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-2"><input type="text" name="address" class="form-control" value="<?= $p['address'] ?>"></div>
                                        <div class="mb-2"><input type="number" step="any" name="lat" class="form-control" value="<?= $p['location_lat'] ?>" required></div>
                                        <div class="mb-2"><input type="number" step="any" name="lng" class="form-control" value="<?= $p['location_lng'] ?>" required></div>
                                        <div class="mb-2"><input type="text" name="phone" class="form-control" value="<?= $p['phone'] ?>"></div>
                                        <div class="mb-2"><input type="text" name="website" class="form-control" value="<?= $p['website'] ?>"></div>
                                        <div class="mb-2"><input type="number" step="0.1" name="rating" class="form-control" value="<?= $p['rating'] ?>" min="0" max="5"></div>
                                        <div class="mb-2">
                                            <select name="is_active" class="form-select">
                                                <option value="1" <?= $p['is_active']?'selected':'' ?>>Active</option>
                                                <option value="0" <?= !$p['is_active']?'selected':'' ?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer"><button type="submit" name="edit" class="btn btn-primary">Update</button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if(empty($places)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-3">No places.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() { $('#placesTable').DataTable({ pageLength: 10 }); });
</script>
<?php include '../includes/footer.php'; ?>