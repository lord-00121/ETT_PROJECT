<?php
// dashboard/seller/add_tournament.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/Tournament.php';
require_once __DIR__ . '/../../includes/layout.php';
requireLogin('seller');

$user = currentUser();
$tournamentModel = new Tournament();
$error = '';
$sports = ['Cricket','Football','Badminton','Basketball','Tennis','Swimming','Others'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $data = [
        'seller_id' => $user['id'],
        'name' => trim($_POST['name'] ?? ''),
        'sport_type' => $_POST['sport_type'] ?? '',
        'location' => trim($_POST['location'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'start_date' => $_POST['start_date'] ?? '',
        'end_date' => $_POST['end_date'] ?? '',
        'registration_deadline' => !empty($_POST['registration_deadline']) ? $_POST['registration_deadline'] : null,
    ];
    
    if (!$data['name'] || !$data['sport_type'] || !$data['location'] || !$data['start_date'] || !$data['end_date']) {
        $error = 'Please fill all required fields.';
    } else {
        $tId = $tournamentModel->create($data);
        if (!empty($_FILES['photos']['name'][0])) {
            $uploadDir = __DIR__ . '/../../assets/uploads/venues/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $files = $_FILES['photos'];
            for ($i = 0; $i < min(count($files['name']), 10); $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
                $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png'])) continue;
                if ($files['size'][$i] > 5 * 1024 * 1024) continue;
                $fname = 'tournament_' . $tId . '_' . time() . '_' . $i . '.' . $ext;
                move_uploaded_file($files['tmp_name'][$i], $uploadDir . $fname);
                $tournamentModel->addPhoto($tId, 'assets/uploads/venues/' . $fname, $i);
            }
        }
        header('Location: ' . BASE_URL . '/dashboard/seller/tournaments.php?msg=saved');
        exit;
    }
}

layoutHead('Add Tournament');
layoutNavbar('seller', $user['name']);
layoutSidebar('seller', 'Tournaments');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-700 m-0">Host a Tournament</h4>
    <p class="text-muted small">Fill in the event details to start accepting registrations</p>
  </div>
  <a href="<?php echo BASE_URL; ?>/dashboard/seller/tournaments.php" class="btn btn-light shadow-0">← Back</a>
</div>

<?php if ($error): ?><div class="alert alert-danger py-2 small fw-600"><span class="material-icons align-middle fs-6 me-1">error</span> <?php echo h($error); ?></div><?php endif; ?>

<div class="card p-4 shadow-sm border-0">
  <form method="POST" enctype="multipart/form-data">
    <?php echo csrfInput(); ?>
    <div class="row g-3">
      <div class="col-md-8">
        <label class="form-label fw-600 small">Tournament Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" placeholder="e.g. Summer Cricket League 2026" required>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-600 small">Sport Type <span class="text-danger">*</span></label>
        <select name="sport_type" class="form-select bg-light" required>
          <option value="">Select sport...</option>
          <?php foreach ($sports as $s): ?>
             <option value="<?php echo h($s); ?>"><?php echo h($s); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12">
        <label class="form-label fw-600 small">Location <span class="text-danger">*</span></label>
        <input type="text" name="location" class="form-control" placeholder="Venue address or complex name" required>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-600 small">Start Date <span class="text-danger">*</span></label>
        <input type="date" name="start_date" class="form-control bg-light" required>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-600 small">End Date <span class="text-danger">*</span></label>
        <input type="date" name="end_date" class="form-control bg-light" required>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-600 small">Registration Deadline</label>
        <input type="date" name="registration_deadline" class="form-control bg-light">
      </div>
      <div class="col-12">
        <label class="form-label fw-600 small">Description & Rules</label>
        <textarea name="description" class="form-control bg-light" rows="4" placeholder="Describe the tournament structure, prizes, and eligibility requirements..."></textarea>
      </div>
      <div class="col-12 border-top pt-3 mt-3">
        <label class="form-label fw-600 small">Promotional Photos (optional, max 5 MB each) <span class="text-muted fw-normal">(Hold Ctrl/Cmd to select multiple)</span></label>
        <input type="file" id="tphotos" name="photos[]" class="form-control" multiple="multiple" accept=".jpg,.jpeg,.png">
        <div class="d-flex flex-wrap gap-2 mt-3" id="photoPreview"></div>
      </div>
    </div>
    
    <div class="d-flex gap-3 mt-4 pt-3 border-top">
      <button type="submit" class="btn btn-primary px-4 fw-600 shadow-0">
        <span class="material-icons align-middle fs-6 me-1">save</span> Save Tournament
      </button>
      <a href="<?php echo BASE_URL; ?>/dashboard/seller/tournaments.php" class="btn btn-light shadow-0 fw-600">Cancel</a>
    </div>
  </form>
</div>

<script>
document.getElementById('tphotos').addEventListener('change', function() {
  const preview = document.getElementById('photoPreview');
  preview.innerHTML = '';
  [...this.files].slice(0,10).forEach(file => {
    const reader = new FileReader();
    reader.onload = e => {
      const img = document.createElement('img');
      img.src = e.target.result;
      img.style.cssText = 'width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;';
      preview.appendChild(img);
    };
    reader.readAsDataURL(file);
  });
});
</script>

<?php layoutFooter(); ?>
