-- Database Schema for Tips by Nadine Booking System
-- MySQL Database

CREATE DATABASE IF NOT EXISTS salonDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE salonDB;

-- Users table (clients)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    referral_code VARCHAR(32) NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Admin users table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role ENUM('owner', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Nail design categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Nail designs table
CREATE TABLE IF NOT EXISTS nail_designs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Services table (nail services offered)
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    duration_minutes INT NOT NULL DEFAULT 60,
    price DECIMAL(10,2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Time slots for booking
CREATE TABLE IF NOT EXISTS time_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    UNIQUE KEY unique_slot (day_of_week, start_time, end_time)
);

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    nail_design_id INT NULL,
    booking_date DATE NOT NULL,
    time_slot_id INT NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    -- Only pending/confirmed bookings share a slot key; cancelled/completed rows
    -- get NULL so multiple cancelled bookings for the same slot do not collide.
    active_slot_key VARCHAR(20) GENERATED ALWAYS AS (
        IF(status IN ('pending', 'confirmed'), CONCAT(booking_date, '-', time_slot_id), NULL)
    ) STORED,
    notes TEXT,
    reference_image_path VARCHAR(500) NULL,
    ai_recommendations JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT,
    FOREIGN KEY (nail_design_id) REFERENCES nail_designs(id) ON DELETE SET NULL,
    FOREIGN KEY (time_slot_id) REFERENCES time_slots(id) ON DELETE RESTRICT,
    UNIQUE KEY unique_active_slot (active_slot_key)
);

-- Client reviews (social proof, surfaced on the landing page)
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(150) NOT NULL,
    rating TINYINT NOT NULL DEFAULT 5 COMMENT '1 to 5 stars',
    review_text TEXT NOT NULL,
    service_name VARCHAR(150) NULL COMMENT 'Service the client received (denormalised label)',
    design_id INT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (design_id) REFERENCES nail_designs(id) ON DELETE SET NULL
);

-- Email notifications log
CREATE TABLE IF NOT EXISTS email_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_email VARCHAR(255) NOT NULL,
    subject VARCHAR(500) NOT NULL,
    body TEXT NOT NULL,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    error_message TEXT NULL,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Landing-page lead capture (Tier 2 marketing baseline)
CREATE TABLE IF NOT EXISTS leads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL,
    user_id INT UNSIGNED NULL,
    source VARCHAR(50) NOT NULL DEFAULT 'landing',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_lead_email (email)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Lightweight marketing and booking analytics events
