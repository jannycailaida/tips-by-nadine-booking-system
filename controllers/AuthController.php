<?php
/**
 * Auth Controller (User)
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Referral.php';
require_once __DIR__ . '/../models/EmailQueue.php';
require_once __DIR__ . '/../models/UserCredit.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/EmailService.php';

class AuthController extends BaseController {
    public function showLogin() {
        Auth::redirectIfLoggedIn();
        $this->view('auth/login', [
            'pageTitle' => 'Login - Tips by Nadine',
        ]);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login.php');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->view('auth/login', [
                'pageTitle' => 'Login - Tips by Nadine',
                'error' => 'Please enter both email and password.',
            ]);
            return;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && $userModel->verifyPassword($password, $user['password_hash'])) {
            Auth::loginUser($user);
            $this->redirect('/dashboard.php');
        } else {
            $this->view('auth/login', [
                'pageTitle' => 'Login - Tips by Nadine',
                'error' => 'Invalid email or password.',
            ]);
        }
    }

    public function showRegister() {
        Auth::redirectIfLoggedIn();
        $referralCode = trim($_GET['ref'] ?? '');

        $this->view('auth/register', [
            'pageTitle' => 'Register - Tips by Nadine',
            'referralCode' => $referralCode,
        ]);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register.php');
        }

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $referralCode = trim($_POST['referral_code'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];

        if (empty($firstName)) $errors[] = 'First name is required.';
        if (empty($lastName)) $errors[] = 'Last name is required.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
        if (empty($password) || strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirmPassword) $errors[] = 'Passwords do not match.';

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            $errors[] = 'Email already registered.';
        }

        if (!empty($errors)) {
            $this->view('auth/register', [
                'pageTitle' => 'Register - Tips by Nadine',
                'errors' => $errors,
                'old' => $_POST,
                'referralCode' => $referralCode,
            ]);
            return;
        }

        $userId = $userModel->createUser($email, $password, $firstName, $lastName, $phone);
        $newUser = $userModel->find($userId);

        if ($referralCode !== '') {
            $referral = (new Referral())->registerConversion($referralCode, $userId, $email);
            if ($referral && !empty($referral['referrer_user_id'])) {
                $referrer = $userModel->find($referral['referrer_user_id']);
                if ($referrer) {
                    (new EmailQueue())->queueReferralNotice($referrer, $newUser);
                    (new UserCredit())->recordCredit(
                        $referrer['id'],
                        'referral',
                        'Referral from ' . trim($firstName . ' ' . $lastName),
                        0,
                        $referral['id']
                    );
                }
            }
        }

        // Send welcome email
        $emailService = new EmailService();
        $emailService->send($email, 'Welcome to Tips by Nadine', "
            <h2>Welcome, {$firstName}!</h2>
            <p>Thank you for registering at Tips by Nadine. You can now book appointments online.</p>
        ");

        Auth::loginUser($newUser);
        $this->redirect('/dashboard.php');
    }

    public function logout() {
        Auth::logout();
        $this->redirect('/');
    }
}