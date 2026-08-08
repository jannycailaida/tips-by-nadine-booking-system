-- Tier 3 Marketing Growth Loop Schema
-- Tips by Nadine Booking System
-- Target database: salonDB

USE salonDB;

-- Add a shareable referral code to client accounts without breaking existing installs.
SET @schema_name := DATABASE();
SET @has_referral_code := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @schema_name
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'referral_code'
);
SET @sql := IF(@has_referral_code = 0,
    'ALTER TABLE users ADD COLUMN referral_code VARCHAR(32) NULL AFTER phone',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE users
SET referral_code = CONCAT('TBN', UPPER(SUBSTRING(MD5(CONCAT(id, email, created_at)), 1, 8)))
WHERE referral_code IS NULL OR referral_code = '';

SET @has_referral_index := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = @schema_name
      AND TABLE_NAME = 'users'
      AND INDEX_NAME = 'unique_referral_code'
);
SET @sql := IF(@has_referral_index = 0,
    'ALTER TABLE users ADD UNIQUE KEY unique_referral_code (referral_code)',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tokenized review links sent after completed appointments.
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

-- Safe queued email sender for Tier 3 review and referral follow-ups.
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

-- Referral conversion tracking. Credits remain manual/administrative, not payment automation.
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

-- Manual goodwill/referral/review credits for admin visibility only.
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
