<?php
// dashboard/seller/profile.php — Seller Profile
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../includes/layout.php';
requireLogin('seller');

$user = currentUser();
$userModel = new User();
$profile = $userModel->findById($user['id']);
$msg = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $data = [
        'name' => trim($_POST['name'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'city' => trim($_POST['city'] ?? ''),
        'business_name' => trim($_POST['business_name'] ?? ''),
    ];
    if (!empty($_FILES['profile_pic']['name'])) {
        $file = $_FILES['profile_pic'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png'])) { $error = 'Only JPG/PNG allowed.'; }
        elseif ($file['size'] > 2*1024*1024) { $error = 'Max 2 MB.'; }
        else {
            $uploadDir = __DIR__ . '/../../assets/uploads/profiles/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $fname = 'seller_' . $user['id'] . '_' . time() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], $uploadDir . $fname);
            $data['profile_pic'] = 'assets/uploads/profiles/' . $fname;
        }
    }
    if (!$error) {
        $userModel->update($user['id'], $data);
        $_SESSION['name'] = $data['name'];
        header('Location: ' . BASE_URL . '/dashboard/seller/profile.php?msg=updated');
        exit;
    }
}

if (isset($_GET['msg'])) $msg = 'Profile updated successfully!';

layoutHead('Profile');
layoutNavbar('seller', $user['name']);
layoutSidebar('seller', 'Profile');
?>
<div class="card p-4 mx-auto" style="max-width: 600px;">
    <h4 class="fw-700 mb-4 text-center">Seller Profile</h4>
    <?php if ($msg): ?><div class="alert alert-success py-2 small"><?php echo h($msg); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger py-2 small"><?php echo h($error); ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <?php echo csrfInput(); ?>
        <div class="mb-4 text-center">
            <div class="mb-3">
                <?php if ($profile['profile_pic']): ?>
                    <img src="<?php echo BASE_URL . '/' . h($profile['profile_pic']); ?>" class="rounded-circle border p-1" style="width:100px; height:100px; object-fit:cover;">
                <?php else: ?>
                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center mx-auto" style="width:100px; height:100px;">
                        <span class="material-icons text-muted" style="font-size:3rem">store</span>
                    </div>
                <?php endif; ?>
            </div>
            <label class="btn btn-light btn-sm shadow-0">
                <span class="material-icons align-middle fs-6 me-1">upload</span> Change Logo
                <input type="file" name="profile_pic" class="d-none">
            </label>
        </div>
        
        <div class="mb-3">
            <label class="form-label small fw-600">Business / Complex Name</label>
            <input type="text" name="business_name" class="form-control" value="<?php echo h($profile['business_name'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label small fw-600">Owner Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo h($profile['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label small fw-600">Email (Read-only)</label>
            <input type="email" class="form-control bg-light" value="<?php echo h($profile['email']); ?>" disabled>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label small fw-600">Phone</label>
                <input type="tel" name="phone" class="form-control" value="<?php echo h($profile['phone'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label small fw-600">City</label>
                <input type="text" name="city" class="form-control" value="<?php echo h($profile['city'] ?? ''); ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-3 py-2 fw-600">Save Profile</button>
    </form>
</div>
<?php layoutFooter(); ?>
