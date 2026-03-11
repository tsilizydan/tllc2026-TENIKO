-- ============================================================
-- TENIKO - The Malagasy Language & Culture Encyclopedia
-- Database Schema v1.0
-- Charset: utf8mb4 / Collation: utf8mb4_unicode_ci
-- ============================================================

SET NAMES utf8mb4;
SET time_zone = '+03:00';
SET foreign_key_checks = 0;

CREATE DATABASE IF NOT EXISTS `teniko` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `teniko`;

-- ------------------------------------------------------------
-- USERS & ROLES
-- ------------------------------------------------------------

CREATE TABLE `users` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username`        VARCHAR(60)  NOT NULL UNIQUE,
    `email`           VARCHAR(191) NOT NULL UNIQUE,
    `password`        VARCHAR(255) NOT NULL,
    `display_name`    VARCHAR(100) DEFAULT NULL,
    `avatar`          VARCHAR(255) DEFAULT NULL,
    `bio`             TEXT         DEFAULT NULL,
    `role`            ENUM('admin','moderator','contributor','user') NOT NULL DEFAULT 'user',
    `status`          ENUM('active','suspended','banned','pending') NOT NULL DEFAULT 'pending',
    `email_verified_at` DATETIME  DEFAULT NULL,
    `email_verify_token` VARCHAR(100) DEFAULT NULL,
    `password_reset_token` VARCHAR(100) DEFAULT NULL,
    `password_reset_expires` DATETIME DEFAULT NULL,
    `reputation`      INT UNSIGNED NOT NULL DEFAULT 0,
    `last_login_at`   DATETIME     DEFAULT NULL,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME     DEFAULT NULL,
    `deleted_at`      DATETIME     DEFAULT NULL,
    INDEX `idx_email` (`email`),
    INDEX `idx_username` (`username`),
    INDEX `idx_role` (`role`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- DIALECTS
-- ------------------------------------------------------------

CREATE TABLE `dialects` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL,
    `code`        VARCHAR(20)  NOT NULL UNIQUE,
    `region`      VARCHAR(150) DEFAULT NULL,
    `description` TEXT         DEFAULT NULL,
    `lat`         DECIMAL(9,6) DEFAULT NULL,
    `lng`         DECIMAL(9,6) DEFAULT NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME     DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- WORDS (DICTIONARY)
-- ------------------------------------------------------------

CREATE TABLE `words` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `word`            VARCHAR(255) NOT NULL,
    `slug`            VARCHAR(300) NOT NULL UNIQUE,
    `pronunciation`   VARCHAR(255) DEFAULT NULL,
    `part_of_speech`  ENUM('noun','verb','adjective','adverb','pronoun','preposition','conjunction','interjection','article','numeral','other') DEFAULT NULL,
    `etymology`       TEXT         DEFAULT NULL,
    `notes`           TEXT         DEFAULT NULL,
    `status`          ENUM('published','draft','pending','rejected') NOT NULL DEFAULT 'pending',
    `featured`        TINYINT(1)   NOT NULL DEFAULT 0,
    `word_of_day_date` DATE        DEFAULT NULL,
    `view_count`      INT UNSIGNED NOT NULL DEFAULT 0,
    `created_by`      INT UNSIGNED DEFAULT NULL,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME     DEFAULT NULL,
    `deleted_at`      DATETIME     DEFAULT NULL,
    FULLTEXT INDEX `ft_word` (`word`),
    INDEX `idx_slug` (`slug`),
    INDEX `idx_status` (`status`),
    INDEX `idx_part_of_speech` (`part_of_speech`),
    INDEX `idx_word_of_day` (`word_of_day_date`),
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `definitions` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `word_id`    INT UNSIGNED NOT NULL,
    `lang`       CHAR(2)      NOT NULL DEFAULT 'mg' COMMENT 'mg=Malagasy, fr=French, en=English',
    `text`       TEXT         NOT NULL,
    `example`    TEXT         DEFAULT NULL,
    `sort_order` TINYINT      NOT NULL DEFAULT 0,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FULLTEXT INDEX `ft_definition` (`text`),
    FOREIGN KEY (`word_id`) REFERENCES `words`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `translations` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `word_id`    INT UNSIGNED NOT NULL,
    `lang`       CHAR(2)      NOT NULL DEFAULT 'fr',
    `translation` VARCHAR(500) NOT NULL,
    `sort_order` TINYINT      NOT NULL DEFAULT 0,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_word_lang` (`word_id`,`lang`),
    FOREIGN KEY (`word_id`) REFERENCES `words`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `word_relations` (
    `word_id`     INT UNSIGNED NOT NULL,
    `related_id`  INT UNSIGNED NOT NULL,
    `type`        ENUM('related','derived','compound','antonym','synonym') NOT NULL DEFAULT 'related',
    PRIMARY KEY (`word_id`,`related_id`,`type`),
    FOREIGN KEY (`word_id`)    REFERENCES `words`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`related_id`) REFERENCES `words`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `word_dialect_variants` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `word_id`     INT UNSIGNED NOT NULL,
    `dialect_id`  INT UNSIGNED NOT NULL,
    `variant`     VARCHAR(255) NOT NULL,
    `notes`       TEXT DEFAULT NULL,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`word_id`)    REFERENCES `words`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`dialect_id`) REFERENCES `dialects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- AUDIO FILES
-- ------------------------------------------------------------

CREATE TABLE `audio_files` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `entity_type`  ENUM('word','proverb') NOT NULL,
    `entity_id`    INT UNSIGNED NOT NULL,
    `dialect_id`   INT UNSIGNED DEFAULT NULL,
    `filename`     VARCHAR(255) NOT NULL,
    `duration_sec` SMALLINT UNSIGNED DEFAULT NULL,
    `uploaded_by`  INT UNSIGNED DEFAULT NULL,
    `status`       ENUM('published','pending','rejected') NOT NULL DEFAULT 'pending',
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_entity` (`entity_type`,`entity_id`),
    FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`dialect_id`)  REFERENCES `dialects`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- IMAGES
