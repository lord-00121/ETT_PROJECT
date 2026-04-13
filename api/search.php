<?php
// api/search.php â€” AJAX venue search endpoint
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Venue.php';

header('Content-Type: application/json');
if (!isLoggedIn()) { echo json_encode(['error' => 'Unauthorized']); exit; }

$filters = [
    'q' =>  trim($_GET['q'] ?? ''),
    'sport' =>  !empty($_GET['sport']) ? (array)$_GET['sport'] : [],
    'location' =>  trim($_GET['location'] ?? ''),
    'date' =>  trim($_GET['date'] ?? ''),
    'min_price' =>  $_GET['min_price'] ?? '',
    'max_price' =>  $_GET['max_price'] ?? '',
    'min_rating' =>  $_GET['min_rating'] ?? '',
];

$venueModel = new Venue();
$venues = $venueModel->search($filters);

// Sanitise output
$out = array_map(function($v) {
    return [
        'id' =>  $v['id'],
        'name' =>  $v['name'],
        'sport_type' =>  $v['sport_type'],
        'location' =>  $v['location'],
        'rating_avg' =>  $v['rating_avg'],
        'price_per_slot' =>  $v['price_per_slot'],
        'primary_photo' =>  $v['primary_photo'],
        'seller_name' =>  $v['seller_name'],
    ];
}, $venues);

echo json_encode(['venues' =>  $out, 'count' =>  count($out)]);