CREATE TABLE IF NOT EXISTS analytics_events (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event VARCHAR(50) NOT NULL,
    user_id INT UNSIGNED NULL,
    booking_id INT UNSIGNED NULL,
    meta TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_event_name (event),
    KEY idx_event_created (created_at)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Tokenized review links sent after completed appointments (Tier 3)
CREATE TABLE IF NOT EXISTS review_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    user_id INT NOT NULL,
    review_id INT NULL,
    token CHAR(64) NOT NULL UNIQUE,
    status ENUM('pending', 'opened', 'submitted', 'expired') NOT NULL DEFAULT 'pending',
    expires_at DATETIME NULL,
    sent_at DATETIME NULL,
    opened_at DATETIME NULL,
    submitted_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_review_request_booking (booking_id),
    KEY idx_review_request_token (token),
    KEY idx_review_request_status (status),
    KEY idx_review_request_expires (expires_at),
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Safe queued email sender for Tier 3 review and referral follow-ups
CREATE TABLE IF NOT EXISTS email_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_email VARCHAR(255) NOT NULL,
    recipient_name VARCHAR(150) NULL,
    subject VARCHAR(500) NOT NULL,
    body MEDIUMTEXT NOT NULL,
    template VARCHAR(80) NOT NULL DEFAULT 'custom',
    related_id INT NULL,
    status ENUM('pending', 'processing', 'sent', 'failed', 'cancelled') NOT NULL DEFAULT 'pending',
    scheduled_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    sent_at DATETIME NULL,
    attempts INT NOT NULL DEFAULT 0,
    last_error TEXT NULL,
    meta TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_email_queue_due (status, scheduled_at),
    KEY idx_email_queue_template (template),
    KEY idx_email_queue_related (template, related_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Referral conversion tracking. Credits remain manual/administrative, not payment automation (Tier 3)
CREATE TABLE IF NOT EXISTS referrals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    referrer_user_id INT NOT NULL,
    referred_user_id INT NULL,
    referral_code VARCHAR(32) NOT NULL,
    referred_email VARCHAR(255) NULL,
    status ENUM('registered', 'converted', 'credited') NOT NULL DEFAULT 'converted',
    converted_at DATETIME NULL,
    credited_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_referred_user (referred_user_id),
    KEY idx_referrals_referrer (referrer_user_id),
    KEY idx_referrals_code (referral_code),
    FOREIGN KEY (referrer_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (referred_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Manual goodwill/referral/review credits for admin visibility only (Tier 3)
CREATE TABLE IF NOT EXISTS user_credits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    referral_id INT NULL,
    review_request_id INT NULL,
    credit_type ENUM('referral', 'review', 'goodwill') NOT NULL DEFAULT 'goodwill',
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('pending', 'approved', 'redeemed', 'void') NOT NULL DEFAULT 'pending',
    created_by_admin_id INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_user_credits_user (user_id),
    KEY idx_user_credits_type (credit_type),
    KEY idx_user_credits_status (status),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (referral_id) REFERENCES referrals(id) ON DELETE SET NULL,
    FOREIGN KEY (review_request_id) REFERENCES review_requests(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by_admin_id) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Insert default admin (password: admin123 - change in production!)
INSERT INTO admins (email, password_hash, first_name, last_name, role) VALUES
('owner@tipsbynadine.com', '$2y$10$SPvWI6SX8sWzwNacQ8kD7eTbeEFtuKdgtk8Q60qX2M26m1H5hVD2m', 'Nadine', 'Owner', 'owner');

-- Insert default categories
INSERT INTO categories (name, description, display_order) VALUES
('Classic', 'Timeless and elegant nail designs', 1),
('French Tip', 'Classic french manicure variations', 2),
('Glitter & Shimmer', 'Sparkly and glamorous designs', 3),
('Floral', 'Flower and nature-inspired designs', 4),
('Geometric', 'Modern geometric patterns', 5),
('Seasonal', 'Holiday and seasonal specials', 6),
('Custom', 'Custom and personalized designs', 7);

-- Insert default services
INSERT INTO services (name, description, duration_minutes, price) VALUES
('Basic Manicure', 'Classic manicure with nail shaping and polish', 45, 25.00),
('Gel Manicure', 'Long-lasting gel polish application', 60, 35.00),
('Acrylic Full Set', 'Full acrylic nail extensions', 90, 55.00),
('Acrylic Fill', 'Acrylic nail maintenance fill', 60, 40.00),
('Nail Art Add-on', 'Custom nail art per nail', 15, 5.00),
('Pedicure', 'Classic pedicure with polish', 60, 40.00),
('Gel Pedicure', 'Gel polish on toes', 75, 50.00);

-- Insert default time slots (9 AM - 6 PM, 1-hour slots)
INSERT INTO time_slots (day_of_week, start_time, end_time) VALUES
('Monday', '09:00:00', '10:00:00'),
('Monday', '10:00:00', '11:00:00'),
('Monday', '11:00:00', '12:00:00'),
('Monday', '13:00:00', '14:00:00'),
('Monday', '14:00:00', '15:00:00'),
('Monday', '15:00:00', '16:00:00'),
('Monday', '16:00:00', '17:00:00'),
('Tuesday', '09:00:00', '10:00:00'),
('Tuesday', '10:00:00', '11:00:00'),
('Tuesday', '11:00:00', '12:00:00'),
('Tuesday', '13:00:00', '14:00:00'),
('Tuesday', '14:00:00', '15:00:00'),
('Tuesday', '15:00:00', '16:00:00'),
('Tuesday', '16:00:00', '17:00:00'),
('Wednesday', '09:00:00', '10:00:00'),
('Wednesday', '10:00:00', '11:00:00'),
('Wednesday', '11:00:00', '12:00:00'),
('Wednesday', '13:00:00', '14:00:00'),
('Wednesday', '14:00:00', '15:00:00'),
('Wednesday', '15:00:00', '16:00:00'),
('Wednesday', '16:00:00', '17:00:00'),
('Thursday', '09:00:00', '10:00:00'),
('Thursday', '10:00:00', '11:00:00'),
('Thursday', '11:00:00', '12:00:00'),
('Thursday', '13:00:00', '14:00:00'),
('Thursday', '14:00:00', '15:00:00'),
('Thursday', '15:00:00', '16:00:00'),
('Thursday', '16:00:00', '17:00:00'),
('Friday', '09:00:00', '10:00:00'),
('Friday', '10:00:00', '11:00:00'),
('Friday', '11:00:00', '12:00:00'),
('Friday', '13:00:00', '14:00:00'),
('Friday', '14:00:00', '15:00:00'),
('Friday', '15:00:00', '16:00:00'),
('Friday', '16:00:00', '17:00:00'),
('Saturday', '09:00:00', '10:00:00'),
('Saturday', '10:00:00', '11:00:00'),
('Saturday', '11:00:00', '12:00:00'),
('Saturday', '13:00:00', '14:00:00'),
('Saturday', '14:00:00', '15:00:00'),
('Saturday', '15:00:00', '16:00:00'),
('Saturday', '16:00:00', '17:00:00');