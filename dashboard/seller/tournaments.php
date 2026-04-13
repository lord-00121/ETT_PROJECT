<?php
// dashboard/seller/tournaments.php — Manage tournaments
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/layout.php';
requireLogin('seller');

$user = currentUser();
$db = getPDO();
$stmt = $db->prepare("SELECT * FROM tournaments WHERE seller_id = ? ORDER BY start_date DESC");
$stmt->execute([$user['id']]);
$tournaments = $stmt->fetchAll();

layoutHead('Tournaments');
layoutNavbar('seller', $user['name']);
layoutSidebar('seller', 'Tournaments');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-700 m-0">My Tournaments</h4>
        <p class="text-muted small">Manage your upcoming sport events</p>
    </div>
    <a href="add_tournament.php" class="btn btn-primary shadow-0">
        <span class="material-icons align-middle fs-6 me-1">add</span> Add Tournament
    </a>
</div>

<?php if (empty($tournaments)): ?>
    <div class="card p-5 text-center">
        <span class="material-icons text-muted mb-3" style="font-size:3rem">emoji_events</span>
        <h5>No tournaments hosted yet</h5>
        <p class="text-muted">Host a tournament to boost engagement and revenue.</p>
        <div><a href="add_tournament.php" class="btn btn-primary px-4">Host Your First Event</a></div>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($tournaments as $t): ?>
            <div class="col-md-6">
                <div class="card h-100 p-0 overflow-hidden border shadow-sm">
                    <div class="p-4">
                        <div class="d-flex justify-content-between mb-2">
                            <h5 class="fw-700 m-0"><?php echo h($t['name']); ?></h5>
                            <span class="badge rounded-pill bg-warning text-dark px-3"><?php echo h($t['sport_type']); ?></span>
                        </div>
                        <p class="text-muted small mb-3"><span class="material-icons fs-6 align-middle me-1">location_on</span> <?php echo h($t['location']); ?></p>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">STARTS</small>
                                <span class="fw-600 small"><?php echo date('d M Y', strtotime($t['start_date'])); ?></span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">ENDS</small>
                                <span class="fw-600 small"><?php echo date('d M Y', strtotime($t['end_date'])); ?></span>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="edit_tournament.php?id=<?php echo $t['id']; ?>" class="btn btn-light shadow-0 flex-grow-1">Edit</a>
                            <button class="btn btn-outline-danger shadow-0"><span class="material-icons fs-6">delete</span></button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php layoutFooter(); ?>
