<?php
/**
 * Admin Update Booking Status
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
Auth::init();

require_once __DIR__ . '/../../controllers/AdminDashboardController.php';

(new AdminDashboardController())->updateBookingStatus();
