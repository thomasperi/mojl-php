<?php
namespace ThomasPeri\Mojl;

class TemplateCache {
	private $fns = [];
	function require($tplFilePath) {
		if (!array_key_exists($tplFilePath, $this->fns)) {
			$fn = require($tplFilePath);
			if (!is_callable($fn)) {
				throw new \Exception("Template $tplFilePath does not return a function");
			}
			$this->fns[$tplFilePath] = $fn;
		}
		return $this->fns[$tplFilePath];
	}
}