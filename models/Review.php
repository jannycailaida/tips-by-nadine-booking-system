<?php
/**
 * Review Model
 * Tips by Nadine Booking System
 *
 * Client testimonials — collected from real appointments and surfaced as
 * social proof on the landing page and each design detail page.
 */

require_once __DIR__ . '/BaseModel.php';

class Review extends BaseModel {
    protected $table = 'reviews';

    /**
     * Active reviews visible to the public, most recent first.
     */
    public function getActive() {
        return $this->findAll(['is_active' => true], 'created_at DESC');
    }

    /**
     * Featured reviews for the landing page. Prefers higher-rated entries so
     * the organic and polished voice shine; falls back to newest when few.
     */
    public function getFeatured($limit = 6) {
        return $this->db->fetchAll("
            SELECT r.*, d.name AS design_name, c.name AS category_name
            FROM {$this->table} r
            LEFT JOIN nail_designs d ON r.design_id = d.id
            LEFT JOIN categories c ON d.category_id = c.id
            WHERE r.is_active = 1
            ORDER BY r.rating DESC, r.created_at DESC
            LIMIT " . (int)$limit
        );
    }

    /**
     * Active reviews attached to one design (shown on the design detail page).
     */
    public function getForDesign($designId, $limit = 3) {
        return $this->db->fetchAll("
            SELECT r.*, d.name AS design_name
            FROM {$this->table} r
            LEFT JOIN nail_designs d ON r.design_id = d.id
            WHERE r.is_active = 1 AND r.design_id = ?
            ORDER BY r.created_at DESC
            LIMIT " . (int)$limit
        , [$designId]);
    }

    /**
     * All reviews (active or not) for the admin panel, joined to their design.
     */
    public function getAllWithDesign() {
        return $this->db->fetchAll("
            SELECT r.*, d.name AS design_name
            FROM {$this->table} r
            LEFT JOIN nail_designs d ON r.design_id = d.id
            ORDER BY r.created_at DESC
        ");
    }

    /**
     * Average star rating across active reviews — feeds the aggregate "5k+
     * happy clients" claim with an earned number where reviews exist.
     */
    public function getAverageRating() {
        $result = $this->db->fetch("SELECT AVG(rating) AS avg_rating, COUNT(*) AS review_count FROM {$this->table} WHERE is_active = 1");
        return [round((float)($result['avg_rating'] ?? 0), 1), (int)($result['review_count'] ?? 0)];
    }
}