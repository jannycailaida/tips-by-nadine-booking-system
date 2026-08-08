<?php
/**
 * Calendar (.ics) download for a booking
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Auth.php';
Auth::init();

require_once __DIR__ . '/controllers/BookingController.php';

$bookingId = (int)($_GET['id'] ?? 0);
(new BookingController())->calendar($bookingId);