<?php
namespace ThomasPeri\Mojl;

class TemplateHelper {
	private $settings;
	private $stack = [];
	
	function __construct($settings = [], $urlDocument = '/index.html') {
		if (substr($urlDocument, 0, 1) !== '/') {
			throw new \Exception('TemplateHelper: `urlDocument` must begin with a slash');
		}
		$this->settings = Options::expand($settings);
	}
	
	function exists($module) {
		$_ = $this->settings;
		return !!getTemplate($_['base'], expandModule($_['base'], $this->stack, $module));
	}

	function include($module, $props = []) {
		$_ = $this->settings;
		
		if (count($this->stack) >= $_['maxIncludeDepth']) {
			throw new \Exception('maxIncludeDepth exceeded ' . $_['maxIncludeDepth']);
		}
		
		$module = expandModule($_['base'], $this->stack, $module);
		$templatePath = getTemplate($_['base'], $module);
		
		if (!$templatePath) {
			throw new \Exception("No template found for module '$module'");
		}
		
		array_push($this->stack, compact('module', 'templatePath'));
		
		includeTemplate($templatePath, $this, $props);
		
		array_pop($this->stack);
	}
	
	function file($filePath, $options = []) {
		// to-do
	}
	
	function link($linkPath) {
		// to-do
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