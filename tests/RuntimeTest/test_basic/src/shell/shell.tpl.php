<?php return function ($tpl, $props) { ?>
<!DOCTYPE html>
<html>
<head>
	<title><?= $props['title'] ?> | Example Site</title>
	<?= $tpl->styles() ?>
</head>
<body>
	<h1><?= $props['title'] ?></h1>
	<?= $props['content']() ?>
	<?= $tpl->include('src/footer') ?>
	<?= $tpl->scripts() ?>
</body>
</html>
<?php };