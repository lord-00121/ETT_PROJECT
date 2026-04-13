<?php
// dashboard/customer/index.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/Booking.php';
require_once __DIR__ . '/../../models/Tournament.php';
require_once __DIR__ . '/../../includes/layout.php';
requireLogin('customer');

$user = currentUser();

$bookingModel = new Booking();
$tournamentModel = new Tournament();

$allBookings = $bookingModel->getByCustomer($user['id']);
$tournaments = $tournamentModel->getAll();
$myRegistrations = $tournamentModel->getCustomerRegistrations($user['id']);

$now = time();
$upcomingBookings = [];
$pastUnreviewed = [];

foreach ($allBookings as $b) {
    if ($b['status'] !== 'confirmed') continue;
    $slotTime = strtotime($b['slot_date'] . ' ' . $b['slot_end']);
    if ($slotTime > $now) {
        $upcomingBookings[] = $b;
    } else {
        if (!$b['review_id']) {
            $pastUnreviewed[] = $b;
        }
    }
}

layoutHead('Customer Dashboard');
layoutNavbar('customer', $user['name']);
layoutSidebar('customer', 'Dashboard');
?>

<div class="mb-4">
    <h3 class="fw-700 m-0">Hello, <?php echo h(explode(' ', $user['name'])[0]); ?>!</h3>
    <p class="text-muted small">Welcome to your Sportify dashboard. Ready for your next game?</p>
</div>

