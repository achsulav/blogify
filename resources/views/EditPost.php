<link rel="stylesheet" href="/css/editor.css">
<form method="POST" action="/post/update/<?= $post['id'] ?>">
<div class="editor-main-layout">
    <div class="editor-content-area">
        <div class="editor-container">
            <div class="editor-toolbar">
                <div class="toolbar-group">
                    <div class="custom-select-wrapper">
                        <select id="heading_select" class="custom-select">
                            <option value="">Normal</option>
                            <option value="1">Heading 1</option>
                            <option value="2">Heading 2</option>
                            <option value="3">Heading 3</option>
                        </select>
                    </div>
                    <div class="custom-select-wrapper">
                        <select id="font_select" class="custom-select">
                            <option value="">Inter</option>
                            <option value="Sailec Light">Sailec Light</option>
                            <option value="Sofia Pro">Sofia Pro</option>
                            <option value="Roboto Slab">Roboto Slab</option>
                            <option value="Ubuntu Mono">Ubuntu Mono</option>
                        </select>
                    </div>
                </div>

                <div class="toolbar-group">
                    <button type="button" id="bold_btn" title="Bold">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/><path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"/></svg>
                    </button>
                    <button type="button" id="italic_btn" title="Italic">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" x2="10" y1="4" y2="4"/><line x1="14" x2="5" y1="20" y2="20"/><line x1="15" x2="9" y1="4" y2="20"/></svg>
                    </button>
                    <button type="button" id="underline_btn" title="Underline">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4v6a6 6 0 0 0 12 0V4"/><line x1="4" x2="20" y1="20" y2="20"/></svg>
                    </button>
                </div>

                <div class="toolbar-group">
                    <button type="button" id="ordered_btn" title="Numbered List">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="10" x2="21" y1="6" y2="6"/><line x1="10" x2="21" y1="12" y2="12"/><line x1="10" x2="21" y1="18" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg>
                    </button>
                    <button type="button" id="bullet_btn" title="Bullet List">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/></svg>
                    </button>
                    <button type="button" id="align_btn" title="Align Left">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="21" x2="3" y1="6" y2="6"/><line x1="15" x2="3" y1="12" y2="12"/><line x1="17" x2="3" y1="18" y2="18"/></svg>
                    </button>
                </div>

                <div class="toolbar-group">
                    <button type="button" id="link_btn" title="Link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    </button>
                    <button type="button" id="image_btn" title="Image">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    </button>
                </div>

                <input type="file" id="image_upload" accept="image/*" style="display:none;">
            </div>
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" class="title-input" required>
            <div id="editor"></div>
        </div>
    </div>

    <aside class="editor-sidebar">
        <div class="sidebar-section">
            <h3>Update</h3>
            <button type="submit" class="publish-btn">Update Post</button>
        </div>

        <div class="sidebar-section">
            <h3>Categorization</h3>
            <select name="category_id" class="category-select" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= $post['category_id'] == $category['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['name'])?>
                </option>
                <?php endforeach;?>
            </select>
        </div>

        <div class="sidebar-section">
            <h3>Snapshots</h3>
            <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                <input type="text" id="commit_message_input" placeholder="Version tag...">
                <button type="button" id="commit_btn" class="sidebar-action-btn" style="padding: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                </button>
            </div>
            <div id="history_container" style="max-height: 200px; overflow-y: auto; font-size: 13px;"></div>
        </div>

        <div class="sidebar-section">
            <h3>Actions</h3>
            <button type="button" id="import_md_btn" class="sidebar-action-btn" title="Import Markdown">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><line x1="10" x2="8" y1="9" y2="9"/></svg>
                Import Markdown
            </button>
            <input type="file" id="md_file_input" accept=".md,text/markdown" style="display:none;">
        </div>
    </aside>
</div>
<input type="hidden" name="content_html" id="content_input" value="<?= htmlspecialchars($post['content_html']) ?>">
</form>

<script type="module" src="<?= vite_asset('@vite/client') ?>"></script>
<script type="module" src="<?= vite_asset('resources/js/editor.js') ?>"></script>

