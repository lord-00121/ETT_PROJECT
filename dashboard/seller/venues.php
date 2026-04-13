<?php
// dashboard/seller/venues.php — Manage venues
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/Venue.php';
require_once __DIR__ . '/../../includes/layout.php';
requireLogin('seller');

$user = currentUser();
$venueModel = new Venue();

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_venue'])) {
    verifyCsrf();
    $venueModel->adminDismiss((int)$_POST['venue_id']); // Using a soft delete
    header('Location: ' . BASE_URL . '/dashboard/seller/venues.php?msg=deleted');
    exit;
}

$venues = $venueModel->getBySeller($user['id']);
$msg = $_GET['msg'] ?? '';

layoutHead('My Venues');
layoutNavbar('seller', $user['name']);
layoutSidebar('seller', 'My Venues');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-700 m-0">My Venues</h4>
    <p class="text-muted small"><?php echo count($venues); ?> venues listed</p>
  </div>
  <a href="add_venue.php" class="btn btn-primary shadow-0">
    <span class="material-icons align-middle fs-6 me-1">add</span> Add Venue
  </a>
</div>

<?php if ($msg === 'deleted'): ?><div class="alert alert-danger py-2 small">Venue removed successfully.</div><?php endif; ?>

<?php if (empty($venues)): ?>
  <div class="card p-5 text-center">
    <span class="material-icons text-muted mb-3" style="font-size:3rem">add_location_alt</span>
    <h5>No venues yet</h5>
    <p class="text-muted">Start listing your sports facility to attract customers.</p>
    <div><a href="add_venue.php" class="btn btn-primary px-4">Add First Venue</a></div>
  </div>
<?php else: ?>
<div class="card p-0 overflow-hidden">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="bg-light">
        <tr>
          <th class="ps-4">Venue Name</th>
          <th>Sport</th>
          <th>Location</th>
          <th>Price/Slot</th>
          <th>Rating</th>
          <th>Status</th>
          <th class="text-end pe-4">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($venues as $v): ?>
        <tr>
          <td class="ps-4 fw-600"><?php echo h($v['name']); ?></td>
          <td><span class="badge rounded-pill bg-info text-dark px-3"><?php echo h($v['sport_type']); ?></span></td>
          <td class="small text-muted"><?php echo h($v['location']); ?></td>
          <td class="fw-600">₹ <?php echo number_format($v['price_per_slot']); ?></td>
          <td><span class="material-icons text-warning fs-6 align-middle">star</span> <?php echo number_format($v['rating_avg'], 1); ?></td>
          <td>
            <div class="form-check form-switch">
              <input class="form-check-input venue-toggle" type="checkbox" data-id="<?php echo $v['id']; ?>" <?php echo $v['is_active'] ? 'checked' : ''; ?>>
            </div>
          </td>
          <td class="text-end pe-4">
            <a href="edit_venue.php?id=<?php echo $v['id']; ?>" class="btn btn-light btn-floating btn-sm me-1 shadow-0" title="Edit"><span class="material-icons fs-6">edit</span></a>
            <form method="POST" class="d-inline" onsubmit="return confirm('Delete this venue?')">
              <?php echo csrfInput(); ?>
              <input type="hidden" name="delete_venue" value="1">
              <input type="hidden" name="venue_id" value="<?php echo $v['id']; ?>">
              <button type="submit" class="btn btn-light btn-floating btn-sm text-danger shadow-0" title="Delete"><span class="material-icons fs-6">delete</span></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<script>
document.querySelectorAll('.venue-toggle').forEach(t => {
  t.addEventListener('change', async function() {
    const active = this.checked ? 1 : 0;
    try {
        const res = await fetch('<?php echo BASE_URL; ?>/api/toggle_venue.php', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'<?php echo csrfToken(); ?>'},
            body: JSON.stringify({id: this.dataset.id, active: active})
        });
    } catch(e) { console.error(e); }
  });
});
</script>

<?php layoutFooter(); ?>
