<?php
// index.php — Main entry point
require_once __DIR__ . '/config/auth.php';

if (!isLoggedIn()) {
    redirect(BASE_URL . '/login.php');
}

$role = $_SESSION['role'] ?? '';
switch ($role) {
    case 'customer': redirect(BASE_URL . '/dashboard/customer/index.php'); break;
    case 'seller':   redirect(BASE_URL . '/dashboard/seller/index.php');   break;
    case 'admin':    redirect(BASE_URL . '/dashboard/admin/index.php');    break;
    default:         redirect(BASE_URL . '/login.php');
}
