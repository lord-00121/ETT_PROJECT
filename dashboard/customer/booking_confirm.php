<?php
// dashboard/customer/booking_confirm.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/Booking.php';
require_once __DIR__ . '/../../includes/layout.php';
requireLogin('customer');

$user = currentUser();
$id = (int)($_GET['id'] ?? 0);
$bookingModel = new Booking();
$booking = $bookingModel->getById($id);

if (!$booking || $booking['customer_id'] != $user['id']) {
    header('Location: ' . BASE_URL . '/dashboard/customer/browse.php');
    exit;
}

layoutHead('Booking Confirmed');
layoutNavbar('customer', $user['name']);
layoutSidebar('customer', 'My Bookings');
?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card p-5 text-center shadow-sm border-0 border-top border-4 border-success mt-4">
            <div class="mb-4">
                <span class="material-icons text-success" style="font-size: 5rem;">check_circle</span>
            </div>
            
            <h2 class="fw-700 mb-2">Booking Confirmed! 🎉</h2>
            <p class="text-muted mb-4">Your slot has been successfully reserved.</p>
            
            <div class="bg-light p-3 rounded d-inline-block mb-4">
                <span class="text-muted small fw-600 me-2 text-uppercase">Booking Ref:</span>
                <span class="fs-5 fw-700 text-dark"><?php echo h($booking['reference']); ?></span>
            </div>

            <div class="text-start bg-white border rounded p-4 mb-4">
                <div class="row align-items-center mb-3">
                    <div class="col-4 text-muted small fw-600"><span class="material-icons align-middle fs-6 me-1 text-primary">stadium</span> Venue:</div>
                    <div class="col-8 fw-700"><?php echo h($booking['venue_name']); ?></div>
                </div>
                <div class="row align-items-center mb-3">
                    <div class="col-4 text-muted small fw-600"><span class="material-icons align-middle fs-6 me-1 text-primary">location_on</span> Location:</div>
                    <div class="col-8"><?php echo h($booking['location']); ?></div>
                </div>
                <div class="row align-items-center mb-3">
                    <div class="col-4 text-muted small fw-600"><span class="material-icons align-middle fs-6 me-1 text-primary">calendar_today</span> Date:</div>
                    <div class="col-8 fw-700 text-dark"><?php echo date('l, d M Y', strtotime($booking['slot_date'])); ?></div>
                </div>
                <div class="row align-items-center mb-3 border-bottom pb-3">
                    <div class="col-4 text-muted small fw-600"><span class="material-icons align-middle fs-6 me-1 text-primary">schedule</span> Time Slot:</div>
                    <div class="col-8 fw-700 text-dark"><?php echo date('h:i A', strtotime($booking['slot_start'])); ?> – <?php echo date('h:i A', strtotime($booking['slot_end'])); ?></div>
                </div>
                <div class="row align-items-center">
                    <div class="col-4 text-muted small fw-600"><span class="material-icons align-middle fs-6 me-1 text-primary">person</span> Organizer:</div>
                    <div class="col-8">
                        <div><?php echo h($booking['seller_name']); ?></div>
                        <div class="text-muted small"><?php echo h($booking['seller_phone'] ?: 'No Phone Number'); ?></div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-center mt-2 flex-wrap">
                <a href="<?php echo BASE_URL; ?>/dashboard/customer/past_bookings.php" class="btn btn-primary btn-lg shadow-0 fw-600 px-4">
                    <span class="material-icons align-middle fs-6 me-1">history</span> My Bookings
                </a>
                <a href="<?php echo BASE_URL; ?>/dashboard/customer/browse.php" class="btn btn-light btn-lg shadow-0 fw-600 px-4">
                    <span class="material-icons align-middle fs-6 me-1">search</span> Browse More
                </a>
            </div>
        </div>
    </div>
</div>

<?php layoutFooter(); ?>
