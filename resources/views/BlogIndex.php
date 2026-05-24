<div class="blog-list-container">
    <form method="GET" action="/blog" class="search-form">
        <input type="text" name="search" placeholder="Search posts.." value="<?= htmlspecialchars($search ?? '') ?>">
        <button type="submit">Search</button>
    </form>
    <h1>Latest Blog Posts</h1>
</div>

<?php if (empty($posts)): ?>
<p>No result found.</p>
<?php else: ?>

<div class="blog-list">

<?php foreach ($posts as $post): ?>

<div class="blog-card">

<h2>
<a href="<?= post_url($post['username'], $post['slug']) ?>">
<?= htmlspecialchars($post['title']) ?>
</a>
</h2>

<p class="meta">
By <?= htmlspecialchars($post['author']) ?> •
<?= date('M d, Y', strtotime($post['created_at'])) ?>
</p>

<p class="excerpt">
<?= substr(strip_tags($post['content_html'] ?? ''), 0, 150) ?>...
</p>

<a href="<?= post_url($post['username'], $post['slug']) ?>" class="read-more">
Read More →
</a>

</div>

<?php endforeach; ?>

</div>

<?php endif; ?>


<style>
.blog-list-container, .blog-list {
    max-width: 900px;
    margin: 40px auto;
    padding: 0 20px;
}

.search-form {
    margin-bottom: 20px;
    display: flex;
    gap: 10px;
}

.search-form input {
    flex: 1;
    padding: 10px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
}

.search-form button {
    padding: 10px 20px;
    background: #000;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.blog-card {
    border-bottom: 1px solid #e5e7eb;
    padding: 20px 0;
}

.blog-card h2 {
    margin-bottom: 5px;
}

.blog-card a {
    text-decoration: none;
    color: #111827;
}

.blog-card a:hover {
    color: #4f46e5;
}

.meta {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 10px;
}

.excerpt {
    margin-bottom: 10px;
}

.read-more {
    color: #4f46e5;
    font-weight: bold;
}
</style>
<?php if($totalPages > 1): ?>
<div class="pagination">
<?php if($currentPage > 1): ?>
<a href="/blog?page=<?= $currentPage - 1?>&search=<?= urlencode($search) ?>">Prev</a>
<?php endif;?>
<?php for($i=1; $i <= $totalPages; $i++):?>
  <a href="/blog?page=<?= $i?>&search=<?=urlencode($search) ?>"
  class="<?= $i == $currentPage ? 'active' : '' ?>">
<?= $i ?>
</a>
<?php endfor; ?>
<?php if($currentPage < $totalPages): ?>
  <a href="/blog?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>">Next</a>
<?php endif; ?>
</div>
<?php endif; ?>


