<?php
// api/toggle_venue.php â€” Toggle venue active status (AJAX)
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Venue.php';

header('Content-Type: application/json');
if (!isLoggedIn() || $_SESSION['role'] !== 'seller') {
    echo json_encode(['success' => false,'error' => 'Unauthorized']); exit;
}

// CSRF via header
$token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!hash_equals(csrfToken(), $token)) {
    echo json_encode(['success' => false,'error' => 'CSRF']); exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$venueId = (int)($body['venue_id'] ?? 0);
$active = (int)($body['active'] ?? 0);

if (!$venueId) { echo json_encode(['success' => false]); exit; }

$venueModel = new Venue();
$venueModel->toggle($venueId, $_SESSION['user_id'], $active ? 1 : 0);
echo json_encode(['success' => true]);







