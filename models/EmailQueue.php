<?php
/**
 * Email Queue Model
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/AnalyticsEvent.php';
require_once __DIR__ . '/../includes/EmailService.php';

class EmailQueue extends BaseModel {
    protected $table = 'email_queue';

    public function queue($email, $name, $subject, $body, $template = 'custom', $scheduledAt = null, array $meta = [], $relatedId = null) {
        $email = trim(strtolower($email));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        if ($relatedId) {
            $existing = $this->db->fetch(
                "SELECT id FROM {$this->table}
                 WHERE template = ? AND related_id = ? AND status IN ('pending', 'processing', 'sent')
                 ORDER BY id DESC
                 LIMIT 1",
                [$template, $relatedId]
            );

            if ($existing) {
                return $existing['id'];
            }
        }

        $queueId = $this->create([
            'recipient_email' => $email,
            'recipient_name' => $name ?: null,
            'subject' => $subject,
            'body' => $body,
            'template' => $template,
            'related_id' => $relatedId ?: null,
            'scheduled_at' => $scheduledAt ?: date('Y-m-d H:i:s'),
            'meta' => $meta ? json_encode($meta, JSON_UNESCAPED_SLASHES) : null,
        ]);

        (new AnalyticsEvent())->track('email_queued', $meta['user_id'] ?? null, $meta['booking_id'] ?? null, [
            'template' => $template,
            'queue_id' => $queueId,
            'related_id' => $relatedId,
        ]);

        return $queueId;
    }

    public function queueReviewRequest($request) {
        if (!$request || $request['status'] === 'submitted') {
            return null;
        }

        $config = require __DIR__ . '/../config/app.php';
        $appUrl = rtrim($config['app']['url'], '/');
        $reviewUrl = $appUrl . '/review.php?token=' . urlencode($request['token']);
        $emailService = new EmailService();
        $message = $emailService->buildReviewRequestEmail($request, $reviewUrl);

        $queueId = $this->queue(
            $request['email'],
            trim($request['first_name'] . ' ' . $request['last_name']),
            $message['subject'],
            $message['body'],
            'review_request',
            null,
            [
                'user_id' => $request['user_id'],
                'booking_id' => $request['booking_id'],
                'review_request_id' => $request['id'],
            ],
            $request['id']
        );

        if ($queueId) {
            require_once __DIR__ . '/ReviewRequest.php';
            (new ReviewRequest())->markSent($request['id']);
        }

        return $queueId;
    }

    public function queueReferralNotice($referrer, $newClient) {
        if (!$referrer || !$newClient) {
            return null;
        }

        $emailService = new EmailService();
        $message = $emailService->buildReferralThankYouEmail($referrer, $newClient);

        return $this->queue(
            $referrer['email'],
            trim($referrer['first_name'] . ' ' . $referrer['last_name']),
            $message['subject'],
            $message['body'],
            'referral_thank_you',
            null,
            [
                'user_id' => $referrer['id'],
                'referred_user_id' => $newClient['id'],
            ],
            $newClient['id']
        );
    }

    public function sendDue($limit = 25) {
        $limit = max(1, min(100, (int)$limit));
        $emails = $this->db->fetchAll(
            "SELECT * FROM {$this->table}
             WHERE status = 'pending' AND scheduled_at <= NOW()
             ORDER BY scheduled_at ASC, id ASC
             LIMIT " . $limit
        );

        $sent = 0;
        $failed = 0;
        $emailService = new EmailService();
        $analytics = new AnalyticsEvent();

        foreach ($emails as $email) {
            $this->db->update($this->table, [
                'status' => 'processing',
                'attempts' => (int)$email['attempts'] + 1,
            ], 'id = ? AND status = ?', [$email['id'], 'pending']);

            try {
                $ok = $emailService->send($email['recipient_email'], $email['subject'], $email['body']);
                if ($ok) {
                    $this->db->update($this->table, [
                        'status' => 'sent',
                        'sent_at' => date('Y-m-d H:i:s'),
                        'last_error' => null,
                    ], 'id = ?', [$email['id']]);
                    $sent++;
                    $analytics->track('email_sent', null, null, [
                        'template' => $email['template'],
                        'queue_id' => $email['id'],
                    ]);
                } else {
                    $failed++;
                    $this->markFailed($email, 'Email service returned false');
                }
            } catch (Exception $e) {
                $failed++;
                $this->markFailed($email, $e->getMessage());
            }
        }

        return [
            'processed' => count($emails),
            'sent' => $sent,
            'failed' => $failed,
        ];
    }

    public function getStats() {
        $rows = $this->db->fetchAll(
            "SELECT status, COUNT(*) AS total
             FROM {$this->table}
             GROUP BY status"
        );

        $stats = [
            'pending' => 0,
            'processing' => 0,
            'sent' => 0,
            'failed' => 0,
            'cancelled' => 0,
            'total' => 0,
        ];

        foreach ($rows as $row) {
            $status = $row['status'];
            $stats[$status] = (int)$row['total'];
            $stats['total'] += (int)$row['total'];
        }

        return $stats;
    }

    public function getRecent($limit = 8) {
        return $this->db->fetchAll(
            "SELECT *
             FROM {$this->table}
             ORDER BY created_at DESC
             LIMIT " . (int)$limit
        );
    }

    private function markFailed($email, $error) {
        $this->db->update($this->table, [
            'status' => 'failed',
            'last_error' => $error,
        ], 'id = ?', [$email['id']]);

        (new AnalyticsEvent())->track('email_failed', null, null, [
            'template' => $email['template'],
            'queue_id' => $email['id'],
            'error' => $error,
        ]);
    }
}
