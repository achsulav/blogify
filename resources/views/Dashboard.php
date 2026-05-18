<?php
use App\Foundation\Application;

$fullName = Application::$app->session->get('user_name');
?>

<div class="dashboard-container">

<div class="dashboard-header">
<h2>Hi, <?= htmlspecialchars($fullName) ?> 👋</h2>
<a href="/create-post" class="btn-primary">+ Create New Post</a>
</div>

<hr>

<?php if (empty($posts)): ?>
<div class="empty-state">
<p>You haven't created any posts yet.</p>
<a href="/create-post" class="btn-secondary">Create Your First Post</a>
</div>
<?php else: ?>

<div class="posts-table-wrapper">
<table class="posts-table">
<thead>
<tr>
<th>Title</th>
<th>Slug</th>
<th>Created</th>
<th>Actions</th>
</tr>
</thead>

<tbody>
<?php foreach ($posts as $post): ?>
<tr>
<td>
<?= htmlspecialchars($post['title']) ?>
</td>

<td>
<a href="http://<?= Application::$app->session->get('username') ?>.blogify.dev/<?= $post['slug'] ?>" target="_blank" class="slug-link">
<?= htmlspecialchars($post['slug']) ?>
</a>
</td>

<td class="date-text">
<?= date('M d, Y', strtotime($post['created_at'])) ?>
</td>
<td class="actions">
<a href="/post/edit/<?= $post['id'] ?>" class="btn-edit">
Edit
</a>
<form method="POST" action="/post/delete" style="display:inline;">
<input type="hidden" name="post_id" value="<?= $post['id'] ?>">
<button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to delete this post?');">Delete</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php endif; ?>
</div>
