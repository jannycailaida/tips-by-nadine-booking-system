<?php
/**
 * Lead Model
 * Tips by Nadine Booking System
 *
 * Landing-page email captures. One row per unique email; repeats re-use the
 * row (their user_id/source are updated) so the contact list stays clean.
 */

require_once __DIR__ . '/BaseModel.php';

class Lead extends BaseModel {
    protected $table = 'leads';

    public function getByEmail($email) {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1",
            [$email]
        );
    }

    /**
     * Insert a new lead. Callers should check getByEmail() first.
     */
    public function capture($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = $data['created_at'];
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update an existing lead's provenance (source/user) without a new row.
     */
    public function refresh($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->update($id, $data);
    }

    public function getAll() {
        return $this->findAll([], 'created_at DESC, id DESC');
    }

    public function countTotal() {
        return $this->count();
    }
}