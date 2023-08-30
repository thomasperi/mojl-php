<?php
namespace ThomasPeri\Mojl;

class Util {
	static function expandOptions($options) {
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
				throw new \Exception(
					"expected '$key' option to be $expectedType but got $actualType instead"
				);
			}
		
			$expanded[$key] = $value;
		}
	
		return $expanded;
	}
	
	static function unixSlashes($path) {
		return str_replace('\\', '/', $path);
	}

	static function pathResolve($path) {
		$sep = '/';
		$path = self::unixSlashes($path);
		$orig_nodes = explode($sep, $path);
		
		// Strip trailing slashes (empty nodes)
		while (end($orig_nodes) === '') {
			array_pop($orig_nodes);
		}
		
		$normalized_nodes = [];
		$last = count($orig_nodes) - 1;
		foreach ($orig_nodes as $i => $node) {
			if ($node === '..') {
				array_pop($normalized_nodes);
			} else if ($node !== '.' && ($node !== '' || $i === 0 || $i === $last)) {
				array_push($normalized_nodes, $node);
			}
		}
		return implode($sep, $normalized_nodes);
	}

	static function pathRelative($from, $to) {
		$sep = '/';
		$from = explode($sep, self::pathResolve($from));
		$to = explode($sep, self::pathResolve($to));
		
		$min_len = min(count($from), count($to));
		for ($i = 0; $i < $min_len; $i++) {
			if ($from[$i] !== $to[$i]) {
				break;
			}
		}
		$from = array_slice($from, $i);
		$to = array_slice($to, $i);
		
		$steps = count($from);
		for ($i = 0; $i < $steps; $i++) {
			array_unshift($to, '..');
		}
		
		return implode($sep, $to);
	}

	static function expandModule($base, $stack, $module) {
		$base = self::unixSlashes($base);
		$module = self::unixSlashes($module);
		$module = trim($module, '/');
		$isRelative = '@^\.{1,2}(/|$)@';
		if (preg_match($isRelative, $module) === 1) {
			if (count($stack) === 0) {
				throw new \Exception('Relative module paths can only be used from inside templates.');
			}
			$moduleDir = self::pathResolve($base . '/' . end($stack)['module']);
			$moduleParent = dirname($moduleDir);
			$absoluteModule = self::pathResolve($moduleParent . '/' . $module);
			$module = self::pathRelative($base, $absoluteModule);
		}
		return $module;
	}

	static function getTemplate($base, $module) {
		$modulePath = $base . '/' . $module;
		$templatePath = $modulePath . '/' . basename($module) . '.tpl.php';
		if (!file_exists($templatePath)) {
			$templatePath = $modulePath + '.tpl.js';
			if (!file_exists($templatePath)) {
				return false;
			}
		}
		return $templatePath;
	}

	static function includeTemplate($templatePath, $helper, $props) {
		$fn = require($templatePath);
		if (!is_callable($fn)) {
			throw new \Exception("Template $templatePath does not return a function");
		}
		$fn($helper, $props);
	}

}
