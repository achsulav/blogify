<!DOCTYPE html> 
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#ffffff">
<meta name="description" content="Blogify - Medium-style Blogging Platform">
<title><?= $title ?? 'Blogify' ?></title>
<link rel="stylesheet" href='/css/Main.css?v=2'>
<link rel="stylesheet" href='/css/Medium.css?v=1'>
<!-- Use BoxIcons for modern icons -->
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<?php if ($css = vite_css('resources/js/main.js')): ?>
    <link rel="stylesheet" href="<?= $css ?>">
<?php endif; ?>
</head>
<body class="medium-theme">
    
    <?php require BASE_PATH . '/resources/views/components/medium_navbar.php';?>

    <div class="medium-layout">
        <!-- Left Sidebar -->
        <aside class="medium-sidebar-left">
            <?php require BASE_PATH . '/resources/views/components/medium_sidebar.php';?>
        </aside>

        <!-- Main Content (Feed) -->
        <main class="medium-main-content">
            <?= $content ?>
        </main>

        <!-- Right Sidebar (Trending/Recommendations) -->
        <aside class="medium-sidebar-right">
            <div class="sidebar-widget">
                <h3>Trending Topics</h3>
                <div class="topic-tags">
                    <span class="tag">Technology</span>
                    <span class="tag">Programming</span>
                    <span class="tag">AI</span>
                    <span class="tag">Design</span>
                </div>
            </div>
            <div class="sidebar-widget">
                <h3>Recommended Authors</h3>
                <!-- Recommended authors list will go here -->
                <p class="text-muted">Discover more writers...</p>
            </div>
            
            <div class="sidebar-footer">
                <a href="#">Help</a>
                <a href="#">Status</a>
                <a href="#">Writers</a>
                <a href="#">Blog</a>
                <a href="#">Careers</a>
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
                <a href="#">About</a>
            </div>
        </aside>
    </div>

    <?php require BASE_PATH . '/resources/views/components/toast.php'; ?>
    <script type="module" src="<?= vite_asset('resources/js/main.js') ?>"></script>
</body>
</html>
