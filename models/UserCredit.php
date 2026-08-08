<?php
/**
 * User Credit Model
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseModel.php';

class UserCredit extends BaseModel {
    protected $table = 'user_credits';

    public function recordCredit($userId, $type, $description, $amount = 0, $referralId = null, $reviewRequestId = null, $adminId = null) {
        if (!$userId || trim($description) === '') {
            return null;
        }

        $allowedTypes = ['referral', 'review', 'goodwill'];
        if (!in_array($type, $allowedTypes)) {
            $type = 'goodwill';
        }

        return $this->create([
            'user_id' => $userId,
            'referral_id' => $referralId ?: null,
            'review_request_id' => $reviewRequestId ?: null,
            'credit_type' => $type,
            'description' => $description,
            'amount' => (float)$amount,
            'status' => 'pending',
            'created_by_admin_id' => $adminId ?: null,
        ]);
    }

    public function getStats() {
        $rows = $this->db->fetchAll(
            "SELECT status, COUNT(*) AS total
             FROM {$this->table}
             GROUP BY status"
        );

        $stats = [
            'pending' => 0,
            'approved' => 0,
            'redeemed' => 0,
            'void' => 0,
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
            "SELECT uc.*, CONCAT(u.first_name, ' ', u.last_name) AS client_name, u.email
             FROM {$this->table} uc
             JOIN users u ON uc.user_id = u.id
             ORDER BY uc.created_at DESC
             LIMIT " . (int)$limit
        );
    }
}
