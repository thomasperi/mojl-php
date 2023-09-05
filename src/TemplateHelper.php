<?php
namespace ThomasPeri\Mojl;

class TemplateHelper {
	private $settings;
	private $tplCache;
	private $urlDocument;
	private $stack = [];
	
	function __construct($settings, $urlDocument = '/index.html') {
		if (substr($urlDocument, 0, 1) !== '/') {
			throw new \Exception('TemplateHelper: `urlDocument` must begin with a slash');
		}
		$this->urlDocument = $urlDocument;
		$this->settings = $settings;
		$this->settings->_cache = new HashCache($this->settings);
		$this->tplCache = new TemplateCache();
	}
	
	function exists($module) {
		$settings = $this->settings;
		return !!Util::getTemplate(
			$settings->base,
			Util::expandModule($settings->base, $this->stack, $module)
		);
	}

	function include($module, $props = null, $test = false) {
		$settings = $this->settings;
		$stack = &$this->stack;
		
		if (count($stack) >= $settings->maxIncludeDepth) {
			throw new \Exception("maxIncludeDepth exceeded ({$settings->maxIncludeDepth})");
		}
		
		$module = Util::expandModule($settings->base, $stack, $module);
		$templatePath = Util::getTemplate($settings->base, $module);
		
		if (!$templatePath) {
			throw new \Exception("No template found for module '$module'");
		}

		array_push($stack, (object) compact('module', 'templatePath'));
		$result = Util::includeTemplate($templatePath, $props, $this, $this->tplCache, $test);
		array_pop($stack);
		
		if ($settings->trimIncludes) {
			$result = trim($result);
		}
		
		echo $result;
	}
	
	function file($filePath, $options = []) {
		return Util::fileUrl(
			$this->settings,
			end($this->stack)->templatePath,
			$this->urlDocument,
			$filePath,
			$options
		);
	}
	
	function link($linkPath) {
		return Util::linkUrl(
			$this->settings,
			$this->urlDocument,
			$linkPath
		);
	}
	
	function scripts($collationNames = null, $options = null) {
		return Util::scriptTag($this->settings, $this->urlDocument, $collationNames, $options);
	}

	function styles($collationNames = null, $options = null) {
		return Util::styleTag($this->settings, $this->urlDocument, $collationNames, $options);
	}
}