-- ------------------------------------------------------------

CREATE TABLE `images` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `entity_type` VARCHAR(50) DEFAULT NULL,
    `entity_id`   INT UNSIGNED DEFAULT NULL,
    `filename`    VARCHAR(255) NOT NULL,
    `alt`         VARCHAR(255) DEFAULT NULL,
    `caption`     TEXT DEFAULT NULL,
    `uploaded_by` INT UNSIGNED DEFAULT NULL,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_entity` (`entity_type`,`entity_id`),
    FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- PROVERBS
-- ------------------------------------------------------------

CREATE TABLE `proverbs` (
    `id`                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `text`                TEXT NOT NULL,
    `slug`                VARCHAR(350) NOT NULL UNIQUE,
    `transliteration`     TEXT DEFAULT NULL,
    `translation_fr`      TEXT DEFAULT NULL,
    `translation_en`      TEXT DEFAULT NULL,
    `meaning`             TEXT DEFAULT NULL,
    `cultural_explanation` TEXT DEFAULT NULL,
    `dialect_id`          INT UNSIGNED DEFAULT NULL,
    `status`              ENUM('published','draft','pending','rejected') NOT NULL DEFAULT 'pending',
    `proverb_of_day_date` DATE DEFAULT NULL,
    `view_count`          INT UNSIGNED NOT NULL DEFAULT 0,
    `created_by`          INT UNSIGNED DEFAULT NULL,
    `created_at`          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          DATETIME DEFAULT NULL,
    `deleted_at`          DATETIME DEFAULT NULL,
    FULLTEXT INDEX `ft_proverb` (`text`,`translation_fr`,`translation_en`),
    INDEX `idx_slug` (`slug`),
    INDEX `idx_status` (`status`),
    INDEX `idx_pod` (`proverb_of_day_date`),
    FOREIGN KEY (`dialect_id`)  REFERENCES `dialects`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`created_by`)  REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- ARTICLES & CULTURAL CONTENT
-- ------------------------------------------------------------

CREATE TABLE `categories` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(150) NOT NULL,
    `slug`        VARCHAR(200) NOT NULL UNIQUE,
    `type`        ENUM('article','cultural','dialect','other') NOT NULL DEFAULT 'article',
    `description` TEXT DEFAULT NULL,
    `sort_order`  INT NOT NULL DEFAULT 0,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `articles` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title`        VARCHAR(350) NOT NULL,
    `slug`         VARCHAR(400) NOT NULL UNIQUE,
    `excerpt`      TEXT DEFAULT NULL,
    `body`         LONGTEXT NOT NULL,
    `cover_image`  VARCHAR(255) DEFAULT NULL,
    `category_id`  INT UNSIGNED DEFAULT NULL,
    `type`         ENUM('article','cultural','historical','linguistic','folklore','place','name') NOT NULL DEFAULT 'article',
    `lang`         CHAR(2) NOT NULL DEFAULT 'mg',
    `status`       ENUM('published','draft','pending','rejected') NOT NULL DEFAULT 'draft',
    `featured`     TINYINT(1) NOT NULL DEFAULT 0,
    `view_count`   INT UNSIGNED NOT NULL DEFAULT 0,
    `author_id`    INT UNSIGNED DEFAULT NULL,
    `published_at` DATETIME DEFAULT NULL,
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME DEFAULT NULL,
    `deleted_at`   DATETIME DEFAULT NULL,
    FULLTEXT INDEX `ft_article` (`title`,`excerpt`,`body`),
    INDEX `idx_slug` (`slug`),
    INDEX `idx_status` (`status`),
    INDEX `idx_type` (`type`),
    INDEX `idx_featured` (`featured`),
    FOREIGN KEY (`author_id`)   REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- COMMENTS (POLYMORPHIC)
