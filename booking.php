<?php
/**
 * Booking Form / Create Booking
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Auth.php';
Auth::init();

require_once __DIR__ . '/controllers/BookingController.php';

$controller = new BookingController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->createBooking();
} else {
    $controller->showBooking();
}
