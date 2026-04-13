<?php
// dashboard/admin/venues.php — Admin Venue Moderation
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/Venue.php';
require_once __DIR__ . '/../../includes/layout.php';
requireLogin('admin');

$user = currentUser();
$venueModel = new Venue();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    if (isset($_POST['dismiss'])) $venueModel->adminDismiss((int)$_POST['venue_id']);
    header('Location: ' . BASE_URL . '/dashboard/admin/venues.php?msg=dismissed');
    exit;
}

$venues = $venueModel->getAll(); // sorted by rating_avg ASC
$msg = $_GET['msg'] ?? '';

layoutHead('Manage Venues');
layoutNavbar('admin', $user['name']);
layoutSidebar('admin', 'Venues');
?>

<div class="mb-4">
    <h3 class="fw-700 m-0">Venue Directory</h3>
    <p class="text-muted small">Monitor active venues and dismiss policy violations. Sorted by lowest ratings first.</p>
</div>

<?php if ($msg === 'dismissed'): ?>
    <div class="alert alert-success py-2 small fw-600"><span class="material-icons align-middle fs-6 me-1">check_circle</span> Venue successfully removed from platform.</div>
<?php endif; ?>

<div class="card shadow-sm border-0" style="border-radius:12px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-uppercase small text-muted">
                <tr>
                    <th class="ps-4 fw-600">Venue Name</th>
                    <th class="fw-600">Seller Details</th>
                    <th class="fw-600">Sport</th>
                    <th class="fw-600">Ratings</th>
                    <th class="fw-600">Status</th>
                    <th class="pe-4 text-end fw-600">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($venues)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-5"><span class="material-icons d-block mb-2 fs-2">stadium</span>No venues found on the platform.</td></tr>
                <?php else: foreach ($venues as $v):
                    $stars = str_repeat('★', round($v['rating_avg'])) . str_repeat('☆', 5 - round($v['rating_avg']));
                ?>
                    <tr>
                        <td class="ps-4">
                            <div class="fw-700 text-primary"><?php echo h($v['name']); ?></div>
                            <div class="small text-muted"><span class="material-icons text-primary" style="font-size:0.75rem">location_on</span> <?php echo h($v['location']); ?></div>
                        </td>
                        <td>
                            <div class="fw-600 small"><?php echo h($v['seller_name']); ?></div>
                            <div class="text-muted" style="font-size: 0.70rem;">Joined <?php echo date('M Y', strtotime($v['created_at'])); ?></div>
                        </td>
                        <td>
                            <span class="badge border border-info text-info bg-light rounded-pill px-3"><?php echo h($v['sport_type']); ?></span>
                        </td>
                        <td>
                            <div class="text-warning small" style="letter-spacing:1px;"><?php echo $stars; ?></div>
                            <div class="text-muted" style="font-size:0.7rem;"><?php echo number_format($v['rating_avg'], 1); ?> Avg</div>
                        </td>
                        <td>
                            <?php if ($v['is_active']): ?>
                                <span class="badge bg-success-light text-success fw-600 rounded-pill px-3">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger-light text-danger fw-600 rounded-pill px-3">Dismissed</span>
                            <?php endif; ?>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?php echo BASE_URL; ?>/dashboard/customer/venue_detail.php?id=<?php echo $v['id']; ?>" class="btn btn-sm btn-light shadow-0" target="_blank" title="Preview as Customer">
                                    <span class="material-icons text-primary align-middle" style="font-size:1.1rem;">visibility</span>
                                </a>
                                <form method="POST" onsubmit="return confirm('WARNING: Dismissing this venue will hide it from the platform. Proceed?')">
                                    <?php echo csrfInput(); ?>
                                    <input type="hidden" name="dismiss" value="1">
                                    <input type="hidden" name="venue_id" value="<?php echo $v['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-light shadow-0 <?php echo !$v['is_active'] ? 'disabled' : ''; ?>" title="Dismiss Venue">
                                        <span class="material-icons text-danger align-middle" style="font-size:1.1rem;">block</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.bg-success-light { background-color: #d1e7dd; }
.bg-danger-light { background-color: #f8d7da; }
</style>

<?php layoutFooter(); ?>
