<?php
/**
 * AJAX: Cancel Booking
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
Auth::init();

require_once __DIR__ . '/../../controllers/BookingController.php';

$id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
(new BookingController())->cancelBooking($id);
