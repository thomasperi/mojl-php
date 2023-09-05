<?php
namespace ThomasPeri\Mojl;

class Runtime {
	static function build($base, $module = null, $config = null, $request = null) {
		if ($config === null) {
			$config = 'mojl-config-export.json';
		}
		
		$settings = json_decode(file_get_contents($base . '/' . $config));
		$settings->base = $base;
		
		if ($request === null) {
			$request = $_SERVER['REQUEST_URI'];
		}
		
		$path = parse_url($request)['path'];

		if ($module === null) {
			$suffix = $settings->templateOutputSuffix;
			if (str_ends_with($path, $suffix)) {
				$path = substr($path, 0, -strlen($suffix));
			}
			$module = $settings->templateHomeModule . $path;
		}
		
		$tpl = new TemplateHelper($settings, $path);
		$tpl->include($module, null, true);
	}
}
