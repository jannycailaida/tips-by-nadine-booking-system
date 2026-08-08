<?php
/**
 * Admin Dashboard Controller
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/NailDesign.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../config/app.php';

class AdminDashboardController extends BaseController {
    private $allowedStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];

    public function dashboard() {
        Auth::requireAdmin();

        $adminModel = new Admin();
        $stats = $adminModel->getDashboardStats();
        $recentBookings = $adminModel->getRecentBookings(10);

        $this->renderAdminView('admin/dashboard/index', [
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'pageTitle' => 'Dashboard - Admin - Tips by Nadine',
        ]);
    }

    public function bookings() {
        Auth::requireAdmin();

        $status = isset($_GET['status']) ? $_GET['status'] : '';
        if ($status !== '' && !in_array($status, $this->allowedStatuses)) {
            $status = '';
        }

        $bookingModel = new Booking();
        $bookings = $bookingModel->getAllBookings($status);

        $this->renderAdminView('admin/bookings/index', [
            'bookings' => $bookings,
            'currentStatus' => $status,
            'pageTitle' => 'Bookings - Admin - Tips by Nadine',
        ]);
    }

    public function bookingDetail($bookingId) {
        Auth::requireAdmin();

        $bookingModel = new Booking();
        $booking = $bookingModel->getBookingDetails($bookingId);

        if (!$booking) {
            http_response_code(404);
            $this->view('errors/404', ['pageTitle' => 'Page Not Found - Tips by Nadine']);
            return;
        }

        $aiRecommendations = [];
        if ($booking['ai_recommendations']) {
            $aiRecommendations = json_decode($booking['ai_recommendations'], true) ?? [];
        }

        $this->renderAdminView('admin/bookings/detail', [
            'booking' => $booking,
            'ai_recommendations' => $aiRecommendations,
            'pageTitle' => 'Booking #' . $bookingId . ' - Admin - Tips by Nadine',
        ]);
    }

    public function updateBookingStatus() {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/bookings.php');
        }

        $bookingId = (int)($_POST['booking_id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if (!$bookingId || !in_array($status, $this->allowedStatuses)) {
            $this->flash('error', 'Invalid booking or status.');
            $this->redirect('/admin/bookings.php');
        }

        $bookingModel = new Booking();
        $bookingModel->updateStatus($bookingId, $status);

        $this->flash('success', 'Booking #' . $bookingId . ' status updated to ' . ucfirst($status) . '.');
        $this->redirect('/admin/bookings.php');
    }

    public function designs() {
        Auth::requireAdmin();

        $designModel = new NailDesign();
        $categoryModel = new Category();

        $this->renderAdminView('admin/designs/index', [
            'designs' => $designModel->getWithCategory(),
            'categories' => $categoryModel->getAll(),
            'pageTitle' => 'Manage Designs - Admin - Tips by Nadine',
        ]);
    }

    public function saveDesign() {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/designs.php');
        }

        $designId = (int)($_POST['design_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        $errors = [];

        if (empty($name)) $errors[] = 'Design name is required.';
        if (!$categoryId) $errors[] = 'Please select a category.';
        if ($price < 0) $errors[] = 'Price cannot be negative.';

        $designModel = new NailDesign();

        // Handle optional image upload
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleImageUpload($_FILES['image']);
            if ($uploadResult['success']) {
                $imagePath = $uploadResult['path'];
            } else {
                $errors[] = $uploadResult['error'];
            }
        }

        if (!empty($errors)) {
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/admin/designs.php');
        }

        $data = [
            'name' => $name,
            'category_id' => $categoryId,
            'price' => $price,
            'description' => $description,
            'is_active' => isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1,
        ];
        if ($imagePath) {
            $data['image_path'] = $imagePath;
        }

        if ($designId) {
            $designModel->update($designId, $data);
            $this->flash('success', 'Design "' . $name . '" updated.');
        } else {
            $designModel->create($data);
            $this->flash('success', 'Design "' . $name . '" created.');
        }

        $this->redirect('/admin/designs.php');
    }

    public function toggleDesign() {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/designs.php');
        }

        $designId = (int)($_POST['design_id'] ?? 0);
        $isActive = (int)($_POST['is_active'] ?? 0);

        if ($designId) {
            $designModel = new NailDesign();
            $designModel->update($designId, ['is_active' => $isActive ? 1 : 0]);
            $this->flash('success', 'Design status updated.');
        }

        $this->redirect('/admin/designs.php');
    }

    public function reviews() {
        Auth::requireAdmin();

        $reviewModel = new Review();
        $designModel = new NailDesign();
        [$avgRating, $reviewCount] = $reviewModel->getAverageRating();

        $this->renderAdminView('admin/reviews/index', [
            'reviews' => $reviewModel->getAllWithDesign(),
            'designs' => array_slice($designModel->getActive(), 0, 30),
            'avgRating' => $avgRating,
            'reviewCount' => $reviewCount,
            'pageTitle' => 'Manage Reviews - Admin - Tips by Nadine',
        ]);
    }

    public function saveReview() {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/reviews.php');
        }

        $reviewId = (int)($_POST['review_id'] ?? 0);
        $clientName = trim($_POST['client_name'] ?? '');
        $rating = (int)($_POST['rating'] ?? 0);
        $reviewText = trim($_POST['review_text'] ?? '');
        $serviceName = trim($_POST['service_name'] ?? '');
        $designId = !empty($_POST['design_id']) ? (int)$_POST['design_id'] : null;

        $errors = [];
        if (empty($clientName)) $errors[] = 'Client name is required.';
        if ($rating < 1 || $rating > 5) $errors[] = 'Rating must be between 1 and 5.';
        if (empty($reviewText)) $errors[] = 'Review text is required.';

        if (!empty($errors)) {
            $this->flash('error', implode(' ', $errors));
            $this->redirect('/admin/reviews.php');
        }

        $data = [
            'client_name' => $clientName,
            'rating' => $rating,
            'review_text' => $reviewText,
            'service_name' => $serviceName ?: null,
            'design_id' => $designId,
            'is_active' => isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1,
        ];

        $reviewModel = new Review();
        if ($reviewId) {
            $reviewModel->update($reviewId, $data);
            $this->flash('success', 'Review updated.');
        } else {
            $reviewModel->create($data);
            $this->flash('success', 'Review added.');
        }

        $this->redirect('/admin/reviews.php');
    }

    public function toggleReview() {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/reviews.php');
        }

        $reviewId = (int)($_POST['review_id'] ?? 0);
        $isActive = (int)($_POST['is_active'] ?? 0);

        if ($reviewId) {
            $reviewModel = new Review();
            $reviewModel->update($reviewId, ['is_active' => $isActive ? 1 : 0]);
            $this->flash('success', 'Review status updated.');
        }

        $this->redirect('/admin/reviews.php');
    }

    private function handleImageUpload($file) {
        $config = require __DIR__ . '/../config/app.php';
        $uploadPath = $config['upload']['path'];
        $maxSize = $config['upload']['max_size'];
        $allowedTypes = $config['upload']['allowed_types'];

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'File size exceeds 5MB limit.'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, and WebP allowed.'];
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'design_' . uniqid() . '_' . time() . '.' . $extension;
        $destination = $uploadPath . '/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'path' => 'uploads/' . $fileName];
        }

        return ['success' => false, 'error' => 'Failed to upload image.'];
    }
}
