<?php
// api/revenue.php â€” Returns revenue data as JSON (AJAX refresh)
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Revenue.php';

header('Content-Type: application/json');
if (!isLoggedIn() || $_SESSION['role'] !== 'seller') {
    echo json_encode(['error' => 'Unauthorized']); exit;
}

$revenueModel = new Revenue();
$summary = $revenueModel->getSummary($_SESSION['user_id']);
$daily = $revenueModel->getDailyLast30($_SESSION['user_id']);
$recent = $revenueModel->getRecentTransactions($_SESSION['user_id'], 10);

echo json_encode([
    'summary' =>  $summary,
    'daily' =>  $daily,
    'recent' =>  $recent,
]);







