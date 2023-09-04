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
		$this->settings = Options::expand($settings);
	}
	
	function exists($module) {
		$_ = $this->settings;
		return !!getTemplate($_->base, expandModule($_->base, $this->stack, $module));
	}

	function include($module, $props = []) {
		$_ = $this->settings;
		
		if (count($this->stack) >= $_->maxIncludeDepth) {
			throw new \Exception('maxIncludeDepth exceeded ' . $_->maxIncludeDepth);
		}
		
		$module = expandModule($_->base, $this->stack, $module);
		$templatePath = getTemplate($_->base, $module);
		
		if (!$templatePath) {
			throw new \Exception("No template found for module '$module'");
		}
		
		array_push($this->stack, compact('module', 'templatePath'));
		
		includeTemplate($templatePath, $this, $props);
		
		array_pop($this->stack);
	}
	
	function file($filePath, $options = []) {
		return Util::fileUrl(
			$this->settings,
			end($stack)->templatePath,
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
		// to-do
		echo '<!-- scripts -->';
	}

	function styles($collationNames = [], $options = []) {
		// to-do
		echo '<!-- styles -->';
	}
}