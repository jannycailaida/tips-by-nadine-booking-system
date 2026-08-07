<?php
/**
 * Admin Auth Controller
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../includes/Auth.php';

class AdminAuthController extends BaseController {
    public function showLogin() {
        if (Auth::isAdminLoggedIn()) {
            $this->redirect('/admin/dashboard.php');
        }
        // The admin login page is a full standalone page (no admin layout),
        // since the admin is not authenticated yet.
        $this->renderStandalone('admin/auth/login', [
            'pageTitle' => 'Admin Login - Tips by Nadine',
        ]);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/login.php');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->renderStandalone('admin/auth/login', [
                'pageTitle' => 'Admin Login - Tips by Nadine',
                'error' => 'Please enter both email and password.',
            ]);
            return;
        }

        $adminModel = new Admin();
        $admin = $adminModel->findByEmail($email);

        if ($admin && $adminModel->verifyPassword($password, $admin['password_hash'])) {
            Auth::loginAdmin($admin);
            $this->redirect('/admin/dashboard.php');
        } else {
            $this->renderStandalone('admin/auth/login', [
                'pageTitle' => 'Admin Login - Tips by Nadine',
                'error' => 'Invalid email or password.',
            ]);
        }
    }

    public function logout() {
        Auth::logout();
        $this->redirect('/admin/login.php');
    }
}