<?php
/**
 * Global Insights - Article Page
 * Display individual article with comments and social sharing
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$db = getDB();

// Get article slug from URL
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
    header('HTTP/1.0 404 Not Found');
    die('Article not found');
}

// Fetch article
$stmt = $db->prepare("
    SELECT a.*, c.name as category_name, c.slug as category_slug
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    WHERE a.slug = ? AND a.status = 'published'
");
$stmt->execute([$slug]);
$article = $stmt->fetch();

if (!$article) {
    header('HTTP/1.0 404 Not Found');
    die('Article not found');
}

// Track view
trackArticleView($article['id']);

// Get article tags
$tags_stmt = $db->prepare("
    SELECT t.* FROM tags t
    INNER JOIN article_tag at ON t.id = at.tag_id
    WHERE at.article_id = ?
");
$tags_stmt->execute([$article['id']]);
$tags = $tags_stmt->fetchAll();

// Get like count and check if user liked
$like_count = getArticleLikes($article['id']);
$has_liked = hasUserLiked($article['id']);

// Get approved comments
$comments_stmt = $db->prepare("
    SELECT * FROM comments
    WHERE article_id = ? AND status = 'approved'
    ORDER BY created_at DESC
");
$comments_stmt->execute([$article['id']]);
$comments = $comments_stmt->fetchAll();

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid security token');
        redirect(getArticleURL($slug) . '#comments-section');
    }
    
    if (!checkRateLimit('comment', RATE_LIMIT_COMMENTS)) {
        setFlash('error', 'Too many comments. Please wait before commenting again.');
        redirect(getArticleURL($slug) . '#comments-section');
    }
    
    $name = trim($_POST['name'] ?? 'Anonymous');
    $content = trim($_POST['content'] ?? '');
    
    // Validate
    if (empty($content) || strlen($content) < 10) {
        setFlash('error', 'Comment must be at least 10 characters long');
        redirect(getArticleURL($slug) . '#comments-section');
    }
    
    if (strlen($content) > 1000) {
        setFlash('error', 'Comment must not exceed 1000 characters');
        redirect(getArticleURL($slug) . '#comments-section');
    }
    
    // Basic spam check (very simple)
    if (preg_match('/(http|www|\.com|\.net)/i', $content)) {
        $status = 'spam';
    } else {
        $status = COMMENTS_AUTO_APPROVE ? 'approved' : 'pending';
    }
    
    // Insert comment
    $insert_stmt = $db->prepare("
        INSERT INTO comments (article_id, name, content, ip_address, status)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insert_stmt->execute([
        $article['id'],
        htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
        htmlspecialchars($content, ENT_QUOTES, 'UTF-8'),
        getClientIP(),
        $status
    ]);
    
    if ($status === 'approved') {
        setFlash('success', 'Comment posted successfully!');
    } else {
        setFlash('success', 'Comment submitted and awaiting moderation.');
    }
    
    redirect(getArticleURL($slug) . '#comments-section');
}

// SEO Meta
$page_title = $article['title'];
$meta_description = $article['meta_description'] ?: truncate($article['subtitle'], 160);
$canonical_url = getArticleURL($article['slug']);
$article_url = getArticleURL($article['slug']);
$article_image = BASE_URL . '/' . UPLOADS_URL . '/' . $article['featured_image'];

// Open Graph
$og_type = 'article';
$og_title = $article['title'];
$og_description = $meta_description;
$og_image = $article_image;
$og_url = $canonical_url;

// Social Share Links
$share_links = getSocialShareLinks($article_url, $article['title']);

// JSON-LD Schema
$article_schema = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'NewsArticle',
    'headline' => $article['title'],
    'description' => $meta_description,
    'image' => $article_image,
    'datePublished' => date('c', strtotime($article['published_at'])),
    'dateModified' => date('c', strtotime($article['updated_at'])),
    'author' => [
        '@type' => 'Person',
        'name' => $article['author']
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => SITE_NAME,
        'logo' => [
            '@type' => 'ImageObject',
            'url' => BASE_URL . '/public/assets/images/logo.png'
        ]
    ]
], JSON_UNESCAPED_SLASHES);

include __DIR__ . '/../templates/header.php';
?>

<main class="container my-4">
    <div class="row">
        <!-- Article Content -->
        <div class="col-lg-8">
            <article>
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/public/">Home</a></li>
                        <?php if ($article['category_name']): ?>
                            <li class="breadcrumb-item">
                                <a href="<?= getCategoryURL($article['category_slug']) ?>">
                                    <?= e($article['category_name']) ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active"><?= e(truncate($article['title'], 50)) ?></li>
                    </ol>
                </nav>

                <!-- Article Header -->
                <div class="article-header">
                    <?php if ($article['category_name']): ?>
                        <a href="<?= getCategoryURL($article['category_slug']) ?>" class="badge-category">
                            <?= e($article['category_name']) ?>
                        </a>
                    <?php endif; ?>
                    <h1><?= e($article['title']) ?></h1>
                    <?php if ($article['subtitle']): ?>
                        <h2><?= e($article['subtitle']) ?></h2>
                    <?php endif; ?>
                    
                    <div class="article-meta mb-3">
                        <span><i class="bi bi-person"></i> <?= e($article['author']) ?></span>
                        <span><i class="bi bi-calendar3"></i> <?= formatDate($article['published_at'], 'F j, Y') ?></span>
                        <span><i class="bi bi-clock"></i> <?= timeAgo($article['published_at']) ?></span>
                        <span><i class="bi bi-eye"></i> <?= number_format($article['views']) ?> views</span>
                    </div>
                </div>

                <!-- Featured Image -->
                <?php if ($article['featured_image']): ?>
                    <img src="<?= UPLOADS_URL . '/' . e($article['featured_image']) ?>" 
                         alt="<?= e($article['title']) ?>" 
                         class="article-featured-image"
                         loading="eager">
                <?php endif; ?>

                <!-- Article Actions -->
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div>
                        <button id="like-button" 
                                class="like-button <?= $has_liked ? 'liked' : '' ?>" 
                                onclick="likeArticle(<?= $article['id'] ?>)"
                                <?= $has_liked ? 'disabled' : '' ?>>
                            <i class="bi bi-heart-fill"></i>
                            <span class="like-text"><?= $has_liked ? 'Liked' : 'Like' ?></span>
                            (<span id="like-count"><?= $like_count ?></span>)
                        </button>
                    </div>
                    <div class="share-buttons">
                        <a href="<?= $share_links['facebook'] ?>" target="_blank" class="share-btn share-btn-facebook">
                            <i class="bi bi-facebook"></i> Share
                        </a>
                        <a href="<?= $share_links['twitter'] ?>" target="_blank" class="share-btn share-btn-twitter">
                            <i class="bi bi-twitter"></i> Tweet
                        </a>
                        <a href="<?= $share_links['whatsapp'] ?>" target="_blank" class="share-btn share-btn-whatsapp">
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </a>
                        <a href="<?= $share_links['telegram'] ?>" target="_blank" class="share-btn share-btn-telegram">
                            <i class="bi bi-telegram"></i> Telegram
                        </a>
                        <button onclick="copyLink('<?= $article_url ?>')" class="share-btn share-btn-copy">
                            <i class="bi bi-link-45deg"></i> Copy Link
                        </button>
                    </div>
                </div>

                <!-- Ad Placeholder: After Header -->
                <div class="ad-placeholder mb-4" style="height: 100px;">
                    <small>Advertisement - AdSense Banner (728x90)</small>
                </div>

                <!-- Article Body -->
                <div class="article-content">
                    <?= $article['body'] ?>
                </div>

                <!-- Ad Placeholder: End of Article -->
                <div class="ad-placeholder mt-4" style="height: 250px;">
                    <small>Advertisement - AdSense Rectangle (336x280)</small>
                </div>

                <!-- Article Tags -->
                <?php if (!empty($tags)): ?>
                    <div class="article-tags">
                        <strong><i class="bi bi-tags"></i> Tags:</strong>
                        <?php foreach ($tags as $tag): ?>
                            <a href="<?= getTagURL($tag['slug']) ?>" class="tag-link">
                                <?= e($tag['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Comments Section -->
                <div class="comments-section" id="comments-section">
                    <h3><i class="bi bi-chat-dots"></i> Comments (<?= count($comments) ?>)</h3>

                    <!-- Comment Form -->
                    <div class="comment-form">
                        <h5>Leave a Comment</h5>
                        <form method="POST" id="comment-form">
                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                            <div class="mb-3">
                                <label for="comment-name" class="form-label">Name (optional)</label>
                                <input type="text" class="form-control" id="comment-name" name="name" maxlength="100" placeholder="Anonymous">
                            </div>
                            <div class="mb-3">
                                <label for="comment-content" class="form-label">Comment *</label>
                                <textarea class="form-control" id="comment-content" name="content" rows="4" required maxlength="1000" placeholder="Share your thoughts..."></textarea>
                            </div>
                            <button type="submit" name="submit_comment" class="btn btn-primary">
                                <i class="bi bi-send"></i> Post Comment
                            </button>
                        </form>
                    </div>

                    <!-- Comments List -->
                    <div class="mt-4">
                        <?php if (empty($comments)): ?>
                            <p class="text-muted">No comments yet. Be the first to comment!</p>
                        <?php else: ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment-item">
                                    <div class="comment-author"><?= e($comment['name']) ?></div>
                                    <div class="comment-date"><?= timeAgo($comment['created_at']) ?></div>
                                    <div class="comment-content"><?= nl2br(e($comment['content'])) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Ad Placeholder: Sidebar -->
            <div class="ad-placeholder mb-4" style="height: 250px;">
                <small>Advertisement - AdSense Square (300x250)</small>
            </div>

            <!-- Related Articles -->
            <div class="sidebar-widget">
                <h4><i class="bi bi-collection"></i> Related Articles</h4>
                <?php
                $related_stmt = $db->prepare("
                    SELECT a.* FROM articles a
                    WHERE a.category_id = ? AND a.id != ? AND a.status = 'published'
                    ORDER BY a.published_at DESC
                    LIMIT 5
                ");
                $related_stmt->execute([$article['category_id'], $article['id']]);
                $related = $related_stmt->fetchAll();
                
                if (empty($related)):
                ?>
                    <p class="text-muted">No related articles found.</p>
                <?php else: ?>
                    <?php foreach ($related as $rel): ?>
                        <div class="trending-item">
                            <img src="<?= UPLOADS_URL . '/' . e($rel['featured_image']) ?>" 
                                 alt="<?= e($rel['title']) ?>"
                                 loading="lazy">
                            <div class="trending-item-content">
                                <div class="trending-item-title">
                                    <a href="<?= getArticleURL($rel['slug']) ?>">
                                        <?= e(truncate($rel['title'], 60)) ?>
                                    </a>
                                </div>
                                <div class="trending-item-meta">
                                    <?= timeAgo($rel['published_at']) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Ad Placeholder: Sidebar Bottom -->
            <div class="ad-placeholder" style="height: 250px;">
                <small>Advertisement - AdSense Square (300x250)</small>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../templates/footer.php'; ?>
