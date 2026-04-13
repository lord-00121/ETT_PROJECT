<?php
// dashboard/admin/users.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../includes/layout.php';
requireLogin('admin');

$user = currentUser();
$userModel = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $targetId = (int)$_POST['target_id'];
    
    // Prevent self-suspension
    if ($targetId !== $user['id']) {
        if (isset($_POST['suspend'])) {
            $userModel->updateStatus($targetId, 'suspended');
        } elseif (isset($_POST['activate'])) {
            $userModel->updateStatus($targetId, 'active');
        } elseif (isset($_POST['delete'])) {
            $userModel->delete($targetId);
        }
    }
    header('Location: ' . BASE_URL . '/dashboard/admin/users.php?tab=' . ($_GET['tab'] ?? 'customer') . '&msg=updated');
    exit;
}

$allUsers = $userModel->getAll();
$customers = array_filter($allUsers, fn($u) => $u['role'] === 'customer');
$sellers = array_filter($allUsers, fn($u) => $u['role'] === 'seller');

$tab = $_GET['tab'] ?? 'customer';
$displayUsers = ($tab === 'seller') ? $sellers : $customers;
$msg = $_GET['msg'] ?? '';

layoutHead('Manage Users');
layoutNavbar('admin', $user['name']);
layoutSidebar('admin', 'Users');
?>

<div class="mb-4">
    <h3 class="fw-700 m-0">User Management</h3>
    <p class="text-muted small">Manage account statuses, suspend bad actors, or completely entirely accounts.</p>
</div>

<?php if ($msg === 'updated'): ?>
    <div class="alert alert-success py-2 small fw-600"><span class="material-icons align-middle fs-6 me-1">check_circle</span> User account updated successfully.</div>
<?php endif; ?>

<ul class="nav nav-tabs mb-4 px-2">
  <li class="nav-item">
    <a class="nav-link fw-600 <?php echo $tab === 'customer' ? 'active' : 'text-muted'; ?>" href="?tab=customer">
        <span class="material-icons align-middle fs-6 me-1">directions_run</span> Customers (<?php echo count($customers); ?>)
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link fw-600 <?php echo $tab === 'seller' ? 'active' : 'text-muted'; ?>" href="?tab=seller">
        <span class="material-icons align-middle fs-6 me-1">storefront</span> Sellers (<?php echo count($sellers); ?>)
    </a>
  </li>
</ul>

<div class="card shadow-sm border-0" style="border-radius:12px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-uppercase small text-muted">
                <tr>
                    <th class="ps-4 fw-600">ID</th>
                    <th class="fw-600">Name & Email</th>
                    <?php if ($tab === 'seller'): ?><th class="fw-600">Business Name</th><?php endif; ?>
                    <th class="fw-600">Contact / City</th>
                    <th class="fw-600">Registration</th>
                    <th class="fw-600">Status</th>
                    <th class="pe-4 text-end fw-600">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($displayUsers)): ?>
                    <tr><td colspan="<?php echo $tab==='seller'?'7':'6'; ?>" class="text-center text-muted py-5">No <?php echo $tab; ?>s found on the platform.</td></tr>
                <?php else: foreach ($displayUsers as $u): ?>
                    <tr>
                        <td class="ps-4 text-muted small">#<?php echo str_pad($u['id'], 4, '0', STR_PAD_LEFT); ?></td>
                        <td>
                            <div class="fw-700 text-dark"><?php echo h($u['name']); ?></div>
                            <div class="small text-muted"><?php echo h($u['email']); ?></div>
                        </td>
                        <?php if ($tab === 'seller'): ?>
                            <td class="small fw-600"><?php echo h($u['business_name'] ?: '—'); ?></td>
                        <?php endif; ?>
                        <td>
                            <div class="small"><span class="material-icons text-muted align-middle" style="font-size:12px;">phone</span> <?php echo h($u['phone'] ?: '—'); ?></div>
                            <div class="small"><span class="material-icons text-muted align-middle" style="font-size:12px;">location_city</span> <?php echo h($u['city'] ?: '—'); ?></div>
                        </td>
                        <td class="small text-muted"><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                        <td>
                            <?php if ($u['status'] === 'active'): ?>
                                <span class="badge bg-success-light text-success fw-600 rounded-pill px-3">Active</span>
                            <?php else: ?>
                                <span class="badge bg-warning-light text-warning fw-600 rounded-pill px-3 darken-1">Suspended</span>
                            <?php endif; ?>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <?php if ($u['status'] === 'active'): ?>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Suspend this user account? They will be unable to log in.')">
                                        <?php echo csrfInput(); ?>
                                        <input type="hidden" name="target_id" value="<?php echo $u['id']; ?>">
                                        <input type="hidden" name="suspend" value="1">
                                        <button type="submit" class="btn btn-sm btn-light shadow-0 text-warning" title="Suspend User">
                                            <span class="material-icons align-middle" style="font-size:1.1rem;">gavel</span>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" style="display:inline;">
                                        <?php echo csrfInput(); ?>
                                        <input type="hidden" name="target_id" value="<?php echo $u['id']; ?>">
                                        <input type="hidden" name="activate" value="1">
                                        <button type="submit" class="btn btn-sm btn-light shadow-0 text-success" title="Restore User">
                                            <span class="material-icons align-middle" style="font-size:1.1rem;">health_and_safety</span>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <form method="POST" style="display:inline;" onsubmit="return confirm('CRITICAL WARNING: This will permanently delete the user and all linked data (bookings, venues). Are you absolutely sure?')">
                                    <?php echo csrfInput(); ?>
                                    <input type="hidden" name="target_id" value="<?php echo $u['id']; ?>">
                                    <input type="hidden" name="delete" value="1">
                                    <button type="submit" class="btn btn-sm btn-light shadow-0 text-danger" title="Permanently Delete">
                                        <span class="material-icons align-middle" style="font-size:1.1rem;">delete_forever</span>
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
.bg-warning-light { background-color: #fff3cd; color: #856404 !important; }
</style>

<?php layoutFooter(); ?>
