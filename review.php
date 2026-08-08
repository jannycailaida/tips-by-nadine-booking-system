<?php
/**
 * Public Review Request
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Auth.php';
Auth::init();

require_once __DIR__ . '/controllers/ReviewController.php';

$controller = new ReviewController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->submit();
} else {
    $controller->show();
}