-- ------------------------------------------------------------

CREATE TABLE `comments` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `entity_type`  ENUM('word','proverb','article') NOT NULL,
    `entity_id`    INT UNSIGNED NOT NULL,
    `user_id`      INT UNSIGNED NOT NULL,
    `parent_id`    INT UNSIGNED DEFAULT NULL,
    `body`         TEXT NOT NULL,
    `status`       ENUM('published','pending','hidden') NOT NULL DEFAULT 'published',
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME DEFAULT NULL,
    `deleted_at`   DATETIME DEFAULT NULL,
    INDEX `idx_entity` (`entity_type`,`entity_id`),
    FOREIGN KEY (`user_id`)   REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`parent_id`) REFERENCES `comments`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- REACTIONS (POLYMORPHIC)
-- ------------------------------------------------------------

CREATE TABLE `reactions` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `entity_type` ENUM('word','proverb','article','comment') NOT NULL,
    `entity_id`   INT UNSIGNED NOT NULL,
    `user_id`     INT UNSIGNED NOT NULL,
    `type`        ENUM('love','useful','popular','educational','interesting') NOT NULL,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_reaction` (`entity_type`,`entity_id`,`user_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- FORUMS
-- ------------------------------------------------------------

CREATE TABLE `forums` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(150) NOT NULL,
    `slug`        VARCHAR(200) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `sort_order`  INT NOT NULL DEFAULT 0,
    `topic_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `post_count`  INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `topics` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `forum_id`    INT UNSIGNED NOT NULL,
    `user_id`     INT UNSIGNED NOT NULL,
    `title`       VARCHAR(300) NOT NULL,
    `slug`        VARCHAR(350) NOT NULL UNIQUE,
    `body`        TEXT NOT NULL,
    `status`      ENUM('open','closed','pinned','archived') NOT NULL DEFAULT 'open',
    `view_count`  INT UNSIGNED NOT NULL DEFAULT 0,
    `reply_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `last_post_at` DATETIME DEFAULT NULL,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME DEFAULT NULL,
    `deleted_at`  DATETIME DEFAULT NULL,
    FULLTEXT INDEX `ft_topic` (`title`,`body`),
    INDEX `idx_forum` (`forum_id`),
    INDEX `idx_slug` (`slug`),
    FOREIGN KEY (`forum_id`) REFERENCES `forums`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `posts` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `topic_id`   INT UNSIGNED NOT NULL,
    `user_id`    INT UNSIGNED NOT NULL,
    `body`       TEXT NOT NULL,
    `status`     ENUM('published','hidden') NOT NULL DEFAULT 'published',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL,
    `deleted_at` DATETIME DEFAULT NULL,
    INDEX `idx_topic` (`topic_id`),
    FOREIGN KEY (`topic_id`) REFERENCES `topics`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- BADGES
