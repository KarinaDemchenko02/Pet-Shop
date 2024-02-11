<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Madagascar</title>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="icon" type="image/x-icon" href="/../images/favicon.png">
	<link rel="stylesheet" href="/../styles/reset.css">
	<link rel="stylesheet" href="/../styles/admin.css">

	<script
		src="https://code.jquery.com/jquery-3.7.1.min.js"
		integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
		crossorigin="anonymous">
	</script>

	<script defer src="/js/admin/ajaxChange.js"></script>

	<script defer src="/js/admin/index.js" type="module"></script>
<body>
	<?php $this->getVariable('header')->display(); ?>
	<main>
		<?php $this->getVariable('table')->display(); ?>
	</main>
</body>