<!-- Key Stat Cards -->
<div class="row mt-4 g-4 mb-5">
    <div class="col-md-4">
        <div class="card p-4 h-100 shadow-sm border-0 border-start border-4 border-primary" style="border-radius:12px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0 text-muted small text-uppercase fw-600">Total Bookings</p>
                <span class="material-icons text-primary bg-light p-2 rounded-circle">history_edu</span>
            </div>
            <h2 class="fw-700 text-dark mb-0"><?php echo count($allBookings); ?></h2>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card p-4 h-100 shadow-sm border-0 border-start border-4 border-warning" style="border-radius:12px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0 text-muted small text-uppercase fw-600">Pending Reviews</p>
                <span class="material-icons text-warning bg-light p-2 rounded-circle">star_half</span>
            </div>
            <h2 class="fw-700 text-dark mb-0"><?php echo count($pastUnreviewed); ?></h2>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card p-4 h-100 shadow-sm border-0 border-start border-4 border-success" style="border-radius:12px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0 text-muted small text-uppercase fw-600">Upcoming Games</p>
                <span class="material-icons text-success bg-light p-2 rounded-circle">directions_run</span>
            </div>
            <h2 class="fw-700 text-dark mb-0"><?php echo count($upcomingBookings); ?></h2>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- Left Column: Upcoming Bookings & Reviews -->
    <div class="col-lg-7">
        <?php if (!empty($pastUnreviewed)): ?>
            <div class="card p-4 shadow-sm border-0 border-warning mb-4" style="border-radius:12px; background-color: #fffdf5;">
                <h6 class="fw-700 text-warning mb-3"><span class="material-icons align-middle fs-5 me-1">rate_review</span> You have pending reviews!</h6>
                <p class="small text-muted mb-3">Share your experience about these venues to help others decide.</p>
                <?php foreach(array_slice($pastUnreviewed, 0, 2) as $rev): ?>
                    <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded border mb-2">
                        <div>
                            <div class="fw-700 small"><?php echo h($rev['venue_name']); ?></div>
                            <div class="text-muted" style="font-size:0.75rem;">Played on <?php echo date('d M Y', strtotime($rev['slot_date'])); ?></div>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/dashboard/customer/past_bookings.php" class="btn btn-warning btn-sm shadow-0 px-3 fw-600 rounded-pill">Leave Review</a>
                    </div>
                <?php endforeach; ?>
                <?php if(count($pastUnreviewed) > 2): ?>
                    <a href="<?php echo BASE_URL; ?>/dashboard/customer/past_bookings.php" class="small fw-600 text-center d-block mt-2">View all pending reviews -></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0" style="border-radius:12px;">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-700 m-0"><span class="material-icons text-primary align-middle me-1">event</span> Your Upcoming Games</h6>
                <a href="<?php echo BASE_URL; ?>/dashboard/customer/past_bookings.php" class="btn btn-light btn-sm shadow-0 fw-600">View All</a>
            </div>
            <div class="card-body p-4">
                <?php if (empty($upcomingBookings)): ?>
                    <div class="text-center py-4">
                        <span class="material-icons text-muted opacity-25" style="font-size:3rem;">calendar_today</span>
                        <p class="text-muted mt-2 fw-500">No upcoming games scheduled.</p>
                        <a href="<?php echo BASE_URL; ?>/dashboard/customer/browse.php" class="btn btn-primary btn-sm mt-2 shadow-0">Book a Venue</a>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-light list-group-small">
                        <?php foreach (array_slice($upcomingBookings, 0, 4) as $ub): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center p-3 border rounded mb-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light text-primary fw-700 rounded text-center p-2" style="width:50px;">
                                        <div style="font-size:0.7rem; text-transform:uppercase;"><?php echo date('M', strtotime($ub['slot_date'])); ?></div>
                                        <div class="fs-5 lh-1"><?php echo date('d', strtotime($ub['slot_date'])); ?></div>
                                    </div>
                                    <div>
                                        <div class="fw-700"><?php echo h($ub['venue_name']); ?></div>
                                        <div class="text-muted small"><span class="material-icons align-middle text-primary" style="font-size:12px;">schedule</span> <?php echo date('h:i A', strtotime($ub['slot_start'])); ?></div>
                                    </div>
                                </div>
                                <span class="badge bg-success-light text-success fw-600 rounded-pill px-3">Confirmed</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Active Tournaments Display -->
    <div class="col-lg-5">
        <?php if (!empty($myRegistrations)): ?>
            <div class="card shadow-sm border-0 mb-4 bg-info text-white" style="border-radius:12px;">
                <div class="card-body p-4">
                    <h6 class="fw-700 mb-3 d-flex align-items-center"><span class="material-icons me-2">emoji_events</span> Your Tournaments</h6>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach (array_slice($myRegistrations, 0, 2) as $reg): ?>
                            <div class="bg-white text-dark rounded p-3 shadow-sm">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-700 m-0 text-info" style="font-size:0.9rem;"><?php echo h($reg['tournament_name']); ?></h6>
                                    <span class="badge bg-light text-dark border small">REF: <?php echo h($reg['reference']); ?></span>
                                </div>
                                <div class="text-muted small d-flex align-items-center">
                                    <span class="material-icons text-info me-1" style="font-size:14px;">event</span> <?php echo date('M d', strtotime($reg['start_date'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0 bg-primary text-white" style="border-radius:12px;">
            <div class="card-body p-4">
                <h6 class="fw-700 mb-3 d-flex align-items-center"><span class="material-icons me-2">emoji_events</span> Discover Tournaments</h6>
                <p class="small text-white-50 mb-4">Check out ongoing or upcoming tournaments on Sportify and challenge yourself!</p>

                <?php 
                $activeTournaments = array_filter($tournaments, fn($t) => $t['is_active'] == 1);
                if (empty($activeTournaments)): 
                ?>
                    <div class="bg-white bg-opacity-10 rounded p-4 text-center">
                        <span class="material-icons text-white-50" style="font-size:2rem;">event_busy</span>
                        <p class="small text-white-50 mt-2 mb-0">No active tournaments right now. Check back later!</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach (array_slice($activeTournaments, 0, 3) as $t): ?>
                            <div class="bg-white text-dark rounded p-3 shadow-sm hover-lift text-decoration-none" style="transition: transform 0.2s;" onclick="window.location.href='<?php echo BASE_URL; ?>/dashboard/customer/tournament_detail.php?id=<?php echo $t['id']; ?>'">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-700 m-0" style="font-size:0.9rem;"><?php echo h($t['name']); ?></h6>
                                    <span class="badge bg-warning text-dark text-uppercase" style="font-size:0.65rem;"><?php echo h($t['sport_type']); ?></span>
                                </div>
                                <div class="text-muted small d-flex align-items-center mb-1">
                                    <span class="material-icons text-primary me-1" style="font-size:14px;">location_on</span> <?php echo h($t['location']); ?>
                                </div>
                                <div class="text-muted small d-flex justify-content-between align-items-center mt-2 border-top pt-2">
                                    <span>Starts: <?php echo date('M d', strtotime($t['start_date'])); ?></span>
                                    <span class="text-primary fw-600">View Details &rarr;</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.hover-lift:hover { transform: translateY(-4px); }
.bg-success-light { background-color: #d1e7dd; }
</style>

<?php layoutFooter(); ?>
