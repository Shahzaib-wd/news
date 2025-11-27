<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
session_name(SESSION_NAME);
session_start();
requireAdmin();
$db = getDB();

if (isset($_GET['delete'])) {
    if (verifyCSRFToken($_GET['csrf_token'] ?? '')) {
        $id = intval($_GET['delete']);
        $stmt = $db->prepare("SELECT path FROM images WHERE id = ?");
        $stmt->execute([$id]);
        $img = $stmt->fetch();
        if ($img) {
            deleteImage($img['path']);
            setFlash('success', 'Image deleted');
        }
    }
    redirect(BASE_URL . '/admin/images.php');
}

$images = $db->query("SELECT * FROM images ORDER BY uploaded_at DESC")->fetchAll();
include __DIR__ . '/includes/header.php';
?>

<h2><i class="bi bi-image"></i> Image Library</h2>

<div class="card">
    <div class="card-body">
        <div class="row g-3">
            <?php foreach ($images as $img): ?>
            <div class="col-md-2">
                <div class="card">
                    <img src="<?= UPLOADS_URL . '/' . e($img['path']) ?>" class="card-img-top" alt="">
                    <div class="card-body p-2">
                        <small class="d-block text-truncate"><?= e($img['filename']) ?></small>
                        <small class="text-muted"><?= number_format($img['size'] / 1024, 1) ?> KB</small>
                        <a href="?delete=<?= $img['id'] ?>&csrf_token=<?= generateCSRFToken() ?>" 
                           class="btn btn-sm btn-danger w-100 mt-1" 
                           onclick="return confirmDelete()">Delete</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($images)): ?>
            <div class="col-12"><p class="text-muted">No images uploaded yet.</p></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
