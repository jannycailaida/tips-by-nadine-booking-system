<?php
/**
 * NailDesign Model
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseModel.php';

class NailDesign extends BaseModel {
    protected $table = 'nail_designs';

    public function getWithCategory($conditions = []) {
        $where = '';
        $params = [];
        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $key => $value) {
                $whereParts[] = "nd.{$key} = ?";
                $params[] = $value;
            }
            $where = 'WHERE ' . implode(' AND ', $whereParts);
        }
        return $this->db->fetchAll("
            SELECT nd.*, c.name as category_name
            FROM {$this->table} nd
            LEFT JOIN categories c ON nd.category_id = c.id
            {$where}
            ORDER BY c.display_order, nd.name
        ", $params);
    }

    public function getByCategory($categoryId) {
        return $this->getWithCategory(['category_id' => $categoryId, 'is_active' => true]);
    }

    public function getActive() {
        return $this->getWithCategory(['is_active' => true]);
    }
}