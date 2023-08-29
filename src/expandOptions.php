<?php
namespace Mojl;

function expandOptions($options) {
	$defaults = [
		'base' => '',
	// to-do:	'buildDevDir' => 'dev',
	// to-do:	'buildDistDir' => 'dist',
	// to-do:	'buildAssetsDir' => 'assets',
		'maxIncludeDepth' => 100,
	// to-do:	'pageRelativeUrls' => false,
	// to-do:	'trimIncludes' => true,
		'isDev' => false,
	];
	
	$expanded = [];
	if (!is_array($options)) {
		$options = [];
	}
	
	if (!array_key_exists('base', $options)) {
		throw new \Exception('no base specified');
	}
	
	// Populate missing options and ensure data types on top-level options are correct.
	foreach ($defaults as $key => $default) {
		$value = array_key_exists($key, $options) ? $options[$key] : $default;
		$actualType = gettype($value);
		$expectedType = gettype($default);

		if ($expectedType !== $actualType) {
			throw new \Exception("expected '$key' option to be $expectedType but got $actualType instead");
		}
		
		$expanded[$key] = $value;
	}
	
	return $expanded;
}
