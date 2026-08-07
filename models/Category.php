<?php
/**
 * Category Model
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseModel.php';

class Category extends BaseModel {
    protected $table = 'categories';

    public function getActive() {
        // Categories are always active (schema has no is_active flag on them)
        return $this->findAll([], 'display_order, name');
    }

    public function getAll() {
        return $this->findAll([], 'display_order, name');
    }
}