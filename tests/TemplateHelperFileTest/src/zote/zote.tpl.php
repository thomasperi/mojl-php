<?php return function ($tpl, $props) {
	$icon = __DIR__ . '/../foo/icon.gif';
	?>zote(<?=
		$tpl->file($icon)
	?>)<?php
};
