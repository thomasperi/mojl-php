<?php return function ($tpl, $props) {
	$tpl->include('src/shell', [
		'title' => 'Home',
		'content' => function () {
			echo '<p>This is the home page</p>';
		}
	]);
};