<?php
/**
 * User Model
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';

    public function findByEmail($email) {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE email = ?", [$email]);
    }

    public function createUser($email, $password, $firstName, $lastName, $phone = null) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        return $this->create([
            'email' => $email,
            'password_hash' => $passwordHash,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
        ]);
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
}