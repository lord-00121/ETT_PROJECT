<?php
// dashboard/seller/revenue.php — Live Revenue Tracker
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/Revenue.php';
require_once __DIR__ . '/../../includes/layout.php';
requireLogin('seller');

$user = currentUser();
$revenueModel = new Revenue();
$summary = $revenueModel->getSummary($user['id']);
$daily = $revenueModel->getDailyLast30($user['id']);
$recent = $revenueModel->getRecentTransactions($user['id'], 10);

// Build chart data (Past 15 days + Next 15 days pipeline)
$chartLabels = [];
$chartData = [];
for ($i = -15; $i <= 14; $i++) {
    $day = date('Y-m-d', strtotime("$i days"));
    $chartLabels[] = date('d M', strtotime($day));
    $amount = 0;
    foreach ($daily as $d) { 
        if ($d['day'] === $day) { $amount = (float)$d['total']; break; } 
    }
    $chartData[] = $amount;
}

layoutHead('Revenue Tracker');
layoutNavbar('seller', $user['name']);
layoutSidebar('seller', 'Revenue');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-700 m-0">Revenue Tracker</h4>
        <p class="text-muted small">Real-time earnings from confirmed bookings</p>
    </div>
    <button class="btn btn-outline-primary btn-sm" onclick="window.location.reload()">
        <span class="material-icons align-middle fs-6">refresh</span> Refresh
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3 border-start border-4 border-primary">
            <div class="small text-muted fw-600 mb-1">TODAY</div>
            <div class="h4 fw-700 mb-0">₹ <?php echo number_format($summary['today'] ?? 0); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-start border-4 border-warning">
            <div class="small text-muted fw-600 mb-1">THIS WEEK</div>
            <div class="h4 fw-700 mb-0">₹ <?php echo number_format($summary['week'] ?? 0); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-start border-4 border-info">
            <div class="small text-muted fw-600 mb-1">THIS MONTH</div>
            <div class="h4 fw-700 mb-0">₹ <?php echo number_format($summary['month'] ?? 0); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 border-start border-4 border-success">
            <div class="small text-muted fw-600 mb-1">ALL TIME</div>
            <div class="h4 fw-700 mb-0">₹ <?php echo number_format($summary['alltime'] ?? 0); ?></div>
        </div>
    </div>
</div>

<div class="card p-4 mb-4">
    <h6 class="fw-700 mb-4">Revenue Pipeline (30-Day Window)</h6>
    <div style="height: 300px;">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<div class="card p-0">
    <div class="p-4 border-bottom"><h6 class="fw-700 mb-0">Recent Transactions</h6></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Reference</th>
                    <th>Venue</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th class="text-end pe-4">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recent)): ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted">No transactions found.</td></tr>
                <?php else: foreach ($recent as $t): ?>
                    <tr>
                        <td class="ps-4 fw-600 text-primary small"><?php echo h($t['reference']); ?></td>
                        <td><?php echo h($t['venue_name']); ?></td>
                        <td><?php echo h($t['customer_name']); ?></td>
                        <td><?php echo date('d M Y', strtotime($t['slot_date'])); ?></td>
                        <td class="text-end pe-4 fw-700 text-success">₹ <?php echo number_format($t['amount']); ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chartLabels); ?>,
            datasets: [{
                label: 'Daily Revenue',
                data: <?php echo json_encode($chartData); ?>,
                backgroundColor: 'rgba(26,107,60, 0.8)',
                borderColor: '#1A6B3C',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>
<?php layoutFooter(); ?>
