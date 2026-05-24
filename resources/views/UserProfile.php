<h1><?= htmlspecialchars($user['name']) ?>'s Blog</h1>

<hr>

<?php if (empty($posts)): ?>
<p>This user has not published any posts yet.</p>
<?php else: ?>

<?php foreach ($posts as $post): ?>

<div class="blog-card">
<h2>
<a href="<?= post_url($user['username'], $post['slug']) ?>">
<?= htmlspecialchars($post['title']) ?>
</a>
</h2>

<p>
<?= date('M d, Y', strtotime($post['created_at'])) ?>
</p>

<p>
<?= substr(strip_tags($post['content_html'] ?? ''), 0, 150) ?>...
</p>

<a href="http://<?= htmlspecialchars($user['username']) ?>.blogify.dev/<?= htmlspecialchars($post['slug']) ?>">
Read More →
</a>
</div>

<hr>

<?php endforeach; ?>

<?php endif; ?>
