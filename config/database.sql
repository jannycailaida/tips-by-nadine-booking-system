-- Database Schema for Tips by Nadine Booking System
-- MySQL Database

CREATE DATABASE IF NOT EXISTS tips_by_nadine CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tips_by_nadine;

-- Users table (clients)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
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