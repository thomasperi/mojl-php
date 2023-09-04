<?php
namespace ThomasPeri\Mojl;

// to-do: port tests for TemplateHelper specifically 

class TemplateHelper {
	private $settings;
	private $urlDocument;
	private $stack = [];
	
	function __construct($settings = [], $urlDocument = '/index.html') {
		if (substr($urlDocument, 0, 1) !== '/') {
			throw new \Exception('TemplateHelper: `urlDocument` must begin with a slash');
		}
		$this->urlDocument = $urlDocument;
		$this->settings = $settings;
	}
	
	function exists($module) {
		$settings = $this->settings;
		return !!Util::getTemplate(
			$settings->base,
			Util::expandModule($settings->base, $this->stack, $module)
		);
	}

	function include($module, $props = null) {
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
		$result = Util::includeTemplate($templatePath, $this, $props);
		array_pop($stack);

		if ($settings->trimIncludes) {
			$result = trim($result);
		}
		return $result;
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
	
	function scripts($collationNames = [], $options = []) {
		return Util::scriptTag($settings, $urlDocument, $collationNames, $options);
	}

	function styles($collationNames = [], $options = []) {
		return Util::styleTag($settings, $urlDocument, $collationNames, $options);
	}
}