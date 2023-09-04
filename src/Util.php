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
			$moduleDir = self::pathResolve($base . '/' . end($stack)->module);
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

	static function includeTemplate($templatePath, $props, $helper, $tplCache) {
		$fn = $tplCache->require($templatePath);
		
		$returned = null;
		$echoed = null;
		ob_start();
		try {
			$returned = $fn($helper, $props);
		} finally {
			$echoed = ob_get_clean();
		}
		
		if ($returned === null) {
			return $echoed;
		}
		if (trim($echoed) === '') {
			return $returned;
		}
		
	// echo "\n";
	// var_dump($templatePath);
	// var_dump($returned);
	// var_dump($echoed);
		
		throw new \Exception(
			"Template $templatePath had non-empty values for both its output and its return value"
		);
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
		
		$settings = (object) $settings;
		$options = (object) $options;

		$isAbsolute = $filePath[0] === '/';
	
		// A relative path gets absolutized relative to the current included template file.
		$absolutePath = $isAbsolute ?
			$filePath :
			self::pathResolve(dirname($currentTemplate) . '/' . $filePath);
	
		$absoluteUrl = '/' . $settings->buildAssetsDir .
			'/' . self::pathRelative($settings->base, $absolutePath);

		$useHash = ( $options && property_exists($options, 'hash') ) ?
			$options->hash : true;
		
		if ($useHash) {
			$absoluteUrl .= $settings->_cache->stampAbs($absolutePath);
		}

		return $settings->pageRelativeUrls ?
			self::pathRelative(dirname($currentDocument), $absoluteUrl) :
			$absoluteUrl;
	}

	static function linkUrl($settings, $currentPage, $url) {
		// RFC 2396
		// scheme        = alpha *( alpha | digit | "+" | "-" | "." )
		// net_path      = "//" authority [ abs_path ]
		if (preg_match('#^([a-z][a-z0-9.+-]*:)?\\/\\/#i', $url)) {
			return $url;
		}
	
		$settings = (object) $settings;

		$isRelative = $url[0] !== '/';
		$currentDir = dirname($currentPage);
		$tailSlash = ($url[strlen($url) - 1] === '/') ? '/' : '';
	
		if ($settings->pageRelativeUrls) {
			if ($isRelative) {
				return $url;
			} else {
				return self::pathRelative($currentDir, $url) . $tailSlash;
			}
		} else {
			if ($isRelative) {
				return self::pathResolve($currentDir . '/' . $url) . $tailSlash;
			} else {
				return $url;
			}
		}
	}
	
	static function encodeHtmlAttribute($value) {
		return str_replace(
			['&', "'", '"', '<', '>'],
			['&amp;', '&apos;', '&quot;', '&lt;', '&gt;'],
			$value
		);
	}

	static function assetTagAttr($settings, $currentPage, $type, $collationNames, $options) {
		$settings = (object) $settings;
		$options = (object) $options;
		
		// Use all collations if none specified
		if ($collationNames === null) {
			$collationNames = array_map(
				fn ($coll) => $coll->name,
				array_filter(
					$settings->collations,
					fn ($coll) => !property_exists($coll, 'page')
				)
			);
			if ($settings->collatePages) {
				$collationNames[] = ''; // Empty string means current page
			}
		}

		// Wrap in array if not already
		if (!is_array($collationNames)) {
			$collationNames = [$collationNames];
		}

		// Convert empty string to current page
		$collationNames = array_map(
			function ($collName) use ($settings, $currentPage) {
				if ($collName === '') {
					foreach ($settings->collations as $coll) {
						if (property_exists($coll, 'page') && $coll->page === $currentPage) {
							$collName = $coll->name;
							break;
						}
					}
				}
				return $collName;
			},
			$collationNames
		);
		
		// Convert collation names to urls
		$urls = [];
		foreach ($collationNames as $collName) {
			$urls[] = self::assetTagAttrEach(
				$settings, $currentPage, "$collName.$type", $options
			);
		}

		// Remove the ones that don't exist
		return array_filter(
			$urls,
			fn ($url) => !!$url
		);
	}

	static function assetTagAttrEach($settings, $currentPage, $file, $options) {
		$buildDir = $settings->isDev ? $settings->buildDevDir : $settings->buildDistDir;
		$docroot = $settings->base . '/' . $buildDir;
		$filePath = self::pathResolve($docroot . '/' . $file);

		if (!file_exists($filePath)) {
			return;
		}
	
		$fileUrl = '/' . self::pathRelative($docroot, $filePath);
		if ($settings->pageRelativeUrls) {
			$fileUrl = self::pathRelative(dirname($currentPage), $fileUrl);
		}
	
		$useHash = ( $options && property_exists($options, 'hash') ) ?
			$options->hash : true;

		if ($useHash) {
			$fileUrl .= $settings->_cache->stampAbs($filePath);
		}
	
		return self::encodeHtmlAttribute($fileUrl);
	}

	static function scriptTag($settings, $currentPage, $collationNames, $options) {
		$srcs = self::assetTagAttr($settings, $currentPage, 'js', $collationNames, $options);
		return implode('', array_map(
			fn ($src) => "<script src=\"$src\"></script>",
			$srcs
		));
	}

	static function styleTag($settings, $currentPage, $collationNames, $options) {
		$hrefs = self::assetTagAttr($settings, $currentPage, 'css', $collationNames, $options);
		return implode('', array_map(
			fn ($href) => "<link rel=\"stylesheet\" href=\"$href\" />",
			$hrefs
		));
	}

}
