<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

session_name(SESSION_NAME);
session_start();
requireAdmin();

$db = getDB();

// Get statistics
$stats = [
    'total_articles' => $db->query("SELECT COUNT(*) FROM articles")->fetchColumn(),
    'published_articles' => $db->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn(),
    'total_comments' => $db->query("SELECT COUNT(*) FROM comments")->fetchColumn(),
    'pending_comments' => $db->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn(),
    'total_views' => $db->query("SELECT SUM(views) FROM articles")->fetchColumn(),
];

// Recent articles
$recent_articles = $db->query("SELECT * FROM articles ORDER BY created_at DESC LIMIT 10")->fetchAll();

// Recent comments
$recent_comments = $db->query("
    SELECT c.*, a.title as article_title, a.slug as article_slug
    FROM comments c
    INNER JOIN articles a ON c.article_id = a.id
    ORDER BY c.created_at DESC
    LIMIT 10
")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h3><?= number_format($stats['published_articles']) ?></h3>
                <p class="mb-0">Published Articles</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h3><?= number_format($stats['total_views']) ?></h3>
                <p class="mb-0">Total Views</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h3><?= number_format($stats['pending_comments']) ?></h3>
                <p class="mb-0">Pending Comments</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-newspaper"></i> Recent Articles</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_articles as $article): ?>
                            <tr>
                                <td><a href="<?= getArticleURL($article['slug']) ?>" target="_blank"><?= truncate($article['title'], 40) ?></a></td>
                                <td><span class="badge bg-<?= $article['status'] === 'published' ? 'success' : 'secondary' ?>"><?= $article['status'] ?></span></td>
                                <td><?= number_format($article['views']) ?></td>
                                <td><?= formatDate($article['created_at'], 'M j') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-chat-dots"></i> Recent Comments</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Author</th>
                                <th>Comment</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_comments as $comment): ?>
                            <tr>
                                <td><?= e($comment['name']) ?></td>
                                <td><?= truncate($comment['content'], 30) ?></td>
                                <td><span class="badge bg-<?= $comment['status'] === 'approved' ? 'success' : 'warning' ?>"><?= $comment['status'] ?></span></td>
                                <td><?= timeAgo($comment['created_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
