<?php

// to-do:
// write tests
// publish composer package

namespace ThomasPeri\Mojl;

/*!
 * Mojl v0.0.1
 * A PHP library for serving content modules built with Nodejs mojl.
 * (c) Thomas Peri <hello@thomasperi.net>
 * MIT License
 */
class Mojl {
	// An instance to use via static calls.
	private static $singleton = null;
	
	private $settings = [
		// The path to the directory in which the modules can be found.
		'modules_dir' => '',
		
		// Output debugging info? (Module comments, extra error info, etc.)
		'debug' => false,
		
		// An array of module names to suppress debugging output on.
		'suppress_debug' => ['html'],

		// Leave unset until config():

		// A place to override $_SERVER['DOCUMENT_ROOT'] for testing and debugging.
		// 'doc_root'
	];

	// Get an instance from within a static method.
	private static function destatic() {
		// Create the singleton if it doesn't exist and the call is static.
		if (!self::$singleton && !isset($this)) {
			self::$singleton = new self();
		}
	
		// De-static
		return isset($this) ? $this : self::$singleton;
	}
	
	// Set configuration options.
	static function config($options = null) {
		$mojl = self::destatic();
		
		// Assign default doc_root to $_SERVER['DOCUMENT_ROOT'] if it exits.
		if (!isset($mojl->settings['doc_root']) && isset($_SERVER['DOCUMENT_ROOT'])) {
			$mojl->settings['doc_root'] = $_SERVER['DOCUMENT_ROOT'];
		}

		// Assign all the supplied options that really are available options.
		if ($options && is_array($options)) {
			foreach ($mojl->settings as $k => $v) {
				if (isset($options[$k])) {
					$mojl->settings[$k] = $options[$k];
				}
			}

		} else {
			// If no options were passed, return a copy of the settings object.
			return $mojl->settings; // copy
		}
	}
	
	// Output the named module and pass it an associative array of $input.
	static function module($module_name, $input = []) {
		$mojl = self::destatic();
		
		// No comment wrap by default
		$comment_begin = '';
		$comment_end = '';
		
		// If we're debugging, wrap the module in a comment
		// for easy search and balance, except where suppressed.
		if (
			$mojl->settings['debug'] &&
			!in_array($module_name, $mojl->settings['suppress_debug'])
		) {
			$comment_begin = "\n<!-- begin $module_name { -->\n";
			$comment_end = "\n<!-- end $module_name } -->\n";
		}
		
		// Output the possibly-wrapped module.
		echo $comment_begin;
		if (($file = $mojl->module_filename($module_name))) {
			Includer::include($input, $file);
		} else if (!empty($input)) {
			echo '<pre>';
			var_dump($input);
			echo '</pre>';
		}
		echo $comment_end;
	}
	
	// Get the path of the php file for the named module, if it exists
	private function module_filename($module_name) {
		$modroot = realpath($this->settings['modules_dir']);
		$modpath = realpath("$modroot/$module_name/$module_name.php");
		// Ensure that the supplied module path is a real file
		// that exists inside the modules directory.
		if (
			is_file($modpath) &&
			substr($modpath, 0, strlen($modroot)) === $modroot
		) {
			return $modpath;
		}
	}
	
	// Convert an absolute file path to a timestamped site-relative URL.
	static function url($file_path, $timestamp = true) {
		$mojl = self::destatic();
		
		$docroot = realpath($mojl->settings['doc_root']);
		$length = strlen($docroot);
		if (($real_path = @realpath($file_path))) {
			if (substr($real_path, 0, $length + 1) === $docroot . '/') {
				return home_url() . substr($real_path, $length) . 
					($timestamp ? $mojl->stamp($real_path) : '');
			}
		} else {
			throw new Exception("No such file: $file_path");
		}
	}

	// Generate a timestamp URL query for the asset at the specified file path.
	private static function stamp($file_path) {
		return '?t=' . ((int) @filemtime($file_path));
	}
}

class Includer {
	static function include($input/*, $file */) {
		include func_get_args()[1]; // $file
	}
}
