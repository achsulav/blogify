<?php
use App\Foundation\Application;
?>
<link rel="stylesheet" href="/css/editor.css">
<link rel="stylesheet" href="/css/app/PostView.css?v=1.1">

<main class="view-post-page">
    <div class="post-content">
    <div class="post-header">
        <h1 class="post-title"><?= htmlspecialchars($post['title']) ?></h1>
        <div class="post-meta">
            <span class="author-name">By <?= htmlspecialchars($post['author']) ?></span>
            <span class="separator">&bull;</span>
            <span class="post-date"><?= date('M j, Y', strtotime($post['created_at'])) ?></span>
        </div>
    </div>
        <div class="content">
            <?= $post['content_html'] ?>
        </div>
    </div>

<div class="comments-wrapper">
    <div class="comments-header">
        <h3>Responses (<span id="responses-count"><?= count($comments) ?></span>)</h3>
    </div>

    <?php if(Application::$app->session->get('user')): ?>
    <div class="comment-input-card" id="comment-input-card">
        <form id="comment-form" method="POST">
            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
            
            <div class="editor-container comment-editor-container">
                <div class="editor-toolbar" id="comment-toolbar">
                    <div class="toolbar-group">
                        <button type="button" class="toolbar-btn" data-command="bold" title="Bold">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/><path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/></svg>
                        </button>
                        <button type="button" class="toolbar-btn" data-command="italic" title="Italic">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" x2="10" y1="4" y2="4"/><line x1="14" x2="5" y1="20" y2="20"/><line x1="15" x2="9" y1="4" y2="20"/></svg>
                        </button>
                    </div>
                </div>
                <div id="comment-editor" contenteditable="true" placeholder="What are your thoughts?"></div>
                <textarea name="content" id="hidden-content" style="display:none"></textarea>
                
                <div class="editor-footer" id="comment-controls">
                    <button type="button" class="btn-cancel" id="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-respond">Respond</button>
                </div>
            </div>
        </form>
    </div>
    <?php else: ?>
    <div class="login-to-comment">
        <p>You must be <a href="/login">logged in</a> to respond.</p>
    </div>
    <?php endif; ?>

    <div id="comments-container">
    <?php if(empty($comments)): ?>
        <div class="no-comments">
            <p>No responses yet. Be the first to respond.</p>
        </div>
    <?php else: ?>
        <?php foreach($comments as $comment): ?>
        <div class="comment-card" id="comment-<?= $comment['id'] ?>">
            <div class="comment-meta">
                <div class="user-info">
                    <span class="user-name"><?= htmlspecialchars($comment['user_name']) ?></span>
                    <span class="comment-date"><?= date('M j, Y', strtotime($comment['created_at'])) ?></span>
                </div>
                <?php if (Application::$app->session->get('user') == $comment['user_id']): ?>
                <div class="comment-actions">
                    <button onclick="editComment(<?= $comment['id'] ?>)" class="action-link">Edit</button>
                    <button onclick="deleteComment(<?= $comment['id'] ?>)" class="action-link delete">Delete</button>
                </div>
                <?php endif; ?>
            </div>
            <div class="comment-content" id="content-<?= $comment['id'] ?>">
                <?= $comment['content'] // Content is now HTML from the editor ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    </div>
</div>

<?php if(!Application::$app->session->get('user')): ?>
<div class="guest-cta-wrapper">
    <div class="guest-cta">
        <p>Enjoyed this post? Sign in to comment or join Blogify to start your own blog.</p>
        <div class="cta-buttons">
            <a href="/login" class="btn btn-primary">Sign In</a>
            <a href="/register" class="btn btn-primary">Sign Up</a>
        </div>
    </div>
