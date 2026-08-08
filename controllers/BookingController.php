<?php
/**
 * Booking Controller (User)
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/NailDesign.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/TimeSlot.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/EmailService.php';
require_once __DIR__ . '/../includes/AIRecommendationService.php';
require_once __DIR__ . '/../config/app.php';

class BookingController extends BaseController {
    public function showBooking() {
        Auth::requireUser();

        $serviceModel = new Service();
        $designModel = new NailDesign();
        $timeSlotModel = new TimeSlot();

        $services = $serviceModel->getActive();
        $designs = $designModel->getActive();
        $categories = (new \Category())->getActive();

        $this->view('booking/create', [
            'services' => $services,
            'designs' => $designs,
            'categories' => $categories,
            'meta' => [
                'title' => 'Book an Appointment - Tips by Nadine',
                'description' => 'Reserve your nail appointment online at Tips by Nadine — choose a service, design, and real-time time slot. Instant confirmation by email.',
            ],
            'pageRoute' => 'booking.php',
            'pageTitle' => 'Book Appointment - Tips by Nadine',
        ]);
    }

    public function getAvailableSlots() {
        Auth::requireUser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request'], 400);
        }

        $date = $_POST['date'] ?? '';
        if (empty($date)) {
            $this->json(['error' => 'Date is required'], 400);
        }

        // Check if date is in the past
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            $this->json(['error' => 'Cannot book for past dates'], 400);
        }

        $timeSlotModel = new TimeSlot();
        $availableSlots = $timeSlotModel->getAvailableSlots($date);

        $this->json([
            'slots' => array_values($availableSlots),
            'date' => $date,
        ]);
    }

    public function createBooking() {
        Auth::requireUser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/booking.php');
        }

        $userId = Auth::getUserId();
        $serviceId = (int)($_POST['service_id'] ?? 0);
        $designId = !empty($_POST['design_id']) ? (int)$_POST['design_id'] : null;
        $date = $_POST['booking_date'] ?? '';
        $timeSlotId = (int)($_POST['time_slot_id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');

        $errors = [];

        if (!$serviceId) $errors[] = 'Please select a service.';
        if (empty($date)) $errors[] = 'Please select a date.';
        if (!$timeSlotId) $errors[] = 'Please select a time slot.';

        // Validate date not in past
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            $errors[] = 'Cannot book for past dates.';
        }

        // Check availability
        $bookingModel = new Booking();
        if (!$bookingModel->checkAvailability($date, $timeSlotId)) {
            $errors[] = 'This time slot is no longer available.';
        }

        // Handle reference image upload
        $referenceImagePath = null;
        $aiRecommendations = null;

        if (isset($_FILES['reference_image']) && $_FILES['reference_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleImageUpload($_FILES['reference_image']);
            if ($uploadResult['success']) {
                $referenceImagePath = $uploadResult['path'];

                // Get AI recommendations
                $designModel = new NailDesign();
                $availableDesigns = $designModel->getActive();
                $aiService = new AIRecommendationService();
                $aiRecommendations = $aiService->getRecommendations($referenceImagePath, $availableDesigns);
            } else {
                $errors[] = $uploadResult['error'];
            }
        }

        if (!empty($errors)) {
            $this->view('booking/create', [
                'services' => (new Service())->getActive(),
                'designs' => (new NailDesign())->getActive(),
                'categories' => (new \Category())->getActive(),
                'pageTitle' => 'Book Appointment - Tips by Nadine',
                'errors' => $errors,
                'old' => $_POST,
                'ai_recommendations' => $aiRecommendations,
            ]);
            return;
        }

        // Create booking
        $bookingId = $bookingModel->createBooking([
            'user_id' => $userId,
            'service_id' => $serviceId,
            'nail_design_id' => $designId,
            'booking_date' => $date,
            'time_slot_id' => $timeSlotId,
            'notes' => $notes,
            'reference_image_path' => $referenceImagePath,
            'ai_recommendations' => $aiRecommendations ? json_encode($aiRecommendations) : null,
            'status' => 'pending',
        ]);

        // Send confirmation emails
        $userModel = new User();
        $user = $userModel->find($userId);

        $serviceModel = new Service();
        $service = $serviceModel->find($serviceId);

        $timeSlotModel = new TimeSlot();
        $timeSlot = $timeSlotModel->find($timeSlotId);

        $designName = null;
        $designPrice = 0;
        if ($designId) {
            $designModel = new NailDesign();
            $design = $designModel->find($designId);
            $designName = $design['name'] ?? null;
            $designPrice = $design['price'] ?? 0;
        }

        $bookingDetails = [
            'service_name' => $service['name'],
            'service_price' => $service['price'],
            'design_name' => $designName,
            'design_price' => $designPrice,
            'booking_date' => $date,
            'start_time' => $timeSlot['start_time'],
            'end_time' => $timeSlot['end_time'],
            'status' => 'pending',
        ];

        $emailService = new EmailService();
        $emailService->sendBookingConfirmation($user['email'], $user['first_name'], $bookingDetails);
        $emailService->sendOwnerNotification('owner@tipsbynadine.com', array_merge($bookingDetails, [
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
        ]));

        $this->redirect('booking/confirmation.php?id=' . $bookingId);
    }

    public function confirmation($bookingId) {
        Auth::requireUser();

        $bookingModel = new Booking();
        $booking = $bookingModel->getBookingDetails($bookingId);

        if (!$booking || $booking['user_id'] !== Auth::getUserId()) {
            http_response_code(404);
            $this->view('errors/404', ['pageTitle' => 'Page Not Found - Tips by Nadine']);
            return;
        }

        $aiRecommendations = [];
        if ($booking['ai_recommendations']) {
            $aiRecommendations = json_decode($booking['ai_recommendations'], true) ?? [];
        }

        $statusLabels = [
            'cancelled' => 'Appointment Cancelled',
            'completed' => 'Appointment Completed',
            'pending'   => 'Booking Received',
            'confirmed' => 'Booking Confirmed',
        ];
        $label = $statusLabels[$booking['status']] ?? 'Booking Details';

        $this->view('booking/confirmation', [
            'booking' => $booking,
            'ai_recommendations' => $aiRecommendations,
            'noindex' => true,
            'meta' => [
                'title' => $label . ' - Tips by Nadine',
                'description' => 'Your Tips by Nadine appointment details and confirmation.',
            ],
            'pageRoute' => 'booking/confirmation.php?id=' . $bookingId,
            'pageTitle' => $label . ' - Tips by Nadine',
        ]);
    }

    public function myBookings() {
        Auth::requireUser();

        $bookingModel = new Booking();
        $bookings = $bookingModel->getUserBookings(Auth::getUserId());

        $this->view('booking/my-bookings', [
            'bookings' => $bookings,
            'noindex' => true,
            'meta' => [
                'title' => 'My Bookings - Tips by Nadine',
                'description' => 'View and manage your Tips by Nadine appointments.',
            ],
            'pageRoute' => 'dashboard.php',
            'pageTitle' => 'My Bookings - Tips by Nadine',
        ]);
    }

    public function cancelBooking($bookingId) {
        Auth::requireUser();

        $bookingModel = new Booking();
        $booking = $bookingModel->getBookingDetails($bookingId);

        if (!$booking || $booking['user_id'] !== Auth::getUserId()) {
            $this->json(['error' => 'Booking not found'], 404);
        }

        if (!in_array($booking['status'], ['pending', 'confirmed'])) {
            $this->json(['error' => 'This booking can no longer be cancelled'], 400);
        }

        if ($booking['booking_date'] < date('Y-m-d')) {
            $this->json(['error' => 'Past appointments cannot be cancelled online'], 400);
        }

        $bookingModel->updateStatus($bookingId, 'cancelled');

        // Send cancellation email
        $userModel = new User();
        $user = $userModel->find(Auth::getUserId());

        $emailService = new EmailService();
        $emailService->send($user['email'], 'Booking Cancelled - Tips by Nadine', "
            <h2>Booking Cancelled</h2>
            <p>Your booking for {$booking['service_name']} on " . date('F j, Y', strtotime($booking['booking_date'])) . " has been cancelled.</p>
        ");

        $this->json(['success' => true]);
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
        $fileName = 'ref_' . uniqid() . '_' . time() . '.' . $extension;
        $destination = $uploadPath . '/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'path' => 'uploads/' . $fileName];
        }

        return ['success' => false, 'error' => 'Failed to upload image.'];
    }
}