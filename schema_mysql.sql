-- BoostReviewsNFC blog — MySQL version
CREATE TABLE IF NOT EXISTS blog (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    slug_en       VARCHAR(220) NOT NULL UNIQUE,
    slug_es       VARCHAR(220) NOT NULL UNIQUE,
    title_en      VARCHAR(220) NOT NULL,
    title_es      VARCHAR(220) NOT NULL,
    meta_title_en VARCHAR(70),
    meta_title_es VARCHAR(70),
    meta_desc_en  VARCHAR(170),
    meta_desc_es  VARCHAR(170),
    excerpt_en    TEXT,
    excerpt_es    TEXT,
    content_en    MEDIUMTEXT NOT NULL,
    content_es    MEDIUMTEXT NOT NULL,
    cover_image   VARCHAR(500),
    status        ENUM('draft','published') NOT NULL DEFAULT 'draft',
    published_at  DATETIME NULL,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_blog_status_published (status, published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
