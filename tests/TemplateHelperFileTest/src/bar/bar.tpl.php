<?php return function ($tpl, $props) {
	$icon = '../foo/icon.gif';
	$options = (is_array($props) && array_key_exists('options', $props)) ? $props['options'] : null;
	?>bar(<?=
		$tpl->file($icon, $options)
	?>)<?php
};
