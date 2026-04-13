<?php
// dashboard/customer/profile.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../includes/layout.php';
requireLogin('customer');

$user = currentUser();
$userModel = new User();
$profile = $userModel->findById($user['id']);
$msg = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] ===  'POST') {
    verifyCsrf();
    $data = [
        'name' =>  trim($_POST['name'] ?? ''),
        'phone' =>  trim($_POST['phone'] ?? ''),
        'city' =>  trim($_POST['city'] ?? ''),
    ];

    // Handle profile picture upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $file = $_FILES['profile_pic'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png'])) {
            $error = 'Only JPG/PNG files allowed.';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $error = 'Profile picture must be under 2 MB.';
        } else {
            $uploadDir = __DIR__ . '/../../assets/uploads/profiles/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $filename = 'profile_' . $user['id'] . '_' . time() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], $uploadDir . $filename);
            $data['profile_pic'] = 'assets/uploads/profiles/' . $filename;
        }
    }

    if (!$error) {
        $userModel->update($user['id'], $data);
        $_SESSION['name'] = $data['name'];
        $profile = $userModel->findById($user['id']); // refresh
        $msg = 'Profile updated successfully!';
    }
}

layoutHead('My Profile');
layoutNavbar('customer', $user['name']);
layoutSidebar('customer', 'Profile');
?>

<div class = "section-header">
  <div><div class = "section-title">My Profile</div></div>
</div>

<?php if ($msg): ?><div class = "alert-sportify alert-success-custom mb-3"><span class = "material-icons">check_circle</span> <? = h($msg) ?></div><?php endif; ?>
<?php if ($error): ?><div class = "alert-sportify alert-danger-custom mb-3"><span class = "material-icons">error</span> <? = h($error) ?></div><?php endif; ?>

<div class = "row justify-content-center">
  <div class = "col-lg-7">
    <div class = "card-sportify p-4">
      <form method = "POST" enctype = "multipart/form-data">
        <? = csrfInput() ?>
        <div class = "text-center mb-4">
          <?php if ($profile['profile_pic']): ?>
            <img src = "<? = BASE_URL ?>/<? = h($profile['profile_pic']) ?>" class = "profile-avatar mb-2" alt = "Profile picture">
          <?php else: ?>
            <div class = "profile-avatar-placeholder mb-2 mx-auto"><span class = "material-icons" style = "font-size:2.5rem">person</span></div>
          <?php endif; ?>
          <div>
            <label for = "profile_pic" class = "btn-outline-sportify" style = "cursor:pointer;font-size:.82rem;display:inline-block">
              <span class = "material-icons" style = "font-size:.95rem;vertical-align:middle">upload</span> Change Photo
            </label>
            <input type = "file" id = "profile_pic" name = "profile_pic" accept = ".jpg,.jpeg,.png" style = "display:none">
          </div>
          <small class = "text-muted d-block mt-1">JPG or PNG, max 2 MB</small>
        </div>

        <div class = "mb-3">
          <label class = "form-label fw-500" for = "name">Full Name</label>
          <input type = "text" id = "name" name = "name" class = "form-control" value = "<? = h($profile['name']) ?>" required>
        </div>
        <div class = "mb-3">
          <label class = "form-label fw-500" for = "emailDisplay">Email Address</label>
          <input type = "email" id = "emailDisplay" class = "form-control" value = "<? = h($profile['email']) ?>" disabled>
          <small class = "text-muted">Email cannot be changed</small>
        </div>
        <div class = "mb-3">
          <label class = "form-label fw-500" for = "phone">Phone Number</label>
          <input type = "tel" id = "phone" name = "phone" class = "form-control" value = "<? = h($profile['phone'] ?? '') ?>" placeholder = "+91 98765 43210">
        </div>
        <div class = "mb-4">
          <label class = "form-label fw-500" for = "city">City</label>
          <input type = "text" id = "city" name = "city" class = "form-control" value = "<? = h($profile['city'] ?? '') ?>" placeholder = "Mumbai">
        </div>
        <button type = "submit" class = "btn-sportify">
          <span class = "material-icons">save</span> Save Changes
        </button>
      </form>
    </div>
  </div>
</div>

<?php layoutFooter(); ?>







