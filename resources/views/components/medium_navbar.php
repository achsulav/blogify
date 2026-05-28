<nav class="medium-navbar">
    <div class="nav-left">
        <a href="/dashboard" class="nav-logo">
            <h2>Blogify</h2>
        </a>
        <div class="nav-search">
            <i class='bx bx-search'></i>
            <input type="text" placeholder="Search Blogify...">
        </div>
    </div>
    
    <div class="nav-right">
        <a href="/create-post" class="nav-write-btn">
            <i class='bx bx-edit-alt'></i> Write
        </a>
        <button class="icon-btn theme-toggle">
            <i class='bx bx-moon'></i>
        </button>
        <button class="icon-btn">
            <i class='bx bx-bell'></i>
        </button>
        <div class="nav-profile">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode(\App\Foundation\Application::$app->session->get('user_name') ?? 'U') ?>&background=random" alt="Profile" class="avatar">
        </div>
    </div>
</nav>
