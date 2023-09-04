<?php
return function ($tpl, $props) {
	static $count = 0;
	?>sneg(<?= $count++ ?>)<?php
};
