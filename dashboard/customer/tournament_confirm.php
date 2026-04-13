<?php
// dashboard/customer/tournament_confirm.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/Tournament.php';
require_once __DIR__ . '/../../includes/layout.php';
requireLogin('customer');

$id = (int)($_GET['id'] ?? 0);
$tournamentModel = new Tournament();
$reg = $tournamentModel->getRegistration($id);

if (!$reg || $reg['customer_id'] != currentUser()['id']) {
    header('Location: ' . BASE_URL . '/dashboard/customer/browse_tournaments.php');
    exit;
}

$user = currentUser();
layoutHead('Registration Confirmed');
layoutNavbar('customer', $user['name']);
layoutSidebar('customer', 'Browse Tournaments');
?>

<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6">
    <div class="card p-5 text-center shadow-lg border-0" style="border-radius:16px;">
      <div class="mb-4">
        <span class="material-icons text-success" style="font-size: 5rem; text-shadow: 0 4px 12px rgba(25,135,84,0.3);">check_circle</span>
      </div>
      
      <h2 class="fw-700 text-dark mb-2">You're Registered!</h2>
      <p class="text-muted mb-4 pb-3 border-bottom">Hold on to your reference code below. The seller will contact you with grouping and match times.</p>
      
      <div class="bg-light p-4 rounded-4 mb-4 text-start">
        <h6 class="fw-700 text-uppercase small text-muted mb-3 d-flex align-items-center"><span class="material-icons fs-5 me-1">receipt</span> Registration Details</h6>
        
        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
            <span class="fw-600 text-muted small">Reference:</span>
            <span class="fw-700 text-primary bg-white px-2 border rounded"><?php echo h($reg['reference']); ?></span>
        </div>
        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
            <span class="fw-600 text-muted small">Tournament:</span>
            <span class="fw-600 text-dark small text-end"><?php echo h($reg['tournament_name']); ?></span>
        </div>
        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
            <span class="fw-600 text-muted small">Location:</span>
            <span class="fw-600 text-dark small text-end"><?php echo h($reg['location']); ?></span>
        </div>
        <div class="d-flex justify-content-between pb-2">
            <span class="fw-600 text-muted small">Date Window:</span>
            <span class="fw-600 bg-warning-subtle text-dark border px-2 rounded small"><?php echo date('M d', strtotime($reg['start_date'])); ?> - <?php echo date('M d, Y', strtotime($reg['end_date'])); ?></span>
        </div>
      </div>
      
      <div class="d-flex flex-column gap-2">
          <a href="<?php echo BASE_URL; ?>/dashboard/customer/index.php" class="btn btn-primary btn-lg shadow-0 fw-700 py-3" style="border-radius:10px;">Return to Dashboard</a>
          <a href="<?php echo BASE_URL; ?>/dashboard/customer/browse_tournaments.php" class="btn btn-light btn-lg shadow-0 fw-600 py-3 text-muted" style="border-radius:10px;">Browse More Events</a>
      </div>
    </div>
  </div>
</div>

<?php layoutFooter(); ?>
