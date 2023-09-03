<?php
namespace ThomasPeri\Mojl;

class Util {
	static function isAbsolutePath(string $path) {
		$windows = '#^[a-zA-Z]:\\\\#';
		return (
			$path[0] === '/' || 
			(preg_match($windows, $path) && preg_match($windows, __DIR__))
		);
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
		$ext = '.tpl.php';
		$modulePath = $base . '/' . $module;
		$templatePath = $modulePath . '/' . basename($module) . $ext;
		if (!file_exists($templatePath)) {
			$templatePath = $modulePath . $ext;
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
	
	static function writeFileRecursive($file, $data) {
		$dir = dirname($file);
		if (!is_dir($dir)) {
			mkdir($dir, 0777, true);
		}
		file_put_contents($file, $data);
	}
	
	static function fileUrl($settings, $currentTemplate, $currentDocument, $filePath, $options) {
		// RFC 2396
		// scheme        = alpha *( alpha | digit | "+" | "-" | "." )
		// net_path      = "//" authority [ abs_path ]
		if (preg_match('#^([a-z][a-z0-9.+-]*:)?\\/\\/#i', $filePath)) {
			return $filePath;
		}

		$isAbsolute = $filePath[0] === '/';
	
		// A relative path gets absolutized relative to the current included template file.
		$absolutePath = $isAbsolute ?
			$filePath :
			self::pathResolve(dirname($currentTemplate) . '/' . $filePath);
	
		$absoluteUrl = '/' . $settings['buildAssetsDir'] .
			'/' . self::pathRelative($settings['base'], $absolutePath);

		$useHash = ( $options && array_key_exists('hash', $options) ) ?
			$options['hash'] : true;
		
		if ($useHash) {
			$absoluteUrl .= $settings['_cache']->stampAbs($absolutePath);
		}

		return $settings['pageRelativeUrls'] ?
			self::pathRelative(dirname($currentDocument), $absoluteUrl) :
			$absoluteUrl;
	}

}
