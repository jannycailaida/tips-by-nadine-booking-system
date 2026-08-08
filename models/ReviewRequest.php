<?php
/**
 * Review Request Model
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseModel.php';

class ReviewRequest extends BaseModel {
    protected $table = 'review_requests';

    public function createForBooking($bookingId, $expiresInDays = 14) {
        $existing = $this->getByBooking($bookingId);
        if ($existing) {
            $existing['is_new'] = false;
            return $existing;
        }

        $booking = $this->db->fetch(
            "SELECT b.*, u.id AS user_id
             FROM bookings b
             JOIN users u ON b.user_id = u.id
             WHERE b.id = ?
             LIMIT 1",
            [$bookingId]
        );

        if (!$booking) {
            return null;
        }

        $requestId = $this->create([
            'booking_id' => $bookingId,
            'user_id' => $booking['user_id'],
            'token' => $this->generateUniqueToken(),
            'status' => 'pending',
            'expires_at' => date('Y-m-d H:i:s', strtotime('+' . (int)$expiresInDays . ' days')),
        ]);

        $request = $this->getDetailsById($requestId);
        if ($request) {
            $request['is_new'] = true;
        }

        return $request;
    }

    public function getByBooking($bookingId) {
        return $this->db->fetch(
            "SELECT rr.*, b.booking_date, b.nail_design_id,
                    u.first_name, u.last_name, u.email,
                    s.name AS service_name,
                    nd.name AS design_name,
                    ts.start_time, ts.end_time
             FROM {$this->table} rr
             JOIN bookings b ON rr.booking_id = b.id
             JOIN users u ON rr.user_id = u.id
             JOIN services s ON b.service_id = s.id
             LEFT JOIN nail_designs nd ON b.nail_design_id = nd.id
             JOIN time_slots ts ON b.time_slot_id = ts.id
             WHERE rr.booking_id = ?
             LIMIT 1",
            [$bookingId]
        );
    }

    public function getDetailsById($id) {
        return $this->db->fetch(
            "SELECT rr.*, b.booking_date, b.nail_design_id,
                    u.first_name, u.last_name, u.email,
                    s.name AS service_name,
                    nd.name AS design_name,
                    ts.start_time, ts.end_time
             FROM {$this->table} rr
             JOIN bookings b ON rr.booking_id = b.id
             JOIN users u ON rr.user_id = u.id
             JOIN services s ON b.service_id = s.id
             LEFT JOIN nail_designs nd ON b.nail_design_id = nd.id
             JOIN time_slots ts ON b.time_slot_id = ts.id
             WHERE rr.id = ?
             LIMIT 1",
            [$id]
        );
    }

    public function findByToken($token) {
        $token = trim($token);
        if ($token === '') {
            return null;
        }

        return $this->db->fetch(
            "SELECT rr.*, b.booking_date, b.nail_design_id,
                    u.first_name, u.last_name, u.email,
                    s.name AS service_name,
                    nd.name AS design_name,
                    ts.start_time, ts.end_time
             FROM {$this->table} rr
             JOIN bookings b ON rr.booking_id = b.id
             JOIN users u ON rr.user_id = u.id
             JOIN services s ON b.service_id = s.id
             LEFT JOIN nail_designs nd ON b.nail_design_id = nd.id
             JOIN time_slots ts ON b.time_slot_id = ts.id
             WHERE rr.token = ?
             LIMIT 1",
            [$token]
        );
    }

    public function isExpired($request) {
        return !empty($request['expires_at']) && strtotime($request['expires_at']) < time();
    }

    public function markOpened($id) {
        return $this->db->update($this->table, [
            'status' => 'opened',
            'opened_at' => date('Y-m-d H:i:s'),
        ], 'id = ? AND status = ?', [$id, 'pending']);
    }

    public function markSubmitted($id, $reviewId) {
        return $this->db->update($this->table, [
            'review_id' => $reviewId,
            'status' => 'submitted',
            'submitted_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);
    }

    public function markExpired($id) {
        return $this->db->update($this->table, [
            'status' => 'expired',
        ], 'id = ? AND status IN (?, ?)', [$id, 'pending', 'opened']);
    }

    public function markSent($id) {
        return $this->db->update($this->table, [
            'sent_at' => date('Y-m-d H:i:s'),
        ], 'id = ? AND sent_at IS NULL', [$id]);
    }

    public function getStats() {
        $rows = $this->db->fetchAll(
            "SELECT status, COUNT(*) AS total
             FROM {$this->table}
             GROUP BY status"
        );

        $stats = [
            'pending' => 0,
            'opened' => 0,
            'submitted' => 0,
            'expired' => 0,
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
            "SELECT rr.*, b.booking_date,
                    CONCAT(u.first_name, ' ', u.last_name) AS client_name,
                    u.email,
                    s.name AS service_name,
                    ts.start_time
             FROM {$this->table} rr
             JOIN bookings b ON rr.booking_id = b.id
             JOIN users u ON rr.user_id = u.id
             JOIN services s ON b.service_id = s.id
             JOIN time_slots ts ON b.time_slot_id = ts.id
             ORDER BY rr.created_at DESC
             LIMIT " . (int)$limit
        );
    }

    private function generateUniqueToken() {
        do {
            $token = bin2hex(random_bytes(32));
            $exists = $this->db->fetch("SELECT id FROM {$this->table} WHERE token = ? LIMIT 1", [$token]);
        } while ($exists);

        return $token;
    }
}
