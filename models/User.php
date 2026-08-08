<?php
/**
 * User Model
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';
    private $hasReferralCodeColumn;

    public function findByEmail($email) {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE email = ?", [$email]);
    }

    public function findByReferralCode($code) {
        if (!$this->hasReferralCodeColumn()) {
            return null;
        }

        $code = $this->normalizeReferralCode($code);
        if ($code === '') {
            return null;
        }

        return $this->db->fetch("SELECT * FROM {$this->table} WHERE referral_code = ? LIMIT 1", [$code]);
    }

    public function createUser($email, $password, $firstName, $lastName, $phone = null) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $data = [
            'email' => $email,
            'password_hash' => $passwordHash,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
        ];

        if ($this->hasReferralCodeColumn()) {
            $data['referral_code'] = $this->generateUniqueReferralCode($firstName, $lastName);
        }

        return $this->create($data);
    }

    public function ensureReferralCode($userId) {
        if (!$this->hasReferralCodeColumn()) {
            return null;
        }

        $user = $this->find($userId);
        if (!$user) {
            return null;
        }

        if (!empty($user['referral_code'])) {
            return $user['referral_code'];
        }

        $code = $this->generateUniqueReferralCode($user['first_name'], $user['last_name']);
        $this->db->update($this->table, ['referral_code' => $code], 'id = ?', [$userId]);
        return $code;
    }

    public function getReferralLink($userId) {
        $code = $this->ensureReferralCode($userId);
        if (!$code) {
            return null;
        }

        $config = require __DIR__ . '/../config/app.php';
        return rtrim($config['app']['url'], '/') . '/register.php?ref=' . urlencode($code);
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public function getBookings($userId) {
        return $this->db->fetchAll("
            SELECT b.*, s.name as service_name, s.duration_minutes, s.price as service_price,
                   nd.name as design_name, nd.price as design_price, nd.image_path as design_image,
                   ts.day_of_week, ts.start_time, ts.end_time
            FROM bookings b
            JOIN services s ON b.service_id = s.id
            LEFT JOIN nail_designs nd ON b.nail_design_id = nd.id
            JOIN time_slots ts ON b.time_slot_id = ts.id
            WHERE b.user_id = ?
            ORDER BY b.booking_date DESC, ts.start_time ASC
        ", [$userId]);
    }

    private function generateUniqueReferralCode($firstName = '', $lastName = '') {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $firstName . $lastName), 0, 3));
        if (strlen($prefix) < 3) {
            $prefix = 'TBN';
        }

        do {
            $code = 'TBN' . $prefix . random_int(1000, 9999);
            $exists = $this->findByReferralCode($code);
        } while ($exists);

        return $code;
    }

    private function normalizeReferralCode($code) {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', trim($code)));
    }

    private function hasReferralCodeColumn() {
        if ($this->hasReferralCodeColumn !== null) {
            return $this->hasReferralCodeColumn;
        }

        $column = $this->db->fetch("SHOW COLUMNS FROM {$this->table} LIKE 'referral_code'");
        $this->hasReferralCodeColumn = (bool)$column;
        return $this->hasReferralCodeColumn;
    }
}
