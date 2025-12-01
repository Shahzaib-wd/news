<?php
/**
 * Global Insights - Homepage
 * Displays featured articles and paginated article listing
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$db = getDB();

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * ARTICLES_PER_PAGE;

// Get total article count
$count_stmt = $db->query("SELECT COUNT(*) as total FROM articles WHERE status = 'published'");
$total_articles = $count_stmt->fetch()['total'];
$total_pages = ceil($total_articles / ARTICLES_PER_PAGE);

// Get featured article
$featured_stmt = $db->prepare("
    SELECT a.*, c.name as category_name, c.slug as category_slug
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    WHERE a.status = 'published' AND a.is_featured = 1
    ORDER BY a.published_at DESC
    LIMIT 1
");
$featured_stmt->execute();
$featured = $featured_stmt->fetch();

// Get articles for listing (excluding featured)
$articles_stmt = $db->prepare("
    SELECT a.*, c.name as category_name, c.slug as category_slug
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    WHERE a.status = 'published' " . ($featured ? "AND a.id != ?" : "") . "
    ORDER BY a.published_at DESC
    LIMIT ? OFFSET ?
");

if ($featured) {
    $articles_stmt->execute([$featured['id'], ARTICLES_PER_PAGE, $offset]);
} else {
    $articles_stmt->execute([ARTICLES_PER_PAGE, $offset]);
}

$articles = $articles_stmt->fetchAll();

// Get trending articles for sidebar
$trending_stmt = $db->prepare("
    SELECT a.*, c.name as category_name
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    WHERE a.status = 'published'
    ORDER BY a.views DESC
    LIMIT 5
");
$trending_stmt->execute();
$trending = $trending_stmt->fetchAll();

// SEO Meta
$page_title = $page > 1 ? "Page $page" : "Home";
$meta_description = SITE_TAGLINE . " - Latest news from around the world";
$canonical_url = BASE_URL . '/public/' . ($page > 1 ? '?page=' . $page : '');

include __DIR__ . '/../templates/header.php';
?>

<main class="container my-4">
    <!-- Featured Article -->
    <?php if ($featured && $page === 1): ?>
    <div class="featured-article mb-5">
        <img src="<?= UPLOADS_URL . '/' . e($featured['featured_image']) ?>" 
             alt="<?= e($featured['title']) ?>" 
             loading="eager">
        <div class="featured-overlay">
            <?php if ($featured['category_name']): ?>
                <a href="<?= getCategoryURL($featured['category_slug']) ?>" class="badge-category">
                    <?= e($featured['category_name']) ?>
                </a>
            <?php endif; ?>
            <h2><?= e($featured['title']) ?></h2>
            <p><?= truncate($featured['subtitle'], 120) ?></p>
            <a href="<?= getArticleURL($featured['slug']) ?>" class="btn btn-light">Read More</a>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Ad Placeholder: Top Banner -->
            <div class="ad-placeholder mb-4" style="height: 100px;">
                <small>Advertisement - AdSense Banner (728x90)</small>
            </div>

            <h3 class="mb-4">Latest News</h3>
            
            <div class="row g-4">
                <?php foreach ($articles as $index => $article): ?>
                    <div class="col-md-6">
                        <div class="article-card">
                            <img src="<?= UPLOADS_URL . '/' . e($article['featured_image']) ?>" 
                                 alt="<?= e($article['title']) ?>" 
                                 loading="lazy">
                            <div class="card-body">
                                <?php if ($article['category_name']): ?>
                                    <a href="<?= getCategoryURL($article['category_slug']) ?>" class="badge-category">
                                        <?= e($article['category_name']) ?>
                                    </a>
                                <?php endif; ?>
                                <h5 class="card-title">
                                    <a href="<?= getArticleURL($article['slug']) ?>">
                                        <?= e($article['title']) ?>
                                    </a>
                                </h5>
                                <p class="card-text"><?= truncate($article['subtitle'], 100) ?></p>
                                <div class="article-meta">
                                    <span><i class="bi bi-calendar3"></i> <?= formatDate($article['published_at'], 'M j, Y') ?></span>
                                    <span><i class="bi bi-eye"></i> <?= number_format($article['views']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ad between articles (after every 4 articles) -->
                    <?php if (($index + 1) % 4 === 0 && $index < count($articles) - 1): ?>
                        <div class="col-12">
                            <div class="ad-placeholder" style="height: 100px;">
                                <small>Advertisement - AdSense In-Feed Ad</small>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                
                <?php if (empty($articles) && !$featured): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            No articles found. Check back soon for updates!
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 ?>" rel="prev">Previous</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 ?>" rel="next">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Ad Placeholder: Sidebar -->
            <div class="ad-placeholder mb-4" style="height: 250px;">
                <small>Advertisement - AdSense Square (300x250)</small>
            </div>

            <!-- Trending Articles -->
            <div class="sidebar-widget">
                <h4><i class="bi bi-fire"></i> Trending</h4>
                <?php foreach ($trending as $trend): ?>
                    <div class="trending-item">
                        <img src="<?= UPLOADS_URL . '/' . e($trend['featured_image']) ?>" 
                             alt="<?= e($trend['title']) ?>"
                             loading="lazy">
                        <div class="trending-item-content">
                            <div class="trending-item-title">
                                <a href="<?= getArticleURL($trend['slug']) ?>">
                                    <?= e(truncate($trend['title'], 60, '...')) ?>
                                </a>
                            </div>
                            <div class="trending-item-meta">
                                <i class="bi bi-eye"></i> <?= number_format($trend['views']) ?> views
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Categories Widget -->
            <div class="sidebar-widget">
                <h4><i class="bi bi-folder"></i> Categories</h4>
                <div class="list-group list-group-flush">
                    <?php
                    $cat_stmt = $db->query("
                        SELECT c.*, COUNT(a.id) as article_count 
                        FROM categories c
                        LEFT JOIN articles a ON c.id = a.category_id AND a.status = 'published'
                        GROUP BY c.id
                        ORDER BY c.name
                    ");
                    while ($cat = $cat_stmt->fetch()):
                    ?>
                        <a href="<?= getCategoryURL($cat['slug']) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <?= e($cat['name']) ?>
                            <span class="badge bg-primary rounded-pill"><?= $cat['article_count'] ?></span>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Ad Placeholder: Sidebar Bottom -->
            <div class="ad-placeholder" style="height: 250px;">
                <small>Advertisement - AdSense Square (300x250)</small>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../templates/footer.php'; ?>
