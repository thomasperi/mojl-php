<?php return function ($tpl, $props) {
	?>bar(<?= $tpl->include('src/foo', $props) ?>)<?php
};
