<?php
/**
 * Admin Model
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseModel.php';

class Admin extends BaseModel {
    protected $table = 'admins';

    public function findByEmail($email) {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE email = ?", [$email]);
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public function getDashboardStats() {
        $stats = [];
        $stats['total_bookings'] = $this->db->fetch("SELECT COUNT(*) as count FROM bookings")['count'];
        $stats['pending_bookings'] = $this->db->fetch("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'")['count'];
        $stats['confirmed_bookings'] = $this->db->fetch("SELECT COUNT(*) as count FROM bookings WHERE status = 'confirmed'")['count'];
        $stats['total_users'] = $this->db->fetch("SELECT COUNT(*) as count FROM users")['count'];
        $stats['total_designs'] = $this->db->fetch("SELECT COUNT(*) as count FROM nail_designs WHERE is_active = 1")['count'];
        return $stats;
    }

    public function getRecentBookings($limit = 10) {
        return $this->db->fetchAll("
            SELECT b.*, u.first_name, u.last_name, u.email, u.phone,
                   s.name as service_name, s.duration_minutes,
                   nd.name as design_name, nd.image_path as design_image,
                   ts.day_of_week, ts.start_time, ts.end_time
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN services s ON b.service_id = s.id
            LEFT JOIN nail_designs nd ON b.nail_design_id = nd.id
            JOIN time_slots ts ON b.time_slot_id = ts.id
            ORDER BY b.created_at DESC
            LIMIT ?
        ", [$limit]);
    }
}