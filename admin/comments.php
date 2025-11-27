<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
session_name(SESSION_NAME);
session_start();
requireAdmin();
$db = getDB();

// Handle actions
if (isset($_GET['action']) && isset($_GET['id']) && isset($_GET['csrf_token'])) {
    if (verifyCSRFToken($_GET['csrf_token'])) {
        $id = intval($_GET['id']);
        $action = $_GET['action'];
        
        if ($action === 'approve') {
            $db->prepare("UPDATE comments SET status = 'approved' WHERE id = ?")->execute([$id]);
            setFlash('success', 'Comment approved');
        } elseif ($action === 'reject') {
            $db->prepare("UPDATE comments SET status = 'rejected' WHERE id = ?")->execute([$id]);
            setFlash('success', 'Comment rejected');
        } elseif ($action === 'spam') {
            $db->prepare("UPDATE comments SET status = 'spam' WHERE id = ?")->execute([$id]);
            setFlash('success', 'Comment marked as spam');
        } elseif ($action === 'delete') {
            $db->prepare("DELETE FROM comments WHERE id = ?")->execute([$id]);
            setFlash('success', 'Comment deleted');
        }
    }
    redirect(BASE_URL . '/admin/comments.php');
}

$comments = $db->query("
    SELECT c.*, a.title as article_title, a.slug as article_slug
    FROM comments c
    INNER JOIN articles a ON c.article_id = a.id
    ORDER BY c.created_at DESC
")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<h2><i class="bi bi-chat-dots"></i> Manage Comments</h2>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr><th>ID</th><th>Author</th><th>Comment</th><th>Article</th><th>Status</th><th>Date</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($comments as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= e($c['name']) ?><br><small class="text-muted"><?= e($c['ip_address']) ?></small></td>
                        <td><?= truncate($c['content'], 80) ?></td>
                        <td><a href="<?= getArticleURL($c['article_slug']) ?>" target="_blank"><?= truncate($c['article_title'], 40) ?></a></td>
                        <td><span class="badge bg-<?= $c['status'] === 'approved' ? 'success' : ($c['status'] === 'pending' ? 'warning' : 'danger') ?>"><?= $c['status'] ?></span></td>
                        <td><?= timeAgo($c['created_at']) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="?action=approve&id=<?= $c['id'] ?>&csrf_token=<?= generateCSRFToken() ?>" class="btn btn-sm btn-success" title="Approve"><i class="bi bi-check"></i></a>
                                <a href="?action=reject&id=<?= $c['id'] ?>&csrf_token=<?= generateCSRFToken() ?>" class="btn btn-sm btn-warning" title="Reject"><i class="bi bi-x"></i></a>
                                <a href="?action=spam&id=<?= $c['id'] ?>&csrf_token=<?= generateCSRFToken() ?>" class="btn btn-sm btn-secondary" title="Spam"><i class="bi bi-shield-exclamation"></i></a>
                                <a href="?action=delete&id=<?= $c['id'] ?>&csrf_token=<?= generateCSRFToken() ?>" class="btn btn-sm btn-danger" onclick="return confirmDelete()" title="Delete"><i class="bi bi-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($comments)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No comments found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
