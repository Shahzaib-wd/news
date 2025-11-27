<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
session_name(SESSION_NAME);
session_start();
requireAdmin();
$db = getDB();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid security token');
        redirect(BASE_URL . '/admin/categories.php');
    }
    
    if ($_POST['action'] === 'add') {
        $name = trim($_POST['name']);
        $slug = generateSlug($name);
        $description = trim($_POST['description'] ?? '');
        
        $stmt = $db->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
        $stmt->execute([$name, $slug, $description]);
        setFlash('success', 'Category added');
    } elseif ($_POST['action'] === 'edit') {
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $description = trim($_POST['description'] ?? '');
        
        $stmt = $db->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $description, $id]);
        setFlash('success', 'Category updated');
    }
    redirect(BASE_URL . '/admin/categories.php');
}

if (isset($_GET['delete'])) {
    if (verifyCSRFToken($_GET['csrf_token'] ?? '')) {
        $db->prepare("DELETE FROM categories WHERE id = ?")->execute([intval($_GET['delete'])]);
        setFlash('success', 'Category deleted');
    }
    redirect(BASE_URL . '/admin/categories.php');
}

$categories = $db->query("
    SELECT c.*, COUNT(a.id) as article_count 
    FROM categories c
    LEFT JOIN articles a ON c.id = a.category_id
    GROUP BY c.id
    ORDER BY c.name
")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<h2><i class="bi bi-folder"></i> Manage Categories</h2>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white"><strong>Add New Category</strong></div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Category</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr><th>Name</th><th>Slug</th><th>Articles</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><strong><?= e($cat['name']) ?></strong><br><small class="text-muted"><?= e($cat['description']) ?></small></td>
                            <td><code><?= e($cat['slug']) ?></code></td>
                            <td><?= $cat['article_count'] ?></td>
                            <td>
                                <a href="?delete=<?= $cat['id'] ?>&csrf_token=<?= generateCSRFToken() ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirmDelete('Delete this category?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
