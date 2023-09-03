<?php
namespace ThomasPeri\Mojl;

class Options {

	static $defaults = [
		'base' => '',
		
		// to-do:	'buildDevDir' => 'dev',
		// to-do:	'buildDistDir' => 'dist',
		'buildAssetsDir' => 'assets',

		'cacheFile' => 'mojl-cache.json',
		'cacheSave' => false,
		'cacheTTL' => 1 * 24 * 60 * 60 * 1000, // 1 day in milliseconds

		'maxIncludeDepth' => 100,
		'pageRelativeUrls' => false,
		'isDev' => false,
	];
	
	static function expand($options = []) {
		// Populate missing options and ensure data types on top-level options are correct.
		$expanded = [];
		foreach (self::$defaults as $key => $default) {
			$value = array_key_exists($key, $options) ? $options[$key] : $default;
			$actualType = gettype($value);
			$expectedType = gettype($default);
			if ($expectedType !== $actualType) {
				throw new \Exception(
					"Expected the '$key' option to be $expectedType but got $actualType instead."
				);
			}
			$expanded[$key] = $value;
		}
		
		if (!is_dir($expanded['base']) || !Util::isAbsolutePath($expanded['base'])) {
			throw new \Exception(
				"The 'base' option must be an absolute path to a directory that exists."
			);
		}
		
		$expanded['_cache'] = new HashCache($expanded);

		return $expanded;
	}
	
}
