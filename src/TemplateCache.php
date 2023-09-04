<?php
namespace ThomasPeri\Mojl;

class TemplateCache {
	private $fns = [];
	function require($tplFilePath) {
		if (!array_key_exists($tplFilePath, $this->fns)) {
			$fn = null;
			$output = null;
			ob_start();
			try {
				$fn = require($tplFilePath);
			} finally {
				$output = ob_get_clean();
			}
			if (trim($output) !== '') {
				throw new \Exception("Template $tplFilePath should not have direct output");
			}
			if (!is_callable($fn)) {
				throw new \Exception("Template $tplFilePath does not return a function");
			}
			$this->fns[$tplFilePath] = $fn;
		}
		return $this->fns[$tplFilePath];
	}
}