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

	static function pathResolve($path, $sep = DIRECTORY_SEPARATOR) { // resolveAbsolutePath
		$orig_nodes = explode($sep, $path);
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

	static function pathRelative($from, $to, $sep = DIRECTORY_SEPARATOR) { // getRelativePathInside
		$from = explode($sep, self::pathResolve($from, $sep));
		$to = explode($sep, self::pathResolve($to, $sep));
		
		// To emulate node.js, we need to strip the trailing separator from $to and $from:
		// $ node
		// Welcome to Node.js v14.21.3.
		// Type ".help" for more information.
		// > const path = require('path');
		// undefined
		// > path.relative('/foo/bar/zote/', '/foo/bar/thed/');
		// '../thed'
		// > path.relative('/foo/bar/zote/', '/foo/bar/thed');
		// '../thed'
		// > path.relative('/foo/bar/zote', '/foo/bar/thed');
		// '../thed'
		// > path.relative('/foo/bar/zote', '/foo/bar/thed/');
		// '../thed'
		// > 
		if (end($from) === '') {
			array_pop($from);
		}
		if (end($to) === '') {
			array_pop($to);
		}
		
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
		$isRelative = '@^\.{1,2}(/|$)@';
		$module = trim($module, '/');
		if (preg_match($isRelative, $module) === 1) {
			if (count($stack) === 0) {
				throw new \Exception('Relative module paths can only be used from inside templates.');
			}
			$base = resolveAbsolutePath($base);
			$moduleDir = $base . '/' . $stack[0]['module'];
			$moduleParent = dirname($moduleDir);
			$absoluteModule = resolveAbsolutePath($moduleParent . '/' . $module);
			$module = getRelativePathInside($base, $absoluteModule);
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