</div>
<?php endif; ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editor = document.getElementById('comment-editor');
    const toolbar = document.getElementById('comment-toolbar');
    const controls = document.getElementById('comment-controls');
    const btnCancel = document.getElementById('btn-cancel');
    const form = document.getElementById('comment-form');
    const hiddenContent = document.getElementById('hidden-content');

    if (editor) {
        // Use event delegation for toolbar buttons (works for edit mode too)
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.toolbar-btn');
            if (btn && btn.hasAttribute('data-command')) {
                e.preventDefault();
                const command = btn.getAttribute('data-command');
                document.execCommand(command, false, null);
                // Try to focus the active editor
                const activeEditor = document.activeElement;
                if (activeEditor && activeEditor.contentEditable === 'true') {
                    activeEditor.focus();
                }
            }
        });

        // Cancel button
        btnCancel.addEventListener('click', () => {
            editor.innerHTML = '';
            editor.classList.remove('focused');
        });

        // Submit form
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Submitting comment...');
                
                const content = editor.innerHTML.trim();
                hiddenContent.value = content;
                
                if (!content || content === '<br>' || content === '<div><br></div>') {
                    alert('Please write something before responding.');
                    return;
                }

                const formData = new FormData(this);
                console.log('FormData:', Object.fromEntries(formData.entries()));
                fetch('/comment/store', {
                    method: 'POST',
                    body: formData,
                    credentials: 'include'
                })
            .then(async response => {
                if (!response.ok) {
                    const text = await response.text();
                    console.error('Server error:', response.status, text);
                    throw new Error('Server responded with ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'error') {
                    alert(data.message);
                    return;
                }
                
                const comment = data.comment;
                const date = new Date(comment.created_at);
                const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                
                const commentHTML = `
                    <div class="comment-card" id="comment-${comment.id}" style="animation: slideIn 0.3s ease-out">
                        <div class="comment-meta">
                            <div class="user-info">
                                <span class="user-name">${comment.user_name}</span>
                                <span class="comment-date">${formattedDate}</span>
                            </div>
                            <div class="comment-actions">
                                <button onclick="editComment(${comment.id})" class="action-link">Edit</button>
                                <button onclick="deleteComment(${comment.id})" class="action-link delete">Delete</button>
                            </div>
                        </div>
                        <div class="comment-content" id="content-${comment.id}">${comment.content}</div>
                    </div>
                `;
                
                const container = document.getElementById('comments-container');
                const noComments = container.querySelector('.no-comments');
                if (noComments) noComments.remove();
                
                container.insertAdjacentHTML('afterbegin', commentHTML);
                
                // Update count
                const countSpan = document.getElementById('responses-count');
                if (countSpan) {
                    countSpan.textContent = parseInt(countSpan.textContent) + 1;
                }
                
                // Reset editor
                editor.innerHTML = '';
                editor.classList.remove('focused');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to post comment. Please try again.');
            });
            });
        }
    }
});

function deleteComment(commentId) {
    if (!confirm('Delete this response?')) return;
    const formData = new FormData();
    formData.append('comment_id', commentId);

    fetch('/comment/delete', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "error") {
            alert(data.message);
            return;
        }

        const element = document.getElementById('comment-' + commentId);
        element.style.opacity = '0';
        element.style.transform = 'translateY(10px)';
        setTimeout(() => {
            element.remove();
            const container = document.getElementById('comments-container');
            if (container.children.length === 0) {
                container.innerHTML = '<div class="no-comments"><p>No responses yet. Be the first to respond.</p></div>';
            }
        }, 300);
    })
    .catch(error => console.error('Error:', error));
}

function editComment(commentId) {
    const contentElement = document.getElementById('content-' + commentId);
    const originalHTML = contentElement.innerHTML;
    
    contentElement.innerHTML = `
        <div class="editor-container edit-editor-card">
            <div class="editor-toolbar">
                <div class="toolbar-group">
                    <button type="button" class="toolbar-btn" onclick="formatEdit(${commentId}, 'bold')" title="Bold">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/><path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/></svg>
                    </button>
                    <button type="button" class="toolbar-btn" onclick="formatEdit(${commentId}, 'italic')" title="Italic">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" x2="10" y1="4" y2="4"/><line x1="14" x2="5" y1="20" y2="20"/><line x1="15" x2="9" y1="4" y2="20"/></svg>
                    </button>
                </div>
            </div>
            <div id="edit-editor-${commentId}" class="edit-editor" contenteditable="true" style="padding: 20px; outline: none; min-height: 100px;">${originalHTML}</div>
            
            <div class="editor-footer" style="display: flex; justify-content: flex-end; padding: 12px 20px; gap: 12px; border-top: 1px solid #f1f5f9; background: #fafafa;">
                <button type="button" onclick="cancelEdit(${commentId}, \`${originalHTML.replace(/`/g, '\\`').replace(/\$/g, '\\$')}\`)" class="btn-cancel">Cancel</button>
                <button type="button" onclick="saveEdit(${commentId})" class="btn-respond">Save</button>
            </div>
        </div>
    `;
    
    const editor = document.getElementById('edit-editor-' + commentId);
    editor.focus();
    
    // Move cursor to end
    const range = document.createRange();
    const sel = window.getSelection();
    range.selectNodeContents(editor);
    range.collapse(false);
    sel.removeAllRanges();
    sel.addRange(range);
}

function formatEdit(commentId, command) {
    const editor = document.getElementById('edit-editor-' + commentId);
    document.execCommand(command, false, null);
    editor.focus();
}

function cancelEdit(commentId, originalHTML) {
    document.getElementById('content-' + commentId).innerHTML = originalHTML;
}

function saveEdit(commentId) {
    const editor = document.getElementById('edit-editor-' + commentId);
    const newContent = editor.innerHTML;
    
    const formData = new FormData();
    formData.append('comment_id', commentId);
    formData.append('content', newContent);

    fetch('/comment/update', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'error') {
            alert(data.message);
            return;
        }
        document.getElementById('content-' + commentId).innerHTML = data.content;
    })
    .catch(error => console.error('Error:', error));
}
</script>
