<?php
/**
 * Home Controller
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/NailDesign.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../includes/Auth.php';

class HomeController extends BaseController {
    public function index() {
        $designModel = new NailDesign();
        $categoryModel = new Category();
        $serviceModel = new Service();

        $featuredDesigns = array_slice($designModel->getActive(), 0, 6);
        $categories = $categoryModel->getActive();
        $services = $serviceModel->getActive();

        $this->view('home/index', [
            'featuredDesigns' => $featuredDesigns,
            'categories' => $categories,
            'services' => $services,
            'pageTitle' => 'Tips by Nadine - Nail Salon Booking',
        ]);
    }

    public function gallery() {
        $designModel = new NailDesign();
        $categoryModel = new Category();

        $categoryId = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? '';

        if ($categoryId) {
            $designs = $designModel->getByCategory($categoryId);
        } elseif ($search) {
            $designs = $designModel->findAll(['is_active' => true]);
            $designs = array_filter($designs, function($d) use ($search) {
                return stripos($d['name'], $search) !== false ||
                       stripos($d['description'], $search) !== false;
            });
        } else {
            $designs = $designModel->getActive();
        }

        $categories = $categoryModel->getActive();

        $this->view('home/gallery', [
            'designs' => $designs,
            'categories' => $categories,
            'selectedCategory' => $categoryId,
            'searchTerm' => $search,
            'pageTitle' => 'Nail Design Gallery - Tips by Nadine',
        ]);
    }

    public function designDetail($id) {
        $designModel = new NailDesign();
        $design = $designModel->find($id);

        if (!$design || !$design['is_active']) {
            http_response_code(404);
            $this->view('errors/404', ['pageTitle' => 'Page Not Found - Tips by Nadine']);
            return;
        }

        $this->view('home/design-detail', [
            'design' => $design,
            'pageTitle' => $design['name'] . ' - Tips by Nadine',
        ]);
    }

    public function about() {
        $this->view('home/about', [
            'pageTitle' => 'About Us - Tips by Nadine',
        ]);
    }

    public function contact() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $message = trim($_POST['message'] ?? '');

            $errors = [];

            if (empty($name)) $errors[] = 'Please enter your name.';
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
            if (empty($subject)) $errors[] = 'Please enter a subject.';
            if (empty($message)) $errors[] = 'Please enter a message.';

            if (empty($errors)) {
                // Log the contact message for the owner. In production this
                // would send an email; locally it is stored so the owner can read it.
                $contactLog = dirname(__DIR__) . '/logs/contact_messages.log';
                if (!is_dir(dirname($contactLog))) {
                    mkdir(dirname($contactLog), 0755, true);
                }
                $line = sprintf(
                    "[%s] %s <%s> - %s: %s" . PHP_EOL,
                    date('Y-m-d H:i:s'),
                    $name,
                    $email,
                    $subject,
                    str_replace(["\r", "\n"], ' ', $message)
                );
                file_put_contents($contactLog, $line, FILE_APPEND);

                $this->flash('success', 'Thank you! Your message has been sent. We\'ll get back to you soon.');
                $this->redirect('/contact.php');
            }

            $this->view('home/contact', [
                'pageTitle' => 'Contact Us - Tips by Nadine',
                'errors' => $errors,
                'old' => $_POST,
            ]);
            return;
        }

        $this->view('home/contact', [
            'pageTitle' => 'Contact Us - Tips by Nadine',
        ]);
    }
}