<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Madagascar</title>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="icon" type="image/x-icon" href="/../images/favicon.png">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
		  integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
		  crossorigin="anonymous">
	<link rel="stylesheet" href="/../styles/reset.css">
	<link rel="stylesheet" href="/../styles/styles.css">

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
			integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
			crossorigin="anonymous">
	</script>
	<script defer src="/js/index.js" type="module"></script>
</head>
<body class="body">
	<?php $this->getVariable('header')->display(); ?>
	<main class="main">
		<?php $this->getVariable('content')->display(); ?>
	</main>
	<?php $this->getVariable('footer')->display(); ?>
</body>
