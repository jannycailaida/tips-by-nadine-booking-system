<?php
/**
 * Session & Authentication Helper
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/helpers.php';

class Auth {
    private static $initialized = false;

    public static function init() {
        if (self::$initialized) return;

        $config = require __DIR__ . '/../config/app.php';
        session_set_cookie_params([
            'lifetime' => $config['session']['lifetime'],
            'path' => '/',
            'httponly' => true,
            'secure' => isset($_SERVER['HTTPS']),
            'samesite' => 'Lax',
        ]);
        session_name($config['session']['name']);
        session_start();
        self::$initialized = true;
    }

    public static function loginUser($user) {
        self::init();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_logged_in'] = true;
    }

    public static function loginAdmin($admin) {
        self::init();
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['first_name'] . ' ' . $admin['last_name'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_role'] = $admin['role'];
        $_SESSION['admin_logged_in'] = true;
    }

    public static function logout() {
        self::init();
        session_destroy();
        self::$initialized = false;
    }

    public static function isUserLoggedIn() {
        self::init();
        return !empty($_SESSION['user_logged_in']);
    }

    public static function isAdminLoggedIn() {
        self::init();
        return !empty($_SESSION['admin_logged_in']);
    }

    public static function getUserId() {
        self::init();
        return $_SESSION['user_id'] ?? null;
    }

    public static function getAdminId() {
        self::init();
        return $_SESSION['admin_id'] ?? null;
    }

    public static function getUserName() {
        self::init();
        return $_SESSION['user_name'] ?? '';
    }

    public static function requireUser() {
        if (!self::isUserLoggedIn()) {
            header('Location: ' . base_url('login.php'));
            exit;
        }
    }

    public static function requireAdmin() {
        if (!self::isAdminLoggedIn()) {
            header('Location: ' . base_url('admin/login.php'));
            exit;
        }
    }

    public static function redirectIfLoggedIn() {
        if (self::isUserLoggedIn()) {
            header('Location: ' . base_url('dashboard.php'));
            exit;
        }
        if (self::isAdminLoggedIn()) {
            header('Location: ' . base_url('admin/dashboard.php'));
            exit;
        }
    }
}