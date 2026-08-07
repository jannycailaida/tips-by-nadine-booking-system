<?php
/**
 * Admin Login
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';
Auth::init();

require_once __DIR__ . '/../controllers/AdminAuthController.php';

$controller = new AdminAuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->login();
} else {
    $controller->showLogin();
}
