<?php
use App\Foundation\Application;
$fullName = Application::$app->session->get('user_name');
?>

<?php if (!in_array($userRole ?? '', ['Primary Audience', 'Reader'])): ?>
<!-- Creator Dashboard Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span>Total Views</span>
            <i class='bx bx-trending-up'></i>
        </div>
        <div class="stat-value"><?= number_format($stats['views'] ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span>Engagement Rate</span>
            <i class='bx bx-heart'></i>
        </div>
        <div class="stat-value"><?= htmlspecialchars($stats['engagement'] ?? '0%') ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-header">
            <span>Published Stories</span>
            <i class='bx bx-book-open'></i>
        </div>
        <div class="stat-value"><?= number_format($stats['published'] ?? 0) ?></div>
    </div>
</div>
<?php endif; ?>

<div class="section-header">
    <h2 class="section-title">Based on Your Interests</h2>
</div>

<div id="personalizedFeedContainer">
    <div style="padding: 40px; text-align: center; color: var(--text-muted);">
        <i class='bx bx-loader-alt bx-spin' style="font-size: 2rem;"></i>
        <p>Curating your personalized feed...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const container = document.getElementById('personalizedFeedContainer');
    
    try {
        const response = await fetch('/api/feed/for-you');
        const result = await response.json();
        
        if (!result.success) {
            container.innerHTML = `<p style="color: red; text-align: center;">Error loading feed</p>`;
            return;
        }

        console.log("=== RECOMMENDATION ENGINE DEBUG ===");
        console.log("Current User:", result.debug.current_user_id);
        console.log("Selected Interests:", result.debug.selected_interests_names);
        console.log("Total Posts Fetched:", result.debug.total_returned);
        console.log("Strict Category Matches Found:", result.debug.matched_categories_count);
        console.log("Generated SQL:", result.debug.generated_sql);
        console.log("===================================");

        if (result.data.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 60px 0; color: var(--text-muted);">
                    <i class='bx bx-ghost' style="font-size: 48px; margin-bottom: 16px;"></i>
                    <p>No stories found. Try updating your interests.</p>
                    <a href="/settings/interests" class="btn-primary" style="margin-top: 15px; display: inline-block;">Update Interests</a>
                </div>
            `;
            return;
        }

        let html = '<div class="feed-grid">';
        
        // Render Featured Hero (First Post)
        const featured = result.data[0];
        html += `
        <a href="${featured.url}" class="featured-article">
            <div class="featured-image">
                <img src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=800&q=80" alt="Featured">
            </div>
            <div class="featured-content">
                <span class="article-category">
                    ${featured.category_name} 
                    ${featured.category_match ? '<span style="background: #eef2ff; color: #4F46E5; padding: 2px 6px; border-radius: 4px; margin-left: 8px; font-weight: bold;">★ Interest Match</span>' : ''}
                </span>
                <h2>${featured.title}</h2>
                <p>${featured.excerpt}</p>
                
                <div class="article-meta">
                    <div class="author-block">
                        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(featured.author_username)}&background=e9ecef&color=212529" alt="Author">
                        <span>${featured.author_username}</span>
                    </div>
                </div>
            </div>
        </a>
        `;

        // Render remaining posts
        for (let i = 1; i < result.data.length; i++) {
            const post = result.data[i];
            let tagsHtml = '';
            if (post.tags && post.tags.length > 0) {
                tagsHtml = `<span style="color: var(--text-muted); margin-left: 5px;">&bull; ${post.tags[0]}</span>`;
            }

            const likeClass = post.is_liked ? 'liked' : '';
            const likeIcon = post.is_liked ? 'bxs-heart' : 'bx-heart';
            const bookmarkClass = post.is_bookmarked ? 'active' : '';
            const bookmarkIcon = post.is_bookmarked ? 'bxs-bookmark' : 'bx-bookmark';

            html += `
            <a href="${post.url}" class="article-card">
                <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=400&q=80" alt="Cover" class="article-card-img">
                <div class="article-card-content">
                    <div style="font-size: 11px; text-transform: uppercase; color: #4F46E5; font-weight: 600; margin-bottom: 8px;">
                        ${post.category_name} ${tagsHtml}
                        ${post.category_match ? '<span style="color: #10b981; margin-left: 5px;" title="Interest Match">★</span>' : ''}
                    </div>
                    <h3>${post.title}</h3>
                    <p>${post.excerpt}</p>
                    
                    <div class="article-meta">
                        <div class="author-block">
                            <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(post.author_username)}&background=e9ecef&color=212529" alt="Author">
                            <span>${post.author_username}</span>
                        </div>
                        <div class="engagement-actions" onclick="event.preventDefault();">
                            <button class="engagement-btn toggle-bookmark ${bookmarkClass}" data-id="${post.id}">
                                <i class='bx ${bookmarkIcon}'></i>
                            </button>
                            <button class="engagement-btn toggle-like ${likeClass}" data-id="${post.id}">
                                <i class='bx ${likeIcon}'></i>
                                <span class="likes-count">${post.likes_count > 0 ? post.likes_count : ''}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </a>
            `;
        }

        html += `</div>`;
        container.innerHTML = html;

        // Re-attach event listeners for engagement buttons since they are dynamically added
        // Since main.js already runs before this, we can just dispatch a custom event or attach manually
        document.querySelectorAll('.toggle-like').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                e.stopPropagation();
                const postId = btn.getAttribute('data-id');
                const icon = btn.querySelector('i');
                const countSpan = btn.querySelector('.likes-count');
                
                try {
                    const res = await fetch('/api/engagement/like', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ post_id: postId })
                    });
                    const data = await res.json();
                    if (data.success) {
                        if (data.action === 'liked') {
                            btn.classList.add('liked');
                            icon.classList.replace('bx-heart', 'bxs-heart');
                        } else {
                            btn.classList.remove('liked');
                            icon.classList.replace('bxs-heart', 'bx-heart');
                        }
                        countSpan.textContent = data.total_likes > 0 ? data.total_likes : '';
                    }
                } catch (err) {
                    console.error('Error toggling like:', err);
                }
            });
        });

        document.querySelectorAll('.toggle-bookmark').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                e.stopPropagation();
                const postId = btn.getAttribute('data-id');
                const icon = btn.querySelector('i');
                
                try {
                    const res = await fetch('/api/engagement/bookmark', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ post_id: postId })
                    });
                    const data = await res.json();
                    if (data.success) {
                        if (data.action === 'bookmarked') {
                            btn.classList.add('active');
                            icon.classList.replace('bx-bookmark', 'bxs-bookmark');
                        } else {
                            btn.classList.remove('active');
                            icon.classList.replace('bxs-bookmark', 'bx-bookmark');
                        }
                    }
                } catch (err) {
                    console.error('Error toggling bookmark:', err);
                }
            });
        });

    } catch (err) {
        console.error("Failed to load personalized feed:", err);
        container.innerHTML = `<p style="color: red; text-align: center;">Failed to connect to recommendation engine.</p>`;
    }
});
</script>
