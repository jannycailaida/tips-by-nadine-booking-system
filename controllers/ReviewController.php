<?php
/**
 * Public Review Controller
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/ReviewRequest.php';
require_once __DIR__ . '/../models/AnalyticsEvent.php';

class ReviewController extends BaseController {
    public function show() {
        $token = trim($_GET['token'] ?? '');
        $request = $this->getValidRequest($token);

        if (!$request) {
            return;
        }

        if ($request['status'] === 'pending') {
            $opened = (new ReviewRequest())->markOpened($request['id']);
            if ($opened) {
                (new AnalyticsEvent())->track('review_opened', $request['user_id'], $request['booking_id'], [
                    'review_request_id' => $request['id'],
                ]);
                $request['status'] = 'opened';
            }
        }

        $this->view('review/form', [
            'request' => $request,
            'token' => $token,
            'old' => [],
            'errors' => [],
            'noindex' => true,
            'meta' => [
                'title' => 'Share Your Review - Tips by Nadine',
                'description' => 'Share private feedback about your recent Tips by Nadine appointment.',
            ],
            'pageRoute' => 'review.php?token=' . urlencode($token),
            'pageTitle' => 'Share Your Review - Tips by Nadine',
        ]);
    }

    public function submit() {
        $token = trim($_POST['token'] ?? '');
        $request = $this->getValidRequest($token);

        if (!$request) {
            return;
        }

        $rating = (int)($_POST['rating'] ?? 0);
        $clientName = trim($_POST['client_name'] ?? '');
        $reviewText = trim($_POST['review_text'] ?? '');
        $errors = [];

        if ($rating < 1 || $rating > 5) {
            $errors[] = 'Please choose a rating from 1 to 5 stars.';
        }

        if ($reviewText === '') {
            $errors[] = 'Please share a short note about your visit.';
        }

        if (strlen($reviewText) > 2000) {
            $errors[] = 'Please keep your review under 2,000 characters.';
        }

        if ($clientName === '') {
            $clientName = trim(($request['first_name'] ?? '') . ' ' . ($request['last_name'] ?? ''));
        }

        if ($clientName === '') {
            $clientName = 'Tips by Nadine Client';
        }

        if (!empty($errors)) {
            $this->view('review/form', [
                'request' => $request,
                'token' => $token,
                'old' => $_POST,
                'errors' => $errors,
                'noindex' => true,
                'meta' => [
                    'title' => 'Share Your Review - Tips by Nadine',
                    'description' => 'Share private feedback about your recent Tips by Nadine appointment.',
                ],
                'pageRoute' => 'review.php?token=' . urlencode($token),
                'pageTitle' => 'Share Your Review - Tips by Nadine',
            ]);
            return;
        }

        $reviewId = (new Review())->create([
            'client_name' => $clientName,
            'rating' => $rating,
            'review_text' => $reviewText,
            'service_name' => $request['service_name'] ?? null,
            'design_id' => !empty($request['nail_design_id']) ? $request['nail_design_id'] : null,
            'is_active' => 1,
        ]);

        (new ReviewRequest())->markSubmitted($request['id'], $reviewId);
        (new AnalyticsEvent())->track('review_submitted', $request['user_id'], $request['booking_id'], [
            'review_request_id' => $request['id'],
            'review_id' => $reviewId,
            'rating' => $rating,
        ]);

        $this->view('review/thankyou', [
            'request' => $request,
            'noindex' => true,
            'meta' => [
                'title' => 'Thank You - Tips by Nadine',
                'description' => 'Thank you for reviewing your Tips by Nadine appointment.',
            ],
            'pageRoute' => 'review.php',
            'pageTitle' => 'Thank You - Tips by Nadine',
        ]);
    }

    private function getValidRequest($token) {
        $reviewRequestModel = new ReviewRequest();
        $request = $reviewRequestModel->findByToken($token);

        if (!$request) {
            $this->renderInvalid('This review link is not valid.', 'Please check the link from your email or contact Tips by Nadine for help.');
            return null;
        }

        if ($request['status'] === 'submitted') {
            $this->renderInvalid('Review already submitted.', 'Thank you — this appointment has already received a review.');
            return null;
        }

        if ($reviewRequestModel->isExpired($request)) {
            $reviewRequestModel->markExpired($request['id']);
            $this->renderInvalid('This review link has expired.', 'Review links stay open for a limited time after your appointment. You can still contact Tips by Nadine directly if you want to share feedback.');
            return null;
        }

        return $request;
    }

    private function renderInvalid($title, $message) {
        $this->view('review/invalid', [
            'title' => $title,
            'message' => $message,
            'noindex' => true,
            'meta' => [
                'title' => 'Review Link Unavailable - Tips by Nadine',
                'description' => 'This Tips by Nadine review link is unavailable.',
            ],
            'pageRoute' => 'review.php',
            'pageTitle' => 'Review Link Unavailable - Tips by Nadine',
        ]);
    }
}
