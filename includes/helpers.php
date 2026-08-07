<?php
/**
 * URL Helper
 * Tips by Nadine Booking System
 *
 * Generates base-path-aware URLs so the app works when served from a
 * subdirectory under the document root (e.g. http://localhost/tips-by-nadine-booking-system/).
 *
 * The base path is derived from config/app.php -> app.url, so changing the
 * deployment URL (or setting up a virtual host) only requires updating that
 * one value.
 */

if (!function_exists('base_url')) {
    function base_url($path = '') {
        static $base = null;

        if ($base === null) {
            $config = require __DIR__ . '/../config/app.php';
            $urlPath = parse_url($config['app']['url'], PHP_URL_PATH);
            $base = rtrim((string)$urlPath, '/');
        }

        return $base . ($path !== '' ? '/' . ltrim($path, '/') : '');
    }
}
