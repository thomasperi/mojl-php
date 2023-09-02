<?php
namespace ThomasPeri\Mojl\Test;

class _Clone {
	static function run($filename, $fn) {
		$source = preg_replace('#\\.php$#', '', $filename);

		$tempUniq = sys_get_temp_dir() . '/clone-' . uniqid();
		$tempBase = $tempUniq . '/' . basename($source);

		mkdir($tempUniq);
		self::clone($source, $tempBase);

		try {
			$fn($tempBase);
		} finally {
			self::destroy($tempUniq);
		}
	}
	
	static function clone($source, $temp) {
		mkdir($temp);
		foreach (scandir($source) as $item) {
			if ($item === '.' || $item === '..') {
				continue;
			}
			$source_item = $source . '/' . $item;
			$temp_item = $temp . '/' . $item;
			if (is_dir($source_item)) {
				self::clone($source_item, $temp_item);
			} else if (is_file($source_item)) {
				copy($source_item, $temp_item);
			}
		}
	}
	
	static function destroy($temp) {
		foreach (scandir($temp) as $item) {
			if ($item === '.' || $item === '..') {
				continue;
			}
			$temp_item = $temp . '/' . $item;
			if (is_dir($temp_item)) {
				self::destroy($temp_item);
			} else if (is_file($temp_item)) {
				unlink($temp_item);
			}
		}
		rmdir($temp);
	}
	
}
