<?php
// dashboard/admin/bookings.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/Booking.php';
require_once __DIR__ . '/../../includes/layout.php';
requireLogin('admin');

$user = currentUser();
$bookingModel = new Booking();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dismiss'])) {
    verifyCsrf();
    $bookingId = (int)$_POST['booking_id'];
    $bookingModel->dismiss($bookingId);
    header('Location: ' . BASE_URL . '/dashboard/admin/bookings.php?msg=dismissed');
    exit;
}

$filters = [
    'status' => $_GET['status'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? '',
];

$bookings = $bookingModel->getAll($filters);

layoutHead('Global Bookings');
layoutNavbar('admin', $user['name']);
layoutSidebar('admin', 'Bookings');
?>

<div class="mb-4">
    <h3 class="fw-700 m-0">Global Bookings List</h3>
    <p class="text-muted small">Comprehensive view of all customer reservations across all venues.</p>
</div>

<?php if (($_GET['msg'] ?? '') === 'dismissed'): ?>
    <div class="alert alert-success py-2 small fw-600"><span class="material-icons align-middle fs-6 me-1">check_circle</span> Booking successfully cancelled.</div>
<?php endif; ?>

<!-- Filters -->
<div class="card p-3 shadow-sm border-0 mb-4 bg-light" style="border-radius:12px;">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-600">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="confirmed" <?php echo $filters['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                <option value="dismissed" <?php echo $filters['status'] === 'dismissed' ? 'selected' : ''; ?>>Dismissed</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-600">Date From</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo h($filters['date_from']); ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-600">Date To</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo h($filters['date_to']); ?>">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-grow-1 shadow-0 fw-600 px-3">Filter</button>
            <a href="<?php echo BASE_URL; ?>/dashboard/admin/bookings.php" class="btn btn-outline-secondary btn-sm shadow-0 fw-600 px-3">Clear</a>
        </div>
    </form>
</div>

<!-- Results Table -->
<div class="card shadow-sm border-0" style="border-radius:12px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-uppercase small text-muted">
                <tr>
                    <th class="ps-4 fw-600">Reference</th>
                    <th class="fw-600">Customer</th>
                    <th class="fw-600">Venue (Seller)</th>
                    <th class="fw-600">Play Date</th>
                    <th class="fw-600">Time Slot</th>
                    <th class="pe-4 text-end fw-600">Status</th>
                    <th class="pe-4 text-end fw-600">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($bookings)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-5"><span class="material-icons d-block mb-2 fs-2">search_off</span>No bookings match these filters.</td></tr>
                <?php else: foreach ($bookings as $b): ?>
                    <tr>
                        <td class="ps-4 fw-700 text-primary small"><?php echo h($b['reference']); ?></td>
                        <td class="small fw-600"><?php echo h($b['customer_name']); ?></td>
                        <td>
                            <div class="fw-600 small"><?php echo h($b['venue_name']); ?></div>
                            <div class="text-muted" style="font-size:0.7rem;">via <?php echo h($b['seller_name']); ?></div>
                        </td>
                        <td class="small text-muted fw-600"><?php echo date('M d, Y', strtotime($b['slot_date'])); ?></td>
                        <td class="small">
                            <span class="bg-light border px-2 py-1 rounded"><?php echo date('h:i A', strtotime($b['slot_start'])); ?> - <?php echo date('h:i A', strtotime($b['slot_end'])); ?></span>
                        </td>
                        <td class="pe-4 text-end">
                            <?php if ($b['status'] === 'confirmed'): ?>
                                <span class="badge bg-success-light text-success fw-600 rounded-pill px-3">Confirmed</span>
                            <?php else: ?>
                                <span class="badge bg-danger-light text-danger fw-600 rounded-pill px-3">Cancelled</span>
                            <?php endif; ?>
                        </td>
                        <td class="pe-4 text-end">
                            <?php if ($b['status'] === 'confirmed'): ?>
                                <form method="POST" onsubmit="return confirm('WARNING: Are you sure you want to forcibly cancel this customer booking?');">
                                    <?php echo csrfInput(); ?>
                                    <input type="hidden" name="dismiss" value="1">
                                    <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-light shadow-0" title="Cancel Booking">
                                        <span class="material-icons text-danger align-middle" style="font-size:1.1rem;">cancel</span>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
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