-- ------------------------------------------------------------

CREATE TABLE `badges` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(100) NOT NULL,
    `slug`        VARCHAR(120) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `icon`        VARCHAR(100) DEFAULT NULL,
    `color`       VARCHAR(20)  DEFAULT '#2E7D32',
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_badges` (
    `user_id`    INT UNSIGNED NOT NULL,
    `badge_id`   INT UNSIGNED NOT NULL,
    `awarded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`,`badge_id`),
    FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`badge_id`) REFERENCES `badges`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- NOTIFICATIONS
-- ------------------------------------------------------------

CREATE TABLE `notifications` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`     INT UNSIGNED NOT NULL,
    `type`        VARCHAR(100) NOT NULL,
    `title`       VARCHAR(255) NOT NULL,
    `body`        TEXT DEFAULT NULL,
    `link`        VARCHAR(500) DEFAULT NULL,
    `is_read`     TINYINT(1) NOT NULL DEFAULT 0,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_read` (`user_id`,`is_read`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- CONTRIBUTIONS (SUBMISSION QUEUE)
-- ------------------------------------------------------------

CREATE TABLE `contributions` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `type`        ENUM('word','proverb','article','correction','audio') NOT NULL,
    `user_id`     INT UNSIGNED DEFAULT NULL,
    `entity_id`   INT UNSIGNED DEFAULT NULL COMMENT 'filled after approval',
    `data`        JSON NOT NULL,
    `notes`       TEXT DEFAULT NULL,
    `status`      ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `reviewed_by` INT UNSIGNED DEFAULT NULL,
    `reviewed_at` DATETIME DEFAULT NULL,
    `review_note` TEXT DEFAULT NULL,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_status` (`status`),
    INDEX `idx_type` (`type`),
    FOREIGN KEY (`user_id`)     REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- ADS
-- ------------------------------------------------------------

