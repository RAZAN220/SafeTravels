<?php
// traveler/profile.php
require_once '../config/database.php';
require_once '../config/auth.php';
requireRole('traveler');
$user_id = $_SESSION['user_id'];

// Fetch user and profile data
$user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$user->execute([$user_id]);
$user = $user->fetch();

$profile = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
$profile->execute([$user_id]);
$profile = $profile->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update user
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $stmt = $pdo->prepare("UPDATE users SET fullname = ?, phone = ? WHERE id = ?");
    $stmt->execute([$fullname, $phone, $user_id]);
    $_SESSION['fullname'] = $fullname;

    // Update profile
    $ec_name = $_POST['emergency_contact_name'];
    $ec_phone = $_POST['emergency_contact_phone'];
    $blood_group = $_POST['blood_group'];
    $allergies = $_POST['allergies'];
    $medical_conditions = $_POST['medical_conditions'];
    $preferred_language = $_POST['preferred_language'];

    $stmt = $pdo->prepare("UPDATE profiles SET 
                           emergency_contact_name = ?, 
                           emergency_contact_phone = ?, 
                           blood_group = ?, 
                           allergies = ?, 
                           medical_conditions = ?, 
                           preferred_language = ? 
                           WHERE user_id = ?");
    $stmt->execute([$ec_name, $ec_phone, $blood_group, $allergies, $medical_conditions, $preferred_language, $user_id]);

    echo "<script>Swal.fire('Updated','Profile updated successfully!','success')</script>";
    // Refresh data
    $profile = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
    $profile->execute([$user_id]);
    $profile = $profile->fetch();
}
?>
<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <header class="top-nav"><div><button class="toggle-sidebar" id="toggleSidebarBtn"><i class="fas fa-bars"></i></button><span class="fw-semibold">My Profile</span></div></header>
    <div class="page-content">
        <div class="card-custom">
            <div class="card-header">Edit Profile</div>
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email (cannot change)</label>
                            <input type="email" class="form-control" value="<?= $user['email'] ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= $user['phone'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Preferred Language</label>
                            <select name="preferred_language" class="form-select">
                                <option value="English" <?= ($profile['preferred_language']??'')=='English'?'selected':'' ?>>English</option>
                                <option value="Sinhala" <?= ($profile['preferred_language']??'')=='Sinhala'?'selected':'' ?>>Sinhala</option>
                                <option value="Tamil" <?= ($profile['preferred_language']??'')=='Tamil'?'selected':'' ?>>Tamil</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name" class="form-control" value="<?= htmlspecialchars($profile['emergency_contact_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Emergency Contact Phone</label>
                            <input type="text" name="emergency_contact_phone" class="form-control" value="<?= htmlspecialchars($profile['emergency_contact_phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Blood Group</label>
                            <input type="text" name="blood_group" class="form-control" placeholder="e.g., O+" value="<?= htmlspecialchars($profile['blood_group'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Allergies</label>
                            <textarea name="allergies" class="form-control" rows="2"><?= htmlspecialchars($profile['allergies'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Medical Conditions</label>
                            <textarea name="medical_conditions" class="form-control" rows="2"><?= htmlspecialchars($profile['medical_conditions'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>