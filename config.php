<?php
/**
 * Global Insights Configuration
 * Main configuration file - loads environment variables and sets constants
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        die('Configuration file not found. Please copy .env.example to .env and configure it.');
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            $value = trim($value, '"\'');
            
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
            }
        }
    }
}

// Load .env file
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    loadEnv($envFile);
} else {
    // Fallback to example for first-time setup
    if (file_exists(__DIR__ . '/.env.example')) {
        die('Please copy .env.example to .env and configure your database settings.');
    }
}

// Helper function to get environment variable with default
function env($key, $default = null) {
    return isset($_ENV[$key]) ? $_ENV[$key] : $default;
}

// Database Configuration
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_NAME', env('DB_NAME', 'global_insights'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

// Site Configuration
define('SITE_URL', rtrim(env('SITE_URL', 'http://localhost'), '/'));
define('SITE_NAME', env('SITE_NAME', 'Global Insights'));
define('SITE_TAGLINE', env('SITE_TAGLINE', 'Your Source for World News'));
define('ADMIN_EMAIL', env('ADMIN_EMAIL', 'admin@example.com'));

// Paths
define('ROOT_PATH', __DIR__);
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('ARTICLES_UPLOAD_PATH', UPLOAD_PATH . '/articles');

// URLs
define('BASE_URL', SITE_URL);
define('ASSETS_URL', BASE_URL . '/public/assets');
define('UPLOADS_URL', BASE_URL . '/uploads');

// Security
define('SESSION_NAME', env('SESSION_NAME', 'gi_session'));
define('CSRF_TOKEN_NAME', env('CSRF_TOKEN_NAME', 'gi_csrf_token'));

// File Upload Settings
define('MAX_UPLOAD_SIZE', env('MAX_UPLOAD_SIZE', 5242880)); // 5MB default
define('ALLOWED_IMAGE_TYPES', explode(',', env('ALLOWED_IMAGE_TYPES', 'image/jpeg,image/png,image/gif,image/webp')));

// Pagination
define('ARTICLES_PER_PAGE', env('ARTICLES_PER_PAGE', 12));
define('COMMENTS_PER_PAGE', env('COMMENTS_PER_PAGE', 20));

// Rate Limiting
define('RATE_LIMIT_LIKES', env('RATE_LIMIT_LIKES', 10));
define('RATE_LIMIT_COMMENTS', env('RATE_LIMIT_COMMENTS', 5));

// Features
define('COMMENTS_AUTO_APPROVE', env('COMMENTS_AUTO_APPROVE', 'false') === 'true');
define('ENABLE_RECAPTCHA', env('ENABLE_RECAPTCHA', 'false') === 'true');
define('RECAPTCHA_SITE_KEY', env('RECAPTCHA_SITE_KEY', ''));
define('RECAPTCHA_SECRET_KEY', env('RECAPTCHA_SECRET_KEY', ''));

// External API Key (e.g., TinyMCE cloud)
define('EXTERNAL_API_KEY', env('EXTERNAL_API_KEY', ''));

// Timezone
date_default_timezone_set(env('TIMEZONE', 'UTC'));

// Debug Mode
define('DEBUG_MODE', env('DEBUG_MODE', 'false') === 'true');

// Error Reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');

// If using HTTPS, enable secure cookies
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}
