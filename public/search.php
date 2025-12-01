<?php
/**
 * Search Page
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$db = getDB();
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$articles = [];
$error = '';

if (!empty($query)) {
    if (strlen($query) < 3) {
        $error = 'Search query must be at least 3 characters long.';
    } else {
        try {
            $search_term = "%{$query}%";
            $stmt = $db->prepare("
                SELECT a.*, c.name as category_name, c.slug as category_slug
                FROM articles a
                LEFT JOIN categories c ON a.category_id = c.id
                WHERE a.status = 'published' 
                AND (a.title LIKE ? OR a.subtitle LIKE ? OR a.body LIKE ?)
                ORDER BY a.published_at DESC
                LIMIT 50
            ");
            $stmt->execute([$search_term, $search_term, $search_term]);
            $articles = $stmt->fetchAll();
        } catch (Exception $e) {
            $error = 'An error occurred while searching. Please try again.';
            if (DEBUG_MODE) {
                $error .= ' (' . $e->getMessage() . ')';
            }
        }
    }
}

$page_title = !empty($query) ? "Search results for: " . $query : "Search";
$meta_description = "Search articles on " . SITE_NAME;

include __DIR__ . '/../templates/header.php';
?>

<main class="container my-4">
    <h2 class="mb-4"><i class="bi bi-search"></i> Search Articles</h2>
    
    <form method="GET" id="search-form" class="search-form mb-4">
        <div class="input-group input-group-lg">
            <input type="text" class="form-control" name="q" id="search-query" 
                   value="<?= e($query) ?>" placeholder="Search for articles..." minlength="3">
            <button class="btn btn-primary" type="submit">
                <i class="bi bi-search"></i> Search
            </button>
        </div>
    </form>

    <?php if (!empty($error)): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?= e($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($query)): ?>
        <h4 class="mb-3">Results for "<?= e($query) ?>" (<?= count($articles) ?> found)</h4>
        
        <?php if (empty($articles)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No articles found. Try different keywords or check back later.
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($articles as $article): ?>
                    <div class="col-md-6">
                        <div class="article-card">
                            <img src="<?= UPLOADS_URL . '/' . e($article['featured_image']) ?>" alt="<?= e($article['title']) ?>" loading="lazy">
                            <div class="card-body">
                                <?php if ($article['category_name']): ?>
                                    <a href="<?= getCategoryURL($article['category_slug']) ?>" class="badge-category"><?= e($article['category_name']) ?></a>
                                <?php endif; ?>
                                <h5 class="card-title"><a href="<?= getArticleURL($article['slug']) ?>"><?= e($article['title']) ?></a></h5>
                                <p class="card-text"><?= truncate($article['subtitle'], 120) ?></p>
                                <div class="article-meta">
                                    <span><i class="bi bi-calendar3"></i> <?= formatDate($article['published_at'], 'M j, Y') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-secondary">
            <i class="bi bi-search"></i> Enter at least 3 characters to search for articles.
        </div>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/../templates/footer.php'; ?>
