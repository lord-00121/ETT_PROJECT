<?php
// dashboard/admin/tournaments.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/Tournament.php';
require_once __DIR__ . '/../../includes/layout.php';
requireLogin('admin');

$user = currentUser();
$tournamentModel = new Tournament();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    if (isset($_POST['dismiss'])) $tournamentModel->adminDismiss((int)$_POST['tournament_id']);
    header('Location: ' . BASE_URL . '/dashboard/admin/tournaments.php?msg=dismissed');
    exit;
}

$tournaments = $tournamentModel->getAll();
$msg = $_GET['msg'] ?? '';

layoutHead('Manage Tournaments');
layoutNavbar('admin', $user['name']);
layoutSidebar('admin', 'Tournaments');
?>

<div class="mb-4">
    <h3 class="fw-700 m-0">Tournament Directory</h3>
    <p class="text-muted small">Monitor competitive events hosted by sellers.</p>
</div>

<?php if ($msg === 'dismissed'): ?>
    <div class="alert alert-success py-2 small fw-600"><span class="material-icons align-middle fs-6 me-1">check_circle</span> Tournament successfully removed from platform.</div>
<?php endif; ?>

<div class="card shadow-sm border-0" style="border-radius:12px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-uppercase small text-muted">
                <tr>
                    <th class="ps-4 fw-600">Event Name</th>
                    <th class="fw-600">Host (Seller)</th>
                    <th class="fw-600">Sport</th>
                    <th class="fw-600">Location</th>
                    <th class="fw-600">Schedule</th>
                    <th class="fw-600">Status</th>
                    <th class="pe-4 text-end fw-600">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tournaments)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-5"><span class="material-icons d-block mb-2 fs-2">emoji_events</span>No tournaments found on the platform.</td></tr>
                <?php else: foreach ($tournaments as $t): ?>
                    <tr>
                        <td class="ps-4 fw-700 text-primary"><?php echo h($t['name']); ?></td>
                        <td class="small fw-600"><?php echo h($t['seller_name']); ?></td>
                        <td>
                            <span class="badge border border-info text-info bg-light rounded-pill px-3"><?php echo h($t['sport_type']); ?></span>
                        </td>
                        <td class="small text-muted"><?php echo h($t['location']); ?></td>
                        <td class="small">
                            <div class="fw-600"><?php echo date('M d', strtotime($t['start_date'])); ?> - <?php echo date('M d, Y', strtotime($t['end_date'])); ?></div>
                        </td>
                        <td>
                            <?php if ($t['is_active']): ?>
                                <span class="badge bg-success-light text-success fw-600 rounded-pill px-3">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger-light text-danger fw-600 rounded-pill px-3">Dismissed</span>
                            <?php endif; ?>
                        </td>
                        <td class="pe-4 text-end">
                            <form method="POST" onsubmit="return confirm('WARNING: Dismissing this tournament will hide it from the platform. Proceed?')">
                                <?php echo csrfInput(); ?>
                                <input type="hidden" name="dismiss" value="1">
                                <input type="hidden" name="tournament_id" value="<?php echo $t['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-light shadow-0 <?php echo !$t['is_active'] ? 'disabled' : ''; ?>" title="Dismiss Tournament">
                                    <span class="material-icons text-danger align-middle" style="font-size:1.1rem;">block</span>
                                </button>
                            </form>
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
