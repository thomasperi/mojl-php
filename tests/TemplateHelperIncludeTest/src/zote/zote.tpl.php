<?php
return function ($tpl, $props) {
	?>zote(<?= $tpl->include('./foo', $props) ?>)<?php
};
