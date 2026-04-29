<?php
use App\Foundation\Application;
?>
<div class="post-content">
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <div class="content">
        <?= $post['content_html'] ?>
    </div>
</div>
<h3>Leave a Comment</h3>

<?php if(Application::$app->session->get('user')): ?>
<form id="comment-form">
<input type="hidden" name="post_id" value="<?= $post['id'] ?>">
<textarea name="content" required placeholder="Write your comment..." rows="4"></textarea>
<button type="submit">Post Comment</button>
</form>
<?php else: ?>
<p>You must be logged in to comment</p>
<?php endif; ?>
<h3>Comments</h3>
<div id="comments-container">
<?php if(empty($comments)): ?>
<p>No comment yet.</p>
<?php else: ?>
<?php foreach($comments as $comment): ?>
<div class="comment" id="comment-<?= $comment['id'] ?>">
<strong><?= htmlspecialchars($comment['user_name']) ?></strong>
<p class="comment-content" id="content-<?= $comment['id'] ?>">
<?= nl2br(htmlspecialchars($comment['content'])) ?></p>

<small><?= $comment['created_at'] ?></small>
<?php if (Application::$app->session->get('user') == $comment['user_id']): ?>
<br>
<!-- EDIT FORM -->
<button onclick="editComment(<?= $comment['id'] ?>)">Edit</button>
<!-- DELETE BUTTON -->
<button onclick="deleteComment(<?= $comment['id'] ?>)">Delete</button>

<?php endif; ?>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>

<?php if(!Application::$app->session->get('user')): ?>
<div class="guest-cta" style="margin-top: 40px; padding: 20px; border-top: 1px solid #eee; text-align: center;">
    <p>Enjoyed this post? Sign in to comment or join Blogify to start your own blog.</p>
    <div style="margin-top: 15px;">
        <a href="/login" class="btn" style="margin-right: 10px;">Sign In</a>
        <a href="/register" class="btn btn-outline">Sign Up</a>
    </div>
</div>
<?php endif; ?>

<script>
const form = document.getElementById('comment-form');
if(form){
  form.addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    fetch('/comment/store', {
      method:'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if(data.status === 'error'){
        alert(data.message);
        return;
      }
      const comment = data.comment;
      const commentHTML = `<div class="comment" id="comment-${comment.id}" style="margin-bottom:20px;">
      <strong>${comment.user_name}</strong>
      <p>${comment.content}</p>
      <small>${comment.created_at}</small>
      <button onclick="deleteComment(${comment.id})">Delete</button>
      </div>`;
      const container = document.getElementById('comments-container');

      if(container.innerHTML.includes('No comment yet')) {
        container.innerHTML = '';
      }
      container.insertAdjacentHTML('afterbegin', commentHTML);
      form.reset();
    })
    .catch(error => console.error(error));
  });
}

function deleteComment(commentId){
  if(!confirm('Delete this comment?')) return;
  const formData = new FormData();
  formData.append('comment_id', commentId);

  fetch('/comment/delete', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if(data.status === "error"){
      alert(data.message);
      return;
    }

    const commentElement = document.getElementById('comment-' + commentId);
    if(commentElement){
      commentElement.remove();
    }
    const container = document.getElementById('comments-container');
    if(container.children.length === 0){
      container.innerHTML = '<p>No comment yet</p>';
    }
  })
  .catch(error => console.error(error));
}
function editComment(commentId){
  const contentElement = document.getElementById('content-' + commentId);
  const originalText = contentElement.innerText;
  contentElement.innerHTML = 
    `
    <textarea id="edit-textarea-${commentId}" rows="3">${originalText}</textarea>
<br>
<button onclick="saveEdit(${commentId})">Save</button>
<button onclick="cancelEdit(${commentId}, \`${originalText}\')">Cancel</button>
    `;
}
function cancelEdit(commentId,originalText){
  const contentElement = document.getElementById('content-' + commentId);
  contentElement.innerHTML = originalText;
}
function saveEdit(commentId){
  const textarea = document.getElementById('edit-textarea-' + commentId);
  const newContent = textarea.value;
  const formData = new FormData();
  formData.append('comment_id',commentId);
  formData.append('content',newContent);
  fetch('/comment/update',{
  method:'POST',
    body:formData
  })
    .then(response=>response.json())
    .then(data=>{
    if(data.status === 'error'){
      alert(data.message);
      return;

    }
    const contentElement = document.getElementById('content-'+ commentId);
    contentElement.innerHTML = data.content;

  })

    .catch(error=>
    console.error(error));
}
</script>
