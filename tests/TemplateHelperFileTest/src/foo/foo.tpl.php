<?php return function ($tpl, $props) {
	$icon = 'icon.gif';
	$options = (is_array($props) && array_key_exists('options', $props)) ? $props['options'] : null;
	?>foo(<?=
		$tpl->file($icon, $options)
	?>)<?php
};
