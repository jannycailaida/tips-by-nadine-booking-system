<?php
/**
 * Booking Model
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseModel.php';

class Booking extends BaseModel {
    protected $table = 'bookings';

    public function createBooking($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->table, $data);
    }

    public function getUserBookings($userId) {
        return $this->db->fetchAll("
            SELECT b.*, s.name as service_name, s.duration_minutes, s.price as service_price,
                   nd.name as design_name, nd.price as design_price, nd.image_path as design_image,
                   ts.day_of_week, ts.start_time, ts.end_time
            FROM {$this->table} b
            JOIN services s ON b.service_id = s.id
            LEFT JOIN nail_designs nd ON b.nail_design_id = nd.id
            JOIN time_slots ts ON b.time_slot_id = ts.id
            WHERE b.user_id = ?
            ORDER BY b.booking_date DESC, ts.start_time ASC
        ", [$userId]);
    }

    public function getAllBookings($status = null) {
        $where = '';
        $params = [];
        if ($status) {
            $where = 'WHERE b.status = ?';
            $params[] = $status;
        }
        return $this->db->fetchAll("
            SELECT b.*, u.first_name, u.last_name, u.email, u.phone,
                   s.name as service_name, s.duration_minutes,
                   nd.name as design_name, nd.image_path as design_image,
                   ts.day_of_week, ts.start_time, ts.end_time
            FROM {$this->table} b
            JOIN users u ON b.user_id = u.id
            JOIN services s ON b.service_id = s.id
            LEFT JOIN nail_designs nd ON b.nail_design_id = nd.id
            JOIN time_slots ts ON b.time_slot_id = ts.id
            {$where}
            ORDER BY b.booking_date DESC, ts.start_time ASC
        ", $params);
    }

    public function updateStatus($bookingId, $status) {
        return $this->update($bookingId, ['status' => $status]);
    }

    public function getBookingDetails($bookingId) {
        return $this->db->fetch("
            SELECT b.*, u.first_name, u.last_name, u.email, u.phone,
                   s.name as service_name, s.duration_minutes, s.price as service_price,
                   nd.name as design_name, nd.price as design_price, nd.image_path as design_image,
                   ts.day_of_week, ts.start_time, ts.end_time
            FROM {$this->table} b
            JOIN users u ON b.user_id = u.id
            JOIN services s ON b.service_id = s.id
            LEFT JOIN nail_designs nd ON b.nail_design_id = nd.id
            JOIN time_slots ts ON b.time_slot_id = ts.id
            WHERE b.id = ?
        ", [$bookingId]);
    }

    public function checkAvailability($date, $timeSlotId) {
        $result = $this->db->fetch("
            SELECT COUNT(*) as count FROM {$this->table}
            WHERE booking_date = ? AND time_slot_id = ? AND status IN ('pending', 'confirmed')
        ", [$date, $timeSlotId]);
        return $result['count'] === 0;
    }
}