<?php
/**
 * Global Insights - Helper Functions
 * Common utility functions used throughout the application
 */

/**
 * Generate CSRF Token
 */
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verify CSRF Token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Get Client IP Address
 */
function getClientIP() {
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Check for proxy headers
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }
    
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

/**
 * Generate Slug from Text
 */
function generateSlug($text) {
    // Convert to lowercase
    $slug = strtolower($text);
    
    // Replace non-alphanumeric characters with hyphens
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    
    // Remove leading/trailing hyphens
    $slug = trim($slug, '-');
    
    return $slug;
}

/**
 * Sanitize HTML Output
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize Article Body (Allow Safe HTML)
 */
function sanitizeArticleBody($html) {
    // Allow specific HTML tags for article content
    $allowed_tags = '<p><br><strong><em><u><h2><h3><h4><ul><ol><li><a><img><blockquote><code><pre>';
    return strip_tags($html, $allowed_tags);
}

/**
 * Format Date for Display
 */
function formatDate($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

/**
 * Time Ago Function
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;
    
    $periods = [
        'year' => 31536000,
        'month' => 2592000,
        'week' => 604800,
        'day' => 86400,
        'hour' => 3600,
        'minute' => 60,
        'second' => 1
    ];
    
    foreach ($periods as $key => $value) {
        $count = floor($difference / $value);
        if ($count > 0) {
            return $count . ' ' . $key . ($count > 1 ? 's' : '') . ' ago';
        }
    }
    
    return 'just now';
}

/**
 * Truncate Text
 */
function truncate($text, $length = 150, $suffix = '...') {
    $text = strip_tags($text);
    if (mb_strlen($text) > $length) {
        return mb_substr($text, 0, $length) . $suffix;
    }
    return $text;
}

/**
 * Get Article URL
 */
function getArticleURL($slug) {
    return BASE_URL . '/public/article.php?slug=' . urlencode($slug);
}

/**
 * Get Category URL
 */
function getCategoryURL($slug) {
    return BASE_URL . '/public/category.php?slug=' . urlencode($slug);
}

/**
 * Get Tag URL
 */
function getTagURL($slug) {
    return BASE_URL . '/public/tag.php?slug=' . urlencode($slug);
}

/**
 * Check if User is Admin (Logged In)
 */
function isAdmin() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require Admin Authentication
 */
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

/**
 * Redirect Function
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Flash Message System
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Rate Limiting Check
 */
function checkRateLimit($action, $limit_per_hour) {
    $db = getDB();
    $ip = getClientIP();
    $one_hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));
    
    // Clean old entries
    $stmt = $db->prepare("DELETE FROM rate_limits WHERE last_attempt < ?");
    $stmt->execute([$one_hour_ago]);
    
    // Check current rate
    $stmt = $db->prepare("
        SELECT attempt_count 
        FROM rate_limits 
        WHERE ip_address = ? AND action_type = ? AND last_attempt >= ?
    ");
    $stmt->execute([$ip, $action, $one_hour_ago]);
    $result = $stmt->fetch();
    
    if ($result && $result['attempt_count'] >= $limit_per_hour) {
        return false; // Rate limit exceeded
    }
    
    // Update or insert rate limit record
    $stmt = $db->prepare("
        INSERT INTO rate_limits (ip_address, action_type, attempt_count, last_attempt) 
        VALUES (?, ?, 1, NOW())
        ON DUPLICATE KEY UPDATE 
            attempt_count = attempt_count + 1,
            last_attempt = NOW()
    ");
    $stmt->execute([$ip, $action]);
    
    return true;
}

/**
 * Get Like Identifier (Cookie + IP Hash)
 */
function getLikeIdentifier() {
    $cookie_id = isset($_COOKIE['gi_visitor']) ? $_COOKIE['gi_visitor'] : '';
    $ip = getClientIP();
    return hash('sha256', $cookie_id . $ip);
}

/**
 * Set Visitor Cookie
 */
function setVisitorCookie() {
    if (!isset($_COOKIE['gi_visitor'])) {
        $visitor_id = bin2hex(random_bytes(16));
        setcookie('gi_visitor', $visitor_id, time() + (86400 * 365), '/', '', isset($_SERVER['HTTPS']), true);
    }
}

/**
 * Track Article View
 */
function trackArticleView($article_id) {
    $db = getDB();
    $ip = getClientIP();
    $today = date('Y-m-d');
    
    try {
        // Try to insert unique view
        $stmt = $db->prepare("
            INSERT IGNORE INTO article_views (article_id, ip_address, viewed_date)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$article_id, $ip, $today]);
        
        // If a new view was recorded, increment article views counter
        if ($stmt->rowCount() > 0) {
            $stmt = $db->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
            $stmt->execute([$article_id]);
        }
    } catch (Exception $e) {
        // Silently fail view tracking
    }
}

