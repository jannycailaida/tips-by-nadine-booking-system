<?php
/**
 * AJAX: Available Time Slots for a Date
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
Auth::init();

require_once __DIR__ . '/../../controllers/BookingController.php';

(new BookingController())->getAvailableSlots();
