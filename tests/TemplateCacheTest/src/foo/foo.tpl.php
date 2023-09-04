<?php return function ($a, $b) {
	static $count = 0;
	return $a . $b . $count++;
};