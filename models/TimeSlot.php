<?php
/**
 * TimeSlot Model
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseModel.php';

class TimeSlot extends BaseModel {
    protected $table = 'time_slots';

    public function getActive() {
        return $this->findAll(['is_active' => true], 'FIELD(day_of_week, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"), start_time');
    }

    public function getByDay($dayOfWeek) {
        return $this->findAll(['day_of_week' => $dayOfWeek, 'is_active' => true], 'start_time');
    }

    public function getAvailableSlots($date) {
        $dayOfWeek = date('l', strtotime($date));
        $bookedSlots = $this->db->fetchAll("
            SELECT time_slot_id FROM bookings
            WHERE booking_date = ? AND status IN ('pending', 'confirmed')
        ", [$date]);

        $bookedIds = array_column($bookedSlots, 'time_slot_id');
        $allSlots = $this->getByDay($dayOfWeek);

        return array_filter($allSlots, function($slot) use ($bookedIds) {
            return !in_array($slot['id'], $bookedIds);
        });
    }
}