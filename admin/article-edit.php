<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

session_name(SESSION_NAME);
session_start();
requireAdmin();

$db = getDB();
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$article = null;
$existing_tags = [];

if ($article_id > 0) {
    $stmt = $db->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$article_id]);
    $article = $stmt->fetch();
    
    if (!$article) {
        setFlash('error', 'Article not found');
        redirect(BASE_URL . '/admin/articles.php');
    }
    
    // Get existing tags
    $tag_stmt = $db->prepare("SELECT tag_id FROM article_tag WHERE article_id = ?");
    $tag_stmt->execute([$article_id]);
    $existing_tags = array_column($tag_stmt->fetchAll(), 'tag_id');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid security token');
        redirect(BASE_URL . '/admin/article-edit.php' . ($article_id ? "?id=$article_id" : ''));
    }
    
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $body = $_POST['body'] ?? '';
    $category_id = intval($_POST['category_id'] ?? 0);
    $author = trim($_POST['author'] ?? 'Admin');
    $status = $_POST['status'] ?? 'draft';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $meta_description = trim($_POST['meta_description'] ?? '');
    $selected_tags = $_POST['tags'] ?? [];
    
    $slug = $article ? $article['slug'] : generateSlug($title);
    $featured_image = $article['featured_image'] ?? '';
    
    // Handle image upload
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        try {
            $new_image = uploadImage($_FILES['featured_image'], 'articles');
            if ($featured_image) {
                deleteImage($featured_image);
            }
            $featured_image = $new_image;
        } catch (Exception $e) {
            setFlash('error', 'Image upload failed: ' . $e->getMessage());
            redirect($_SERVER['REQUEST_URI']);
        }
    }
    
    if ($article_id) {
        // Update
        $stmt = $db->prepare("
            UPDATE articles SET title=?, subtitle=?, body=?, featured_image=?, category_id=?, 
                   author=?, status=?, is_featured=?, meta_description=?, slug=?
            WHERE id=?
        ");
        $stmt->execute([$title, $subtitle, $body, $featured_image, $category_id, $author, $status, $is_featured, $meta_description, $slug, $article_id]);
        
        // Update tags
        $db->prepare("DELETE FROM article_tag WHERE article_id = ?")->execute([$article_id]);
        foreach ($selected_tags as $tag_id) {
            $db->prepare("INSERT INTO article_tag (article_id, tag_id) VALUES (?, ?)")->execute([$article_id, $tag_id]);
        }
        
        setFlash('success', 'Article updated successfully');
        redirect(BASE_URL . '/admin/articles.php');
    } else {
        // Insert
        $stmt = $db->prepare("
            INSERT INTO articles (title, subtitle, body, featured_image, category_id, author, status, is_featured, meta_description, slug)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $subtitle, $body, $featured_image, $category_id, $author, $status, $is_featured, $meta_description, $slug]);
        $new_id = $db->lastInsertId();
        
        // Insert tags
        foreach ($selected_tags as $tag_id) {
            $db->prepare("INSERT INTO article_tag (article_id, tag_id) VALUES (?, ?)")->execute([$new_id, $tag_id]);
        }
        
        generateSitemap(); // Regenerate sitemap
        setFlash('success', 'Article created successfully');
        redirect(BASE_URL . '/admin/articles.php');
    }
}

$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$tags = $db->query("SELECT * FROM tags ORDER BY name")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<h2><i class="bi bi-pencil-square"></i> <?= $article_id ? 'Edit' : 'New' ?> Article</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" required 
                               value="<?= e($article['title'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subtitle</label>
                        <input type="text" name="subtitle" class="form-control" 
                               value="<?= e($article['subtitle'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Body *</label>
                        <textarea name="body" class="form-control rich-editor" rows="20" required><?= e($article['body'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta Description (SEO)</label>
                        <textarea name="meta_description" class="form-control" rows="2" maxlength="160"><?= e($article['meta_description'] ?? '') ?></textarea>
                        <small class="text-muted">Max 160 characters for search engines</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Publish</strong></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" <?= ($article['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= ($article['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="archived" <?= ($article['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="0">None</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($article['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Author</label>
                        <input type="text" name="author" class="form-control" value="<?= e($article['author'] ?? 'Admin') ?>">
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_featured" class="form-check-input" id="featured" 
                               <?= ($article['is_featured'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="featured">Featured Article</label>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> <?= $article_id ? 'Update' : 'Publish' ?>
                        </button>
                        <a href="<?= BASE_URL ?>/admin/articles.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Featured Image</strong></div>
                <div class="card-body">
                    <?php if ($article && $article['featured_image']): ?>
                        <img src="<?= UPLOADS_URL . '/' . e($article['featured_image']) ?>" class="img-fluid mb-2">
                    <?php endif; ?>
                    <input type="file" name="featured_image" class="form-control" accept="image/*">
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-white"><strong>Tags</strong></div>
                <div class="card-body">
                    <?php foreach ($tags as $tag): ?>
                        <div class="form-check">
                            <input type="checkbox" name="tags[]" value="<?= $tag['id'] ?>" class="form-check-input" 
                                   id="tag<?= $tag['id'] ?>" <?= in_array($tag['id'], $existing_tags) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="tag<?= $tag['id'] ?>"><?= e($tag['name']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
