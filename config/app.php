<?php
/**
 * Application Configuration
 * Tips by Nadine Booking System
 */

return [
    'app' => [
        'name' => 'Tips by Nadine',
        'url' => 'http://localhost/tips-by-nadine-booking-system',
        'timezone' => 'Asia/Manila',
        'debug' => true,
    ],
    'mail' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'username' => '', // Configure with your email
        'password' => '', // Configure with your app password
        'encryption' => 'tls',
        'from_email' => 'noreply@tipsbynadine.com',
        'from_name' => 'Tips by Nadine',
    ],
    'ai' => [
        'enabled' => true,
        'api_endpoint' => '', // Configure with AI service endpoint
        'api_key' => '', // Configure with AI service key
    ],
    'upload' => [
        'path' => __DIR__ . '/../uploads',
        'max_size' => 5 * 1024 * 1024, // 5MB
        'allowed_types' => ['image/jpeg', 'image/png', 'image/webp'],
    ],
    'session' => [
        'lifetime' => 3600,
        'name' => 'tips_by_nadine_session',
    ],
];