CREATE TABLE `ads` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title`       VARCHAR(200) NOT NULL,
    `placement`   ENUM('header','sidebar','article_footer','sponsored') NOT NULL,
    `type`        ENUM('image','html','text') NOT NULL DEFAULT 'image',
    `content`     TEXT NOT NULL,
    `link`        VARCHAR(500) DEFAULT NULL,
    `status`      ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `start_date`  DATE DEFAULT NULL,
    `end_date`    DATE DEFAULT NULL,
    `impressions` INT UNSIGNED NOT NULL DEFAULT 0,
    `clicks`      INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_placement_status` (`placement`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- DONATIONS
-- ------------------------------------------------------------

CREATE TABLE `donation_campaigns` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title`       VARCHAR(200) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `goal`        DECIMAL(10,2) NOT NULL DEFAULT 0,
    `raised`      DECIMAL(10,2) NOT NULL DEFAULT 0,
    `status`      ENUM('active','completed','cancelled') NOT NULL DEFAULT 'active',
    `start_date`  DATE DEFAULT NULL,
    `end_date`    DATE DEFAULT NULL,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `donations` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `campaign_id`  INT UNSIGNED DEFAULT NULL,
    `user_id`      INT UNSIGNED DEFAULT NULL,
    `name`         VARCHAR(150) DEFAULT NULL,
    `email`        VARCHAR(191) DEFAULT NULL,
    `amount`       DECIMAL(10,2) NOT NULL,
    `currency`     CHAR(3) NOT NULL DEFAULT 'USD',
    `message`      TEXT DEFAULT NULL,
    `anonymous`    TINYINT(1) NOT NULL DEFAULT 0,
    `status`       ENUM('pending','confirmed','failed') NOT NULL DEFAULT 'pending',
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`campaign_id`) REFERENCES `donation_campaigns`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`user_id`)     REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- ANNOUNCEMENTS
-- ------------------------------------------------------------

CREATE TABLE `announcements` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title`       VARCHAR(255) NOT NULL,
    `body`        TEXT NOT NULL,
    `type`        ENUM('info','warning','success','update') NOT NULL DEFAULT 'info',
    `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
    `created_by`  INT UNSIGNED DEFAULT NULL,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at`  DATETIME DEFAULT NULL,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- NEWSLETTER
-- ------------------------------------------------------------

CREATE TABLE `newsletter_subscribers` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email`       VARCHAR(191) NOT NULL UNIQUE,
    `name`        VARCHAR(150) DEFAULT NULL,
    `status`      ENUM('active','unsubscribed') NOT NULL DEFAULT 'active',
    `token`       VARCHAR(100) DEFAULT NULL,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- SEO METADATA
-- ------------------------------------------------------------

CREATE TABLE `seo_metadata` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `entity_type`  VARCHAR(50) NOT NULL,
    `entity_id`    INT UNSIGNED DEFAULT NULL,
    `path`         VARCHAR(500) DEFAULT NULL,
    `title`        VARCHAR(255) DEFAULT NULL,
    `description`  VARCHAR(500) DEFAULT NULL,
    `og_image`     VARCHAR(255) DEFAULT NULL,
    `schema_type`  VARCHAR(50)  DEFAULT NULL,
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- SITE SETTINGS (KEY-VALUE CMS)
-- ------------------------------------------------------------

CREATE TABLE `site_settings` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key`         VARCHAR(150) NOT NULL UNIQUE,
    `value`       TEXT DEFAULT NULL,
    `type`        ENUM('text','textarea','boolean','json','image','color') NOT NULL DEFAULT 'text',
    `group`       VARCHAR(80) NOT NULL DEFAULT 'general',
    `label`       VARCHAR(200) DEFAULT NULL,
    `updated_at`  DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- ANALYTICS LOGS
-- ------------------------------------------------------------

CREATE TABLE `analytics_logs` (
    `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `event`       VARCHAR(80) NOT NULL,
    `entity_type` VARCHAR(50) DEFAULT NULL,
    `entity_id`   INT UNSIGNED DEFAULT NULL,
    `user_id`     INT UNSIGNED DEFAULT NULL,
    `ip`          VARCHAR(45) DEFAULT NULL,
    `user_agent`  VARCHAR(500) DEFAULT NULL,
    `referer`     VARCHAR(500) DEFAULT NULL,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_event_date` (`event`,`created_at`),
    INDEX `idx_entity` (`entity_type`,`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- ACTIVITY LOGS (ADMIN AUDIT TRAIL)
-- ------------------------------------------------------------

CREATE TABLE `activity_logs` (
    `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`     INT UNSIGNED DEFAULT NULL,
    `action`      VARCHAR(150) NOT NULL,
    `entity_type` VARCHAR(80) DEFAULT NULL,
    `entity_id`   INT UNSIGNED DEFAULT NULL,
    `details`     JSON DEFAULT NULL,
    `ip`          VARCHAR(45) DEFAULT NULL,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET foreign_key_checks = 1;
