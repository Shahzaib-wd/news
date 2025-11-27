<?php
/**
 * Like API Endpoint
 * Handles article like requests via AJAX
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

session_name(SESSION_NAME);
session_start();

header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Verify CSRF token
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}

// Check rate limit
if (!checkRateLimit('like', RATE_LIMIT_LIKES)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many like requests. Please try again later.']);
    exit;
}

// Get article ID
$article_id = isset($_POST['article_id']) ? intval($_POST['article_id']) : 0;

if ($article_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid article ID']);
    exit;
}

$db = getDB();

// Verify article exists
$stmt = $db->prepare("SELECT id FROM articles WHERE id = ? AND status = 'published'");
$stmt->execute([$article_id]);
if (!$stmt->fetch()) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Article not found']);
    exit;
}

// Get like identifier
$identifier = getLikeIdentifier();

// Check if already liked
$stmt = $db->prepare("SELECT id FROM likes WHERE article_id = ? AND identifier = ?");
$stmt->execute([$article_id, $identifier]);

if ($stmt->fetch()) {
    // Already liked
    $like_count = getArticleLikes($article_id);
    echo json_encode([
        'success' => false,
        'message' => 'You have already liked this article',
        'like_count' => $like_count
    ]);
    exit;
}

// Add like
try {
    $stmt = $db->prepare("INSERT INTO likes (article_id, identifier) VALUES (?, ?)");
    $stmt->execute([$article_id, $identifier]);
    
    $like_count = getArticleLikes($article_id);
    
    echo json_encode([
        'success' => true,
        'message' => 'Article liked successfully',
        'like_count' => $like_count
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to like article']);
}
