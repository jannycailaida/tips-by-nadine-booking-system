<?php
/**
 * Booking Confirmation
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';
Auth::init();

require_once __DIR__ . '/../controllers/BookingController.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
(new BookingController())->confirmation($id);
