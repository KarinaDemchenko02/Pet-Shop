<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Madagascar</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/../images/favicon.png">
    <link rel="stylesheet" href="/../styles/reset.css">
    <link rel="stylesheet" href="/../styles/styles.css">
    <script defer src="https://unpkg.com/js-image-zoom/js-image-zoom.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/js-image-zoom/js-image-zoom.min.js"></script>
    <script defer src="/js/index.js" type="module"></script>
</head>
<body>
    <header class="header">
        <?php $this->getVariable('header')->display() ?>
    </header>
    <main class="main">
        <?php $this->getVariable('content')->display() ?>
    </main>
    <footer class="footer">
		<?php $this->getVariable('footer')->display() ?>
    </footer>
</body>
