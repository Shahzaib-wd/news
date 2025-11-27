<?php
/**
 * Category Page - Display articles by category
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$db = getDB();
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
    redirect(BASE_URL . '/public/');
}

// Get category
$cat_stmt = $db->prepare("SELECT * FROM categories WHERE slug = ?");
$cat_stmt->execute([$slug]);
$category = $cat_stmt->fetch();

if (!$category) {
    header('HTTP/1.0 404 Not Found');
    die('Category not found');
}

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * ARTICLES_PER_PAGE;

// Get total articles
$count_stmt = $db->prepare("SELECT COUNT(*) as total FROM articles WHERE category_id = ? AND status = 'published'");
$count_stmt->execute([$category['id']]);
$total_articles = $count_stmt->fetch()['total'];
$total_pages = ceil($total_articles / ARTICLES_PER_PAGE);

// Get articles
$articles_stmt = $db->prepare("
    SELECT a.*, (SELECT COUNT(*) FROM likes WHERE article_id = a.id) as like_count
    FROM articles a
    WHERE a.category_id = ? AND a.status = 'published'
    ORDER BY a.published_at DESC
    LIMIT ? OFFSET ?
");
$articles_stmt->execute([$category['id'], ARTICLES_PER_PAGE, $offset]);
$articles = $articles_stmt->fetchAll();

$page_title = $category['name'];
$meta_description = $category['description'] ?: "Browse {$category['name']} articles on " . SITE_NAME;
$canonical_url = getCategoryURL($slug) . ($page > 1 ? '?page=' . $page : '');

include __DIR__ . '/../templates/header.php';
?>

<main class="container my-4">
    <h2 class="mb-4"><i class="bi bi-folder"></i> <?= e($category['name']) ?></h2>
    <?php if ($category['description']): ?>
        <p class="lead"><?= e($category['description']) ?></p>
    <?php endif; ?>
    
    <div class="row g-4">
        <?php foreach ($articles as $article): ?>
            <div class="col-md-4">
                <div class="article-card">
                    <img src="<?= UPLOADS_URL . '/' . e($article['featured_image']) ?>" alt="<?= e($article['title']) ?>" loading="lazy">
                    <div class="card-body">
                        <h5 class="card-title"><a href="<?= getArticleURL($article['slug']) ?>"><?= e($article['title']) ?></a></h5>
                        <p class="card-text"><?= truncate($article['subtitle'], 100) ?></p>
                        <div class="article-meta">
                            <span><i class="bi bi-calendar3"></i> <?= formatDate($article['published_at'], 'M j') ?></span>
                            <span><i class="bi bi-heart"></i> <?= $article['like_count'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if (empty($articles)): ?>
            <div class="col-12"><div class="alert alert-info">No articles in this category yet.</div></div>
        <?php endif; ?>
    </div>

    <?php if ($total_pages > 1): ?>
    <nav class="mt-4"><ul class="pagination justify-content-center">
        <?php if ($page > 1): ?><li class="page-item"><a class="page-link" href="?slug=<?= $slug ?>&page=<?= $page - 1 ?>">Previous</a></li><?php endif; ?>
        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="?slug=<?= $slug ?>&page=<?= $i ?>"><?= $i ?></a></li>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?><li class="page-item"><a class="page-link" href="?slug=<?= $slug ?>&page=<?= $page + 1 ?>">Next</a></li><?php endif; ?>
    </ul></nav>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/../templates/footer.php'; ?>
