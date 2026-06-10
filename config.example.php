<?php
/**
 * BoostReviewsNFC — configuration
 * Copy to config.php and fill in your values.
 */

const SITE_NAME = 'BoostReviewsNFC';

// Your public domain, no trailing slash. Used for canonical URLs, hreflang and the sitemap.
// Can be overridden with the BASE_URL environment variable (handy for local testing).
define('BASE_URL', getenv('BASE_URL') ?: 'https://example.com');

// Supported languages. First one is the default / x-default.
const LANGS = ['en', 'es'];
const DEFAULT_LANG = 'en';

// Admin password (bcrypt hash). Never store the plain password here.
// Generate with: php -r "echo password_hash('your-password', PASSWORD_BCRYPT, ['cost' => 12]);"
const ADMIN_PASSWORD_HASH = 'REPLACE_WITH_BCRYPT_HASH';

// Database connection string (MySQL). On the Webempresa server PHP connects via localhost.
// From outside (e.g. local testing) override with the DATABASE_URL env variable.
define('DATABASE_URL', getenv('DATABASE_URL') ?: 'mysql://user:password@localhost:3306/dbname');

const POSTS_PER_PAGE = 10;
