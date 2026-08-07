<?php
/**
 * Base Model Class
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/../includes/Database.php';

abstract class BaseModel {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find($id) {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function findAll($conditions = [], $orderBy = 'id DESC') {
        $where = '';
        $params = [];
        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $key => $value) {
                $whereParts[] = "{$key} = ?";
                $params[] = $value;
            }
            $where = 'WHERE ' . implode(' AND ', $whereParts);
        }
        return $this->db->fetchAll("SELECT * FROM {$this->table} {$where} ORDER BY {$orderBy}", $params);
    }

    public function create($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update($this->table, $data, 'id = ?', [$id]);
    }

    public function delete($id) {
        return $this->db->delete($this->table, 'id = ?', [$id]);
    }

    public function count($conditions = []) {
        $where = '';
        $params = [];
        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $key => $value) {
                $whereParts[] = "{$key} = ?";
                $params[] = $value;
            }
            $where = 'WHERE ' . implode(' AND ', $whereParts);
        }
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM {$this->table} {$where}", $params);
        return $result['count'];
    }
}