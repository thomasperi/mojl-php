<?php return function ($tpl, $props) {
	echo $tpl->include('src/shell', [
		'title' => 'About',
		'content' => function () {
			echo '<p>This is the about page</p>';
		}
	]);
};