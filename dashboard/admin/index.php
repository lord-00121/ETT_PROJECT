<?php
// dashboard/admin/index.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Venue.php';
require_once __DIR__ . '/../../models/Booking.php';
require_once __DIR__ . '/../../includes/layout.php';
requireLogin('admin');

$user = currentUser();

// Metrics
$userModel = new User();
$allUsers = $userModel->getAll();
$customerCount = count(array_filter($allUsers, fn($u) => $u['role'] === 'customer'));
$sellerCount = count(array_filter($allUsers, fn($u) => $u['role'] === 'seller'));

$venueModel = new Venue();
$allVenues = $venueModel->getAll();
$activeVenuesCount = count(array_filter($allVenues, fn($v) => $v['is_active'] == 1));

$bookingModel = new Booking();
$allBookings = $bookingModel->getAll();

layoutHead('Admin Dashboard');
layoutNavbar('admin', $user['name']);
layoutSidebar('admin', 'Dashboard');
?>

<div class="mb-4">
    <h3 class="fw-700 m-0 text-primary">⚙️ Administrator Control Panel</h3>
    <p class="text-muted small">Overview of the platform's key statistics.</p>
</div>

<div class="row g-4 mb-5">
    <!-- Platform Users -->
    <div class="col-md-3">
        <div class="card p-4 shadow-sm border-0 border-start border-4 border-primary h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small text-uppercase fw-600">Customers</span>
                <span class="material-icons bg-light text-primary p-2 rounded-circle">directions_run</span>
            </div>
            <h2 class="fw-700 mb-0"><?php echo $customerCount; ?></h2>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card p-4 shadow-sm border-0 border-start border-4 border-info h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small text-uppercase fw-600">Sellers</span>
                <span class="material-icons bg-light text-info p-2 rounded-circle">storefront</span>
            </div>
            <h2 class="fw-700 mb-0"><?php echo $sellerCount; ?></h2>
        </div>
    </div>
    
    <!-- Active Venues -->
    <div class="col-md-3">
        <div class="card p-4 shadow-sm border-0 border-start border-4 border-success h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small text-uppercase fw-600">Active Venues</span>
                <span class="material-icons bg-light text-success p-2 rounded-circle">stadium</span>
            </div>
            <h2 class="fw-700 mb-0"><?php echo $activeVenuesCount; ?></h2>
        </div>
    </div>
    
    <!-- Total Bookings -->
    <div class="col-md-3">
        <div class="card p-4 shadow-sm border-0 border-start border-4 border-warning h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small text-uppercase fw-600">Overall Bookings</span>
                <span class="material-icons bg-light text-warning p-2 rounded-circle">event_available</span>
            </div>
            <h2 class="fw-700 mb-0"><?php echo count($allBookings); ?></h2>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Quick Links -->
    <div class="col-12">
        <div class="card shadow-sm border-0 p-4">
            <h6 class="fw-700 mb-3"><span class="material-icons text-primary align-middle me-2">link</span> Quick Management Links</h6>
            <div class="d-flex flex-wrap gap-3">
                <a href="users.php" class="btn btn-outline-primary shadow-0 fw-600">Manage Users</a>
                <a href="venues.php" class="btn btn-outline-success shadow-0 fw-600">Manage Venues</a>
                <a href="tournaments.php" class="btn btn-outline-warning shadow-0 fw-600">Manage Tournaments</a>
                <a href="bookings.php" class="btn btn-outline-info shadow-0 fw-600">View All Bookings</a>
            </div>
        </div>
    </div>
</div>

<?php layoutFooter(); ?>
