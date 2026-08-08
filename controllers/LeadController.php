<?php
/**
 * Lead Controller
 * Tips by Nadine Booking System
 *
 * Landing-page email capture. Accepts JSON (AJAX, preferred) or a plain
 * form POST (no-JS fallback) and keeps the salon contact list tidy.
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Lead.php';
require_once __DIR__ . '/../models/AnalyticsEvent.php';
require_once __DIR__ . '/../includes/Auth.php';

class LeadController extends BaseController {
    public function capture() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
        }

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;

        $email = trim(strtolower($_POST['email'] ?? ''));
        $error = '';
        if ($email === '') {
            $error = 'Please enter your email address.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please double-check that email address.';
        }

        if ($error) {
            if ($isAjax) {
                $this->json(['success' => false, 'error' => $error], 422);
            }
            $this->flash('error', $error);
            $this->redirect('/');
        }

        $userId = Auth::getUserId();
        $leadModel = new Lead();
        $existing = $leadModel->getByEmail($email);

        if ($existing) {
            // Reuse the row — keep the contact list clean.
            $leadModel->refresh($existing['id'], [
                'user_id' => $userId ?: $existing['user_id'],
                'source'  => 'landing',
            ]);
        } else {
            $leadModel->capture([
                'email'   => $email,
                'user_id' => $userId ?: null,
                'source'  => 'landing',
            ]);
            (new AnalyticsEvent())->track('lead_captured', $userId ?: null, null, ['source' => 'landing']);
        }

        if ($isAjax) {
            $this->json(['success' => true, 'message' => "You're on the list — watch your inbox for the next good thing."]);
        }

        $this->flash('success', "You're on the list! We'll send nail inspiration straight to your inbox.");
        $this->redirect('/');
    }
}