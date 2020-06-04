<?php
namespace ThomasPeri\Mojl;

/*!
 * Mojl v1.0.0
 * A PHP library for including content modules built with the npm mojl library.
 * (c) Thomas Peri <hello@thomasperi.net>
 * MIT License
 */
class Mojl {
	// An instance to use via static calls.
	private static $singleton = null;
	
	private $conf = [
		// The path to the directory in which the modules can be found.
		'modules_dir' => '',
		
		// Output debugging info? (Module comments, extra error info, etc.)
		'debug' => false,
		
		// An array of module names to suppress debugging output on.
		'suppress_debug' => ['html'],

		// A place to override $_SERVER['DOCUMENT_ROOT'] for testing and debugging.
		'doc_root' => '',
	];

	// Set configuration options.
	static function config($options = null) {
		$mojl = self::destatic();
		
		// Assign default doc_root to $_SERVER['DOCUMENT_ROOT'] if it exits.
		if (!$mojl->conf['doc_root'] && isset($_SERVER['DOCUMENT_ROOT'])) {
			$mojl->conf['doc_root'] = $_SERVER['DOCUMENT_ROOT'];
		}

		// Assign all the supplied options that really are available options.
		if ($options && is_array($options)) {
			foreach ($mojl->conf as $k => $v) {
				if (isset($options[$k])) {
					$mojl->conf[$k] = $options[$k];
				}
			}
			
			// Set real paths for the config items that are paths.
			$mojl->conf['modules_dir'] = realpath($mojl->conf['modules_dir']);
			$mojl->conf['doc_root'] = realpath($mojl->conf['doc_root']);

		} else {
			// If no options were passed, return a copy of the conf object.
			return $mojl->conf; // copy
		}
	}
	
	// Output the named module and pass it an associative array of $input.
	static function include($module_name, $input = []) {
		$mojl = self::destatic();
		
		// No comment wrap by default
		$comment_begin = '';
		$comment_end = '';
		
		// If we're debugging, wrap the module in a comment
		// for easy search and balance, except where suppressed.
		if (
			$mojl->conf['debug'] &&
			!in_array($module_name, $mojl->conf['suppress_debug'])
		) {
			$comment_begin = "\n<!-- begin $module_name { -->\n";
			$comment_end = "\n<!-- end $module_name } -->\n";
		}
		
		// Output the possibly-wrapped module.
		echo $comment_begin;
		if (($file = $mojl->module_filename($module_name))) {
			MojlIncluder::include($mojl, $input, $file);
			
		} else if (!empty($input)) {
			echo '<pre>';
			var_dump($input);
			echo '</pre>';
		}
		echo $comment_end;
	}
	
	// Output a timestamped site-relative URL based on an absolute file path.
	static function url($file_path, $timestamp = true) {
		$mojl = self::destatic();
		echo $mojl->get_url($file_path, $timestamp);
	}

	// Return a timestamped site-relative URL based on an absolute file path.
	static function get_url($file_path, $timestamp = true) {
		$mojl = self::destatic();
		$docroot = $mojl->conf['doc_root'];
		$length = strlen($docroot);
		if (
			// Ensure that the file exists.
			($real_path = realpath($file_path)) &&

			// Ensure that no files outside the document root can be accessed.
			(substr($real_path, 0, $length + 1) === $docroot . '/')
		) {
			// Return the part of the file path
			// that comes after the document root path.
			return substr($real_path, $length) . 
				($timestamp ? $mojl->stamp($real_path) : '');
				
		} else {
			throw new \Exception("No such file in web root: $file_path");
		}
	}

	// Get an instance from within a static method.
	private static function destatic() {
		// If the call isn't static, return the instance.
		if (isset($this)) {
			return $this;
		}
		
		// If the call is static, return the singleton,
		// creating it if it doesn't exist.
		if (!self::$singleton) {
			self::$singleton = new self();
		}
		return self::$singleton;
	}
	
	// Get the path of the php file for the named module, if it exists,
	// or false if the module doesn't exist.
	private function module_filename($module_name) {
		if (!$this->valid_module_name($module_name)) {
			throw new \Exception(
				"'$module_name' is not a valid module name. " .
				'Module names may only contain letters, numbers, ' .
				'underscores, and dashes'
			);
		}
		
		// modules_dir has already been passed through realpath, but realpath
		// also indicates whether the file exists or not, so use it here anyway.
		return realpath(
			$this->conf['modules_dir'] . "/$module_name/$module_name.php"
		);
	}
	
	// Module names may only contain letters, numbers, underscores,
	// and dashes. This requirement ensures that all modules are
	// first-level subdirectories of the modules directory.
	private function valid_module_name($module_name) {
		return !!preg_match('/^[\\w\\-]+$/', $module_name);
	}
	
	// Generate a timestamp URL query for the asset at the specified file path.
	private static function stamp($file_path) {
		return '?t=' . ((int) @filemtime($file_path));
	}
}

// An auxiliary class for sequestering 
class MojlIncluder {
	static function include($mojl, $input/*, $file */) {
		include func_get_args()[2]; // $file
	}
}
