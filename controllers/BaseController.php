<?php
/**
 * Base Controller
 * Tips by Nadine Booking System
 */

require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/helpers.php';

abstract class BaseController {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected function view($template, $data = []) {
        $data['content'] = $this->renderView($template, $data);
        extract($data);
        require __DIR__ . "/../views/layout.php";
    }

    protected function renderAdminView($template, $data = []) {
        $data['content'] = $this->renderView($template, $data);
        extract($data);
        require __DIR__ . "/../views/admin/layout.php";
    }

    protected function renderStandalone($template, $data = []) {
        extract($data);
        require __DIR__ . "/../views/{$template}.php";
    }

    protected function renderView($template, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . "/../views/{$template}.php";
        return ob_get_clean();
    }

    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url) {
        // External absolute URLs pass through untouched; everything else is
        // relative to the app base URL (works when hosted in a subdirectory).
        if (preg_match('#^https?://#i', $url)) {
            header("Location: {$url}");
            exit;
        }
        header("Location: " . base_url($url));
        exit;
    }

    protected function flash($type, $message) {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
}