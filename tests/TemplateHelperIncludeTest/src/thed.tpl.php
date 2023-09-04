<?php
return function ($tpl, $props) {
	?>thed <?= $props['a'] ?> ( <?=
		($props['a'] >= 5) ? 'end' : $tpl->include('./thed', ['a' => $props['a'] + 1])
	?> )<?php
};
