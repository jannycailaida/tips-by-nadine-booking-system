<?php
/**
 * AnalyticsEvent Model
 * Tips by Nadine Booking System
 *
 * Lightweight funnel event log. Every meaningful marketing step writes a
 * tiny row here — booking_started → booking_completed, cancellations,
 * leads captured — so future decisions have real numbers instead of vibes.
 *
 * Event names (kept lowercase, underscore-separated):
 *   booking_started   — visitor reaches the booking form
 *   booking_completed — booking successfully created
 *   booking_confirmed — admin confirms a pending booking
 *   booking_cancelled — user/admin cancels a booking
 *   lead_captured     — email captured on the landing page
 */

require_once __DIR__ . '/BaseModel.php';

class AnalyticsEvent extends BaseModel {
    protected $table = 'analytics_events';

    /**
     * Write one event. meta is optional extra context (e.g. {'source': 'landing'}).
     */
    public function track($event, $userId = null, $bookingId = null, array $meta = []) {
        $this->db->insert($this->table, [
            'event'      => $event,
            'user_id'    => $userId ?: null,
            'booking_id' => $bookingId ?: null,
            'meta'       => $meta ? json_encode($meta, JSON_UNESCAPED_SLASHES) : null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Total occurrences of one event (all time).
     */
    public function countEvent($event) {
        return $this->db->fetch(
            "SELECT COUNT(*) AS count FROM {$this->table} WHERE event = ?",
            [$event]
        )['count'];
    }

    /**
     * Counts for the funnel dashboard.
     */
    public function getFunnelCounts() {
        $events = ['booking_started', 'booking_completed', 'booking_confirmed', 'booking_cancelled', 'lead_captured'];
        $counts = ['all_events' => 0];
        foreach ($events as $event) {
            $counts[$event] = $this->countEvent($event);
            $counts['all_events'] += $counts[$event];
        }
        // Booking→completion conversion as a clean percentage (0 when unused).
        if ($counts['booking_started'] > 0) {
            $counts['completion_rate'] = round(
                $counts['booking_completed'] / $counts['booking_started'] * 100, 1
            );
        } else {
            $counts['completion_rate'] = 0;
        }
        return $counts;
    }

    /**
     * Recent events joined to users for a readable admin trail.
     */
    public function getRecent($limit = 40) {
        return $this->db->fetchAll("
            SELECT e.*, u.first_name, u.last_name, u.email
            FROM {$this->table} e
            LEFT JOIN users u ON e.user_id = u.id
            ORDER BY e.created_at DESC, e.id DESC
            LIMIT " . (int)$limit
        );
    }

    /**
     * Events per day for the trailing N days (including empty days),
     * newest day first — used for a lightweight activity trend.
     */
    public function getDailyCounts($days = 14) {
        $days = (int)$days;
        // Rows are stamped with PHP's date() (same clock the rest of the app
        // uses), so window the query from PHP dates too — mixing in MySQL's
        // NOW() would skew a day when the two clocks differ.
        $start = date('Y-m-d', strtotime('-' . ($days - 1) . ' days')) . ' 00:00:00';
        $end = date('Y-m-d') . ' 23:59:59';
        $rows = $this->db->fetchAll("
            SELECT DATE(created_at) AS day, COUNT(*) AS count
            FROM {$this->table}
            WHERE created_at >= ? AND created_at <= ?
            GROUP BY DATE(created_at)
            ORDER BY day ASC
        ", [$start, $end]);
        $byDay = [];
        foreach ($rows as $row) {
            $byDay[$row['day']] = (int)$row['count'];
        }
        $series = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime("-{$i} days"));
            $series[] = [
                'day'     => $day,
                'label'   => date('M j', strtotime($day)),
                'is_today' => $day === date('Y-m-d'),
                'count'   => $byDay[$day] ?? 0,
            ];
        }
        return $series;
    }
}