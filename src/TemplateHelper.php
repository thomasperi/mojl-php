<?php
namespace Mojl;
require_once __DIR__ . '/expandOptions.php';

class TemplateHelper {
	private $settings;
	private $stack = [];
	
	function __construct($settings = [], $urlDocument = '/index.html') {
		if (substr($urlDocument, 0, 1) !== '/') {
			throw new \Exception('TemplateHelper: `urlDocument` must begin with a slash');
		}
		$this->settings = expandOptions($settings);
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

function expandModule($base, $stack, $module) {
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

function getTemplate($base, $module) {
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

function includeTemplate($templatePath, $helper, $props) {
	$fn = require($templatePath);
	if (!is_callable($fn)) {
		throw new \Exception("Template $templatePath does not return a function");
	}
	$fn($helper, $props);
}

function resolveAbsolutePath($path) {
	$orig_nodes = explode('/', trim($path, '/'));
	$resolved_nodes = [];
	foreach ($orig_nodes as $node) {
		if ($node === '..') {
			array_pop($resolved_nodes);
		}
		if ($node !== '' && $node !== '.') {
			array_push($resolved_nodes, $node);
		}
	}
	return '/' . implode('/', $resolved_nodes);
}

function getRelativePathInside($from, $to) {
	$len = strlen($from);
	if ($from . '/' !== substr($to, 0, $len + 1)) {
		throw new \Exception("$to is outside $from");
	}
	return substr($to, $len);
}
