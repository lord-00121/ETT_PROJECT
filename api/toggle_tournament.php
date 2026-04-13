<?php
// api/toggle_tournament.php â€” Toggle tournament active status (AJAX)
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Tournament.php';

header('Content-Type: application/json');
if (!isLoggedIn() || $_SESSION['role'] !== 'seller') {
    echo json_encode(['success' => false,'error' => 'Unauthorized']); exit;
}

$token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!hash_equals(csrfToken(), $token)) {
    echo json_encode(['success' => false,'error' => 'CSRF']); exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$tournamentId = (int)($body['tournament_id'] ?? 0);
$active = (int)($body['active'] ?? 0);

if (!$tournamentId) { echo json_encode(['success' => false]); exit; }

$tournamentModel = new Tournament();
$tournamentModel->toggle($tournamentId, $_SESSION['user_id'], $active ? 1 : 0);
echo json_encode(['success' => true]);







