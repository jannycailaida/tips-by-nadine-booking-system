<?php
/**
 * Email Service
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/../config/app.php';

class EmailService {
    private $config;

    public function __construct() {
        $this->config = require __DIR__ . '/../config/app.php';
    }

    public function send($to, $subject, $body, $isHtml = true) {
        // Log email attempt
        $this->logEmail($to, $subject, $body, 'pending');

        // For development, just log to file
        if ($this->config['app']['debug']) {
            $this->logEmail($to, $subject, $body, 'sent');
            error_log("EMAIL SENT (dev mode): To: {$to}, Subject: {$subject}");
            return true;
        }

        // Production: Use PHPMailer or similar
        // This is a placeholder - integrate with your preferred mail library
        try {
            // Example with PHPMailer:
            // $mail = new PHPMailer(true);
            // $mail->isSMTP();
            // $mail->Host = $this->config['mail']['host'];
            // $mail->Port = $this->config['mail']['port'];
            // $mail->SMTPAuth = true;
            // $mail->Username = $this->config['mail']['username'];
            // $mail->Password = $this->config['mail']['password'];
            // $mail->SMTPSecure = $this->config['mail']['encryption'];
            // $mail->setFrom($this->config['mail']['from_email'], $this->config['mail']['from_name']);
            // $mail->addAddress($to);
            // $mail->Subject = $subject;
            // $mail->Body = $body;
            // $mail->isHTML($isHtml);
            // $mail->send();

            $this->logEmail($to, $subject, $body, 'sent');
            return true;
        } catch (Exception $e) {
            $this->logEmail($to, $subject, $body, 'failed', $e->getMessage());
            return false;
        }
    }

    private function logEmail($to, $subject, $body, $status, $error = null) {
        $db = Database::getInstance();
        $db->insert('email_logs', [
            'recipient_email' => $to,
            'subject' => $subject,
            'body' => $body,
            'status' => $status,
            'error_message' => $error,
            'sent_at' => $status === 'sent' ? date('Y-m-d H:i:s') : null,
        ]);
    }

    public function sendBookingConfirmation($userEmail, $userName, $bookingDetails) {
        $subject = "Booking Confirmation - Tips by Nadine";
        $body = $this->getConfirmationTemplate($userName, $bookingDetails);
        return $this->send($userEmail, $subject, $body);
    }

    public function sendBookingReminder($userEmail, $userName, $bookingDetails) {
        $subject = "Appointment Reminder - Tips by Nadine";
        $body = $this->getReminderTemplate($userName, $bookingDetails);
        return $this->send($userEmail, $subject, $body);
    }

    public function sendOwnerNotification($ownerEmail, $bookingDetails, $type = 'new') {
        $subject = ($type === 'new') ? "New Booking - Tips by Nadine" : "Booking Update - Tips by Nadine";
        $body = $this->getOwnerTemplate($bookingDetails, $type);
        return $this->send($ownerEmail, $subject, $body);
    }

    public function buildReviewRequestEmail($request, $reviewUrl) {
        $clientName = trim(($request['first_name'] ?? '') . ' ' . ($request['last_name'] ?? '')) ?: 'there';
        $serviceName = $request['service_name'] ?? 'your nail appointment';
        $date = !empty($request['booking_date']) ? date('F j, Y', strtotime($request['booking_date'])) : 'your recent visit';
        $time = (!empty($request['start_time']) && !empty($request['end_time']))
            ? date('g:i A', strtotime($request['start_time'])) . ' - ' . date('g:i A', strtotime($request['end_time']))
            : '';

        return [
            'subject' => 'How did your Tips by Nadine appointment go?',
            'body' => "
                <h2>Thank you for visiting Tips by Nadine</h2>
                <p>Hi <strong>" . htmlspecialchars($clientName) . "</strong>,</p>
                <p>We hope you loved your <strong>" . htmlspecialchars($serviceName) . "</strong> appointment on " . htmlspecialchars($date) . "" . ($time ? ' at ' . htmlspecialchars($time) : '') . ".</p>
                <p>Your feedback helps future clients choose with confidence and helps Tips by Nadine keep improving.</p>
                <p style='text-align:center;margin:28px 0;'>
                    <a href='" . htmlspecialchars($reviewUrl) . "' style='background:#cd4e0a;color:#fff;padding:13px 24px;border-radius:999px;text-decoration:none;font-weight:700;'>Leave a Review</a>
                </p>
                <p>If the button does not work, copy this link into your browser:<br>" . htmlspecialchars($reviewUrl) . "</p>
                <p>Warm regards,<br><strong>Tips by Nadine Team</strong></p>
            ",
        ];
    }

    public function buildReferralThankYouEmail($referrer, $newClient) {
        $referrerName = trim(($referrer['first_name'] ?? '') . ' ' . ($referrer['last_name'] ?? '')) ?: 'there';
        $newClientName = trim(($newClient['first_name'] ?? '') . ' ' . ($newClient['last_name'] ?? '')) ?: 'your friend';

        return [
            'subject' => 'Thank you for sharing Tips by Nadine',
            'body' => "
                <h2>Your referral joined Tips by Nadine</h2>
                <p>Hi <strong>" . htmlspecialchars($referrerName) . "</strong>,</p>
                <p>Thank you for sharing Tips by Nadine with <strong>" . htmlspecialchars($newClientName) . "</strong>. Personal recommendations mean a lot to a small salon.</p>
                <p>We have recorded this referral for admin review. If a referral thank-you credit is offered, the team will handle it manually at the salon.</p>
                <p>Warm regards,<br><strong>Tips by Nadine Team</strong></p>
            ",
        ];
    }

    private function getConfirmationTemplate($userName, $details) {
        $date = date('F j, Y', strtotime($details['booking_date']));
        $time = date('g:i A', strtotime($details['start_time'])) . ' - ' . date('g:i A', strtotime($details['end_time']));

        // Tier 2 — save-it / find-us / share-it blocks wired from business config.
        $biz = $this->config['business'] ?? [];
        $addr = $biz['address'] ?? [];
        $locationLabel = implode(', ', array_filter([
            $addr['street'] ?? '',
            $addr['locality'] ?? '',
            $addr['region'] ?? '',
        ])) ?: ($biz['name'] ?? 'Tips by Nadine');
        $base = rtrim($this->config['app']['url'] ?? '', '/');
        $icsUrl = $base . '/calendar.php?id=' . (int)($details['booking_id'] ?? 0);
        $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($locationLabel);

        // Google Calendar add-to-calendar URL (absolute, UTC, app timezone).
        $tz = new DateTimeZone($this->config['app']['timezone'] ?? 'Asia/Manila');
        $utc = new DateTimeZone('UTC');
        $cStart = new DateTime($details['booking_date'] . ' ' . $details['start_time'], $tz);
        $cEnd = new DateTime($details['booking_date'] . ' ' . $details['end_time'], $tz);
        if ($cEnd <= $cStart) $cEnd->modify('+1 day');
        $calUrl = 'https://calendar.google.com/calendar/render?' . http_build_query([
            'action'   => 'TEMPLATE',
            'text'     => $details['service_name'] . ' - Tips by Nadine',
            'dates'    => $cStart->setTimezone($utc)->format('Ymd\THis\Z') . '/' . $cEnd->setTimezone($utc)->format('Ymd\THis\Z'),
            'details'  => !empty($details['design_name']) ? 'Nail design: ' . $details['design_name'] : 'Your Tips by Nadine appointment.',
            'location' => $locationLabel,
        ]);

        $social = $biz['social'] ?? [];
        $socialLinks = '';
        if (!empty($social['instagram'])) {
            $socialLinks .= "<a href=\"" . htmlspecialchars($social['instagram']) . "\" style=\"display:inline-block;margin:0 6px;\">Instagram</a>";
        }
        if (!empty($social['facebook'])) {
            $socialLinks .= "<a href=\"" . htmlspecialchars($social['facebook']) . "\" style=\"display:inline-block;margin:0 6px;\">Facebook</a>";
        }
        if (!empty($social['tiktok'])) {
            $socialLinks .= "<a href=\"" . htmlspecialchars($social['tiktok']) . "\" style=\"display:inline-block;margin:0 6px;\">TikTok</a>";
        }

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #f8f4f0; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #fff; padding: 30px; border: 1px solid #eee; }
                .footer { background: #f8f4f0; padding: 20px; text-align: center; font-size: 12px; color: #888; border-radius: 0 0 8px 8px; }
                .details { background: #fafafa; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
                .detail-row:last-child { border-bottom: none; }
                .label { font-weight: 600; color: #555; }
                .value { color: #333; }
                .btn { display: inline-block; background: #d4a574; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 20px; }
                .btn-orange { background: #cd4e0a; }
                .action-row { text-align: center; margin: 24px 0 8px; }
                .action-btn { display: inline-block; background: #cd4e0a; color: #fff; padding: 12px 22px; text-decoration: none; border-radius: 6px; font-size: 13px; margin: 4px 6px; }
                .action-btn.ghost { background: #f2e7db; color: #4c3d2a; }
                .location { background: #fafafa; padding: 16px 20px; border-radius: 8px; margin: 20px 0; text-align: center; font-size: 14px; color: #555; }
                .location strong { color: #333; display: block; margin-bottom: 4px; }
                .referral { border-top: 1px solid #eee; margin-top: 24px; padding-top: 16px; font-size: 13px; color: #777; text-align: center; }
                .social { text-align: center; margin-top: 12px; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1 style='margin: 0; color: #8b7355;'>Tips by Nadine</h1>
                <p style='margin: 10px 0 0; color: #888;'>Booking Confirmation</p>
            </div>
            <div class='content'>
                <p>Hi <strong>{$userName}</strong>,</p>
                <p>Your appointment has been confirmed! Here are your booking details:</p>
                <div class='details'>
                    <div class='detail-row'><span class='label'>Service:</span> <span class='value'>{$details['service_name']}</span></div>
                    <div class='detail-row'><span class='label'>Date:</span> <span class='value'>{$date}</span></div>
                    <div class='detail-row'><span class='label'>Time:</span> <span class='value'>{$time}</span></div>
                    " . ($details['design_name'] ? "<div class='detail-row'><span class='label'>Nail Design:</span> <span class='value'>{$details['design_name']}</span></div>" : "") . "
                    <div class='detail-row'><span class='label'>Total:</span> <span class='value'>₱" . number_format($details['service_price'] + ($details['design_price'] ?? 0), 2) . "</span></div>
                </div>
                <div class='action-row'>
                    <a class='action-btn' href='{$calUrl}' target='_blank'>Add to Calendar</a>
                    <a class='action-btn ghost' href='{$icsUrl}'>Download .ics</a>
                    <a class='action-btn ghost' href='{$mapsUrl}' target='_blank'>Get Directions</a>
                </div>
                <div class='location'>
                    <strong>{$locationLabel}</strong>
                    You'll walk in knowing exactly where to sit — see hours and map when you tap directions.
                </div>
                <p>If you need to reschedule or cancel, please contact us at least 24 hours in advance.</p>
                <p>We look forward to seeing you! And if you know a friend overdue for some polish, bring them along — booked appointments are twice as nice shared.</p>
                <p style='margin-top: 30px;'>Warm regards,<br><strong>Tips by Nadine Team</strong></p>
                <p class='referral'>Love it? Tag <strong>#TipsByNadine</strong> on Instagram and we'll feature your nails.</p>
            </div>
            <div class='footer'>
                <p>This is an automated message. Please do not reply to this email.</p>
                <p>Tips by Nadine Nail Salon</p>
                <div class='social'>" . ($socialLinks ?: "<p>@tipsbynadine everywhere</p>") . "</div>
            </div>
        </body>
        </html>
        ";
    }

    private function getReminderTemplate($userName, $details) {
        $date = date('F j, Y', strtotime($details['booking_date']));
        $time = date('g:i A', strtotime($details['start_time'])) . ' - ' . date('g:i A', strtotime($details['end_time']));

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #fff8e1; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #fff; padding: 30px; border: 1px solid #eee; }
                .footer { background: #f8f4f0; padding: 20px; text-align: center; font-size: 12px; color: #888; border-radius: 0 0 8px 8px; }
                .details { background: #fff8e1; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .detail-row { display: flex; justify-content: space-between; padding: 8px 0; }
                .label { font-weight: 600; color: #555; }
                .value { color: #333; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1 style='margin: 0; color: #8b7355;'>Tips by Nadine</h1>
                <p style='margin: 10px 0 0; color: #888;'>Appointment Reminder</p>
            </div>
            <div class='content'>
                <p>Hi <strong>{$userName}</strong>,</p>
                <p>This is a friendly reminder about your upcoming appointment:</p>
                <div class='details'>
                    <div class='detail-row'><span class='label'>Service:</span> <span class='value'>{$details['service_name']}</span></div>
                    <div class='detail-row'><span class='label'>Date:</span> <span class='value'>{$date}</span></div>
                    <div class='detail-row'><span class='label'>Time:</span> <span class='value'>{$time}</span></div>
                </div>
                <p>Please arrive a few minutes before your scheduled time. If you need to make any changes, please contact us as soon as possible.</p>
                <p>See you soon!</p>
                <p style='margin-top: 30px;'>Warm regards,<br><strong>Tips by Nadine Team</strong></p>
            </div>
            <div class='footer'>
                <p>This is an automated reminder. Please do not reply to this email.</p>
            </div>
        </body>
        </html>
        ";
    }

    private function getOwnerTemplate($details, $type) {
        $date = date('F j, Y', strtotime($details['booking_date']));
        $time = date('g:i A', strtotime($details['start_time'])) . ' - ' . date('g:i A', strtotime($details['end_time']));
        $isNew = ($type === 'new');
        $title = $isNew ? 'New Booking Received' : 'Booking Updated';
        $intro = $isNew ? 'A new booking has been made' : 'A booking has been updated';
        $status = ucfirst($details['status']);

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #8b7355; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; color: #fff; }
                .content { background: #fff; padding: 30px; border: 1px solid #eee; }
                .footer { background: #f8f4f0; padding: 20px; text-align: center; font-size: 12px; color: #888; border-radius: 0 0 8px 8px; }
                .details { background: #fafafa; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
                .detail-row:last-child { border-bottom: none; }
                .label { font-weight: 600; color: #555; }
                .value { color: #333; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1 style='margin: 0;'>Tips by Nadine - Admin</h1>
                <p style='margin: 10px 0 0; opacity: 0.9;'>{$title}</p>
            </div>
            <div class='content'>
                <p>{$intro}:</p>
                <div class='details'>
                    <div class='detail-row'><span class='label'>Client:</span> <span class='value'>{$details['first_name']} {$details['last_name']}</span></div>
                    <div class='detail-row'><span class='label'>Email:</span> <span class='value'>{$details['email']}</span></div>
                    <div class='detail-row'><span class='label'>Phone:</span> <span class='value'>{$details['phone']}</span></div>
                    <div class='detail-row'><span class='label'>Service:</span> <span class='value'>{$details['service_name']}</span></div>
                    <div class='detail-row'><span class='label'>Date:</span> <span class='value'>{$date}</span></div>
                    <div class='detail-row'><span class='label'>Time:</span> <span class='value'>{$time}</span></div>
                    " . ($details['design_name'] ? "<div class='detail-row'><span class='label'>Design:</span> <span class='value'>{$details['design_name']}</span></div>" : "") . "
                    <div class='detail-row'><span class='label'>Status:</span> <span class='value'>{$status}</span></div>
                </div>
            </div>
            <div class='footer'>
                <p>Tips by Nadine Admin Notification</p>
            </div>
        </body>
        </html>
        ";
    }
}