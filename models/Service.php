<?php
/**
 * Service Model
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/BaseModel.php';

class Service extends BaseModel {
    protected $table = 'services';

    public function getActive() {
        return $this->findAll(['is_active' => true], 'name');
    }

    public function getAll() {
        return $this->findAll([], 'name');
    }
}