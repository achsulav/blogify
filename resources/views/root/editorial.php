<!DOCTYPE html> 
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#F8F9FA">
<meta name="description" content="Blogify - Professional Publishing Platform">
<title><?= $title ?? 'Blogify | Creator Dashboard' ?></title>
<link rel="stylesheet" href='/css/Main.css?v=2'>
<link rel="stylesheet" href='/css/Editorial.css?v=1'>
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<?php if ($css = vite_css('resources/js/main.js')): ?>
    <link rel="stylesheet" href="<?= $css ?>">
<?php endif; ?>
</head>
<body class="editorial-theme">
    
    <div class="app-container">
        
        <!-- Light Floating Sidebar -->
        <aside class="editorial-sidebar">
            <a href="/dashboard" class="sidebar-logo">Blogify.</a>
            
            <nav class="sidebar-nav">
                <div class="sidebar-section-title">Discover</div>
                <a href="/dashboard" class="nav-item active">
                    <i class='bx bx-compass'></i> Explore
                </a>
                <a href="/trending" class="nav-item">
                    <i class='bx bx-trending-up'></i> Trending
                </a>
                <a href="/bookmarks" class="nav-item">
                    <i class='bx bx-bookmark'></i> Bookmarks
                </a>
                
                <div class="sidebar-section-title">Workspace</div>
                <a href="/my-blogs" class="nav-item">
                    <i class='bx bx-edit-alt'></i> Stories
                </a>
                <a href="/analytics" class="nav-item">
                    <i class='bx bx-bar-chart-alt-2'></i> Analytics
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-preview">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode(\App\Foundation\Application::$app->session->get('user_name') ?? 'U') ?>&background=e9ecef&color=212529" alt="Profile">
                    <div class="user-info">
                        <span class="name"><?= htmlspecialchars(\App\Foundation\Application::$app->session->get('user_name') ?? 'User') ?></span>
                        <span class="role">Creator</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="main-wrapper">
            <!-- Glassmorphism Navbar -->
            <header class="editorial-navbar">
                <div class="nav-search-bar" style="position: relative;">
                    <i class='bx bx-search'></i>
                    <input type="text" id="globalSearchInput" placeholder="Search articles, authors, or topics...">
                    <div id="searchResults" class="search-dropdown" style="display: none;"></div>
                </div>
                
                <div class="nav-actions">
                    <button class="icon-btn theme-toggle">
                        <i class='bx bx-moon'></i>
                    </button>
                    <a href="/create-post" class="btn-publish">
                        <i class='bx bx-pencil'></i> Write
                    </a>
                </div>
            </header>

            <!-- Dynamic Page Content -->
            <main class="page-content">
                <?= $content ?>
            </main>
        </div>
    </div>

    <?php require BASE_PATH . '/resources/views/components/toast.php'; ?>
    <script type="module" src="<?= vite_asset('resources/js/main.js') ?>"></script>
    <script>
        // Simple script for basic interactivity (Theme toggle etc would go here)
    </script>
</body>
</html>
