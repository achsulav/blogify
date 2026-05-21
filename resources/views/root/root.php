<!DOCTYPE html> 
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#000000">
<meta name="description" content="Blogify - Post and See Others">
<link rel="stylesheet" href='/css/Main.css?v=2'>
<?php if ($css = vite_css('resources/js/main.js')): ?>
    <link rel="stylesheet" href="<?= $css ?>">
<?php endif; ?>
</head>
<body>
<div>
<?php require BASE_PATH . '/resources/views/components/navbar.php';?>
</div>
<?= $content ?>
<?php require BASE_PATH . '/resources/views/components/footer.php';?>
<?php require BASE_PATH . '/resources/views/components/toast.php'; ?>
<?php require BASE_PATH . '/resources/views/components/cookie-banner.php'; ?>
<script type="module" src="<?= vite_asset('resources/js/main.js') ?>"></script>
</body>
</html>


