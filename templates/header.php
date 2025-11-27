<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

setVisitorCookie();

// Get categories for navigation
$db = getDB();
$categories_stmt = $db->query("SELECT name, slug FROM categories ORDER BY name LIMIT 8");
$categories = $categories_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <?php if (isset($page_title)): ?>
        <title><?= e($page_title) ?> - <?= e(SITE_NAME) ?></title>
    <?php else: ?>
        <title><?= e(SITE_NAME) ?> - <?= e(SITE_TAGLINE) ?></title>
    <?php endif; ?>
    
    <?php if (isset($meta_description)): ?>
        <meta name="description" content="<?= e($meta_description) ?>">
    <?php else: ?>
        <meta name="description" content="<?= e(SITE_TAGLINE) ?>">
    <?php endif; ?>
    
    <?php if (isset($canonical_url)): ?>
        <link rel="canonical" href="<?= e($canonical_url) ?>">
    <?php endif; ?>
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?= isset($og_type) ? e($og_type) : 'website' ?>">
    <meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
    <?php if (isset($og_title)): ?>
        <meta property="og:title" content="<?= e($og_title) ?>">
    <?php endif; ?>
    <?php if (isset($og_description)): ?>
        <meta property="og:description" content="<?= e($og_description) ?>">
    <?php endif; ?>
    <?php if (isset($og_image)): ?>
        <meta property="og:image" content="<?= e($og_image) ?>">
    <?php endif; ?>
    <?php if (isset($og_url)): ?>
        <meta property="og:url" content="<?= e($og_url) ?>">
    <?php endif; ?>
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <?php if (isset($og_title)): ?>
        <meta name="twitter:title" content="<?= e($og_title) ?>">
    <?php endif; ?>
    <?php if (isset($og_description)): ?>
        <meta name="twitter:description" content="<?= e($og_description) ?>">
    <?php endif; ?>
    <?php if (isset($og_image)): ?>
        <meta name="twitter:image" content="<?= e($og_image) ?>">
    <?php endif; ?>
    
    <?php if (isset($article_schema)): ?>
        <!-- JSON-LD Article Schema -->
        <script type="application/ld+json">
        <?= $article_schema ?>
        </script>
    <?php endif; ?>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= ASSETS_URL ?>/images/favicon.png">
</head>
<body>
    <!-- Header -->
    <header class="sticky-top bg-white border-bottom">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand fw-bold text-primary fs-4" href="<?= BASE_URL ?>/public/">
                    <i class="bi bi-globe2"></i> <?= e(SITE_NAME) ?>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/public/">Home</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                                Categories
                            </a>
                            <ul class="dropdown-menu">
                                <?php foreach ($categories as $cat): ?>
                                    <li><a class="dropdown-item" href="<?= getCategoryURL($cat['slug']) ?>"><?= e($cat['name']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/public/search.php">
                                <i class="bi bi-search"></i> Search
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <?php 
    // Display flash messages
    $flash = getFlash();
    if ($flash): 
    ?>
    <div class="container mt-3">
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>
