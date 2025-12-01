<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

session_name(SESSION_NAME);
session_start();
requireAdmin(); // Ensure only admin can access

$db = getDB();

// Handle delete request
if (isset($_GET['delete']) && isset($_GET['csrf_token'])) {
    if (verifyCSRFToken($_GET['csrf_token'])) {
        $id = intval($_GET['delete']);
        
        // Fetch article to delete image
        $stmt = $db->prepare("SELECT featured_image FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $article = $stmt->fetch();
        
        if ($article) {
            // Delete featured image if exists
            if ($article['featured_image']) {
                deleteImage($article['featured_image']);
            }
            
            // Delete article and cascade to likes, comments, tags
            $delete = $db->prepare("DELETE FROM articles WHERE id = ?");
            $delete->execute([$id]);
            
            setFlash('success', 'Article deleted successfully');
        }
    } else {
        setFlash('error', 'Invalid CSRF token');
    }
    redirect(BASE_URL . '/admin/articles.php');
}

// Fetch all articles with counts
$stmt = $db->query("
    SELECT a.*, c.name as category_name,
           (SELECT COUNT(*) FROM comments WHERE article_id = a.id) AS comment_count
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    ORDER BY a.created_at DESC
");
$articles = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-newspaper"></i> Manage Articles</h2>
    <a href="<?= BASE_URL ?>/admin/article-edit.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Article
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th style="width: 80px;">Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Comments</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?= $article['id'] ?></td>
                        <td>
                            <?php if ($article['featured_image']): ?>
                                <img src="<?= UPLOADS_URL . '/' . e($article['featured_image']) ?>" 
                                     alt="" class="img-thumbnail" style="width: 60px; height: 40px; object-fit: cover;">
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= e(truncate($article['title'], 50)) ?></strong><br>
                            <small class="text-muted"><?= e(truncate($article['subtitle'], 60)) ?></small>
                        </td>
                        <td><?= e($article['category_name'] ?? 'None') ?></td>
                        <td>
                            <span class="badge bg-<?= $article['status'] === 'published' ? 'success' : ($article['status'] === 'draft' ? 'warning' : 'secondary') ?>">
                                <?= e($article['status']) ?>
                            </span>
                            <?php if ($article['is_featured']): ?>
                                <span class="badge bg-primary">Featured</span>
                            <?php endif; ?>
                        </td>
                        <td><?= number_format($article['views']) ?></td>
                        <td><?= $article['comment_count'] ?></td>
                        <td><?= formatDate($article['created_at'], 'M j, Y') ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= getArticleURL($article['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/admin/article-edit.php?id=<?= $article['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="?delete=<?= $article['id'] ?>&csrf_token=<?= generateCSRFToken() ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this article?')" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($articles)): ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">No articles found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
