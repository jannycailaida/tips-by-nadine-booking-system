-- ============================================================
-- Tier 2 — Marketing infrastructure
-- Tips by Nadine Booking System
-- ------------------------------------------------------------
-- 1) leads            : landing-page email capture (retargeting list)
-- 2) analytics_events : lightweight funnel event log so future
--                       marketing decisions have numbers
-- ============================================================

CREATE TABLE IF NOT EXISTS leads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL,
    user_id INT UNSIGNED NULL,
    source VARCHAR(50) NOT NULL DEFAULT 'landing' COMMENT 'Where the lead came from e.g. landing',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_lead_email (email)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS analytics_events (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event VARCHAR(50) NOT NULL COMMENT 'booking_started|booking_completed|etc',
    user_id INT UNSIGNED NULL,
    booking_id INT UNSIGNED NULL,
    meta TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_event_name (event),
    KEY idx_event_created (created_at)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;