<?php
/**
 * Referral Model
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/AnalyticsEvent.php';

class Referral extends BaseModel {
    protected $table = 'referrals';

    public function registerConversion($referralCode, $referredUserId, $referredEmail = null) {
        $code = $this->normalizeCode($referralCode);
        if ($code === '' || !$referredUserId) {
            return null;
        }

        $userModel = new User();
        $referrer = $userModel->findByReferralCode($code);
        if (!$referrer || (int)$referrer['id'] === (int)$referredUserId) {
            return null;
        }

        $existing = $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE referred_user_id = ? LIMIT 1",
            [$referredUserId]
        );

        if ($existing) {
            return $existing;
        }

        $referralId = $this->create([
            'referrer_user_id' => $referrer['id'],
            'referred_user_id' => $referredUserId,
            'referral_code' => $referrer['referral_code'],
            'referred_email' => $referredEmail ?: null,
            'status' => 'converted',
            'converted_at' => date('Y-m-d H:i:s'),
        ]);

        (new AnalyticsEvent())->track('referral_converted', $referrer['id'], null, [
            'referral_id' => $referralId,
            'referred_user_id' => $referredUserId,
        ]);

        return $this->find($referralId);
    }

    public function getWithUsers($id) {
        return $this->db->fetch(
            "SELECT r.*,
                    ref.first_name AS referrer_first_name,
                    ref.last_name AS referrer_last_name,
                    ref.email AS referrer_email,
                    new_user.first_name AS referred_first_name,
                    new_user.last_name AS referred_last_name,
                    new_user.email AS referred_user_email
             FROM {$this->table} r
             JOIN users ref ON r.referrer_user_id = ref.id
             LEFT JOIN users new_user ON r.referred_user_id = new_user.id
             WHERE r.id = ?
             LIMIT 1",
            [$id]
        );
    }

    public function getStats() {
        $rows = $this->db->fetchAll(
            "SELECT status, COUNT(*) AS total
             FROM {$this->table}
             GROUP BY status"
        );

        $stats = [
            'registered' => 0,
            'converted' => 0,
            'credited' => 0,
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
            "SELECT r.*,
                    CONCAT(ref.first_name, ' ', ref.last_name) AS referrer_name,
                    CONCAT(new_user.first_name, ' ', new_user.last_name) AS referred_name,
                    COALESCE(new_user.email, r.referred_email) AS referred_email
             FROM {$this->table} r
             JOIN users ref ON r.referrer_user_id = ref.id
             LEFT JOIN users new_user ON r.referred_user_id = new_user.id
             ORDER BY r.created_at DESC
             LIMIT " . (int)$limit
        );
    }

    public function normalizeCode($code) {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', trim($code)));
    }
}