/**
 * Get Article Like Count
 */
function getArticleLikes($article_id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM likes WHERE article_id = ?");
    $stmt->execute([$article_id]);
    $result = $stmt->fetch();
    return $result['count'] ?? 0;
}

/**
 * Check if User Has Liked Article
 */
function hasUserLiked($article_id) {
    $db = getDB();
    $identifier = getLikeIdentifier();
    
    $stmt = $db->prepare("SELECT id FROM likes WHERE article_id = ? AND identifier = ?");
    $stmt->execute([$article_id, $identifier]);
    
    return $stmt->fetch() !== false;
}

/**
 * Upload Image
 */
function uploadImage($file, $destination_folder = 'articles') {
    // Validate file
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        throw new Exception('Invalid file upload');
    }
    
    // Check file size
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        throw new Exception('File size exceeds maximum allowed size');
    }
    
    // Validate MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, ALLOWED_IMAGE_TYPES)) {
        throw new Exception('Invalid file type. Only images are allowed.');
    }
    
    // Generate safe filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safe_filename = uniqid('img_', true) . '.' . $extension;
    
    // Ensure upload directory exists
    $upload_dir = UPLOAD_PATH . '/' . $destination_folder;
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $destination = $upload_dir . '/' . $safe_filename;
    
    // Move file
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Failed to move uploaded file');
    }
    
    // Save to database
    $db = getDB();
    $stmt = $db->prepare("
        INSERT INTO images (filename, path, size, mime_type)
        VALUES (?, ?, ?, ?)
    ");
    $relative_path = $destination_folder . '/' . $safe_filename;
    $stmt->execute([$safe_filename, $relative_path, $file['size'], $mime_type]);
    
    return $relative_path;
}

/**
 * Delete Image
 */
function deleteImage($path) {
    $full_path = UPLOAD_PATH . '/' . $path;
    if (file_exists($full_path)) {
        unlink($full_path);
    }
    
    // Remove from database
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM images WHERE path = ?");
    $stmt->execute([$path]);
}

/**
 * Get Social Share Links
 */
function getSocialShareLinks($url, $title) {
    $encoded_url = urlencode($url);
    $encoded_title = urlencode($title);
    
    return [
        'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$encoded_url}",
        'twitter' => "https://twitter.com/intent/tweet?url={$encoded_url}&text={$encoded_title}",
        'whatsapp' => "https://api.whatsapp.com/send?text={$encoded_title}%20{$encoded_url}",
        'telegram' => "https://t.me/share/url?url={$encoded_url}&text={$encoded_title}",
        'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url={$encoded_url}"
    ];
}

/**
 * Generate Sitemap XML
 */
function generateSitemap() {
    $db = getDB();
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    // Homepage
    $sitemap .= "  <url>\n";
    $sitemap .= "    <loc>" . e(BASE_URL . '/public/') . "</loc>\n";
    $sitemap .= "    <changefreq>daily</changefreq>\n";
    $sitemap .= "    <priority>1.0</priority>\n";
    $sitemap .= "  </url>\n";
    
    // Articles
    $stmt = $db->query("SELECT slug, updated_at FROM articles WHERE status = 'published' ORDER BY updated_at DESC");
    while ($article = $stmt->fetch()) {
        $sitemap .= "  <url>\n";
        $sitemap .= "    <loc>" . e(getArticleURL($article['slug'])) . "</loc>\n";
        $sitemap .= "    <lastmod>" . date('Y-m-d', strtotime($article['updated_at'])) . "</lastmod>\n";
        $sitemap .= "    <changefreq>weekly</changefreq>\n";
        $sitemap .= "    <priority>0.8</priority>\n";
        $sitemap .= "  </url>\n";
    }
    
    // Categories
    $stmt = $db->query("SELECT slug FROM categories");
    while ($category = $stmt->fetch()) {
        $sitemap .= "  <url>\n";
        $sitemap .= "    <loc>" . e(getCategoryURL($category['slug'])) . "</loc>\n";
        $sitemap .= "    <changefreq>weekly</changefreq>\n";
        $sitemap .= "    <priority>0.6</priority>\n";
        $sitemap .= "  </url>\n";
    }
    
    $sitemap .= "</urlset>";
    
    // Save sitemap
    file_put_contents(ROOT_PATH . '/public/sitemap.xml', $sitemap);
}
