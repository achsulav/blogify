<!DOCTYPE html> 
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#000000">
<meta name="description" content="Blogify - Search Engine">
<link rel="stylesheet" href='/css/Main.css'>
<?php if ($css = vite_css('resources/js/main.js')): ?>
    <link rel="stylesheet" href="<?= $css ?>">
<?php endif; ?>
<style>
    /* Add any specific styles for the clean layout here */
    body { padding-top: 20px; }
</style>
</head>
<div class="container">
    <?= $content ?>
</div>
<?php require BASE_PATH . '/resources/views/components/toast.php'; ?>
<script type="module" src="<?= vite_asset('resources/js/main.js') ?>"></script>
</body>
</html>
