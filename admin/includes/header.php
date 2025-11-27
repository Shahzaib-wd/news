<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #212529; padding-top: 20px; }
        .sidebar a { color: #adb5bd; text-decoration: none; padding: 10px 20px; display: block; }
        .sidebar a:hover, .sidebar a.active { background-color: #343a40; color: white; }
        .main-content { padding: 20px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-md-block sidebar">
                <div class="text-center text-white mb-4">
                    <h4><i class="bi bi-shield-lock"></i> Admin Panel</h4>
                    <small class="text-muted">Welcome, <?= e($_SESSION['admin_username'] ?? 'Admin') ?></small>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item"><a href="<?= BASE_URL ?>/admin/dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li class="nav-item"><a href="<?= BASE_URL ?>/admin/articles.php" class="<?= in_array(basename($_SERVER['PHP_SELF']), ['articles.php', 'article-edit.php']) ? 'active' : '' ?>"><i class="bi bi-newspaper"></i> Articles</a></li>
                    <li class="nav-item"><a href="<?= BASE_URL ?>/admin/comments.php" class="<?= basename($_SERVER['PHP_SELF']) === 'comments.php' ? 'active' : '' ?>"><i class="bi bi-chat-dots"></i> Comments</a></li>
                    <li class="nav-item"><a href="<?= BASE_URL ?>/admin/categories.php" class="<?= basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active' : '' ?>"><i class="bi bi-folder"></i> Categories</a></li>
                    <li class="nav-item"><a href="<?= BASE_URL ?>/admin/images.php" class="<?= basename($_SERVER['PHP_SELF']) === 'images.php' ? 'active' : '' ?>"><i class="bi bi-image"></i> Images</a></li>
                    <li class="nav-item"><a href="<?= BASE_URL ?>/public/" target="_blank"><i class="bi bi-eye"></i> View Site</a></li>
                    <li class="nav-item"><a href="<?= BASE_URL ?>/admin/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </nav>
            <main class="col-md-10 ms-sm-auto main-content">
                <?php $flash = getFlash(); if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
                    <?= e($flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
