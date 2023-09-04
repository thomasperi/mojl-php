<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use ThomasPeri\Mojl\HashCache as HashCache;
use ThomasPeri\Mojl\TemplateHelper as TemplateHelper;
use PHPUnit\Framework\TestCase as TestCase;

final class TemplateHelperExistsTest extends TestCase {

	static private $defaults = [
		// 'buildDevDir' => 'dev',
		// 'buildDistDir' => 'dist',
		// 'pageRelativeUrls' => false,
		// 'isDev' => false,
		// 'collatePages' => false,
		// 'buildAssetsDir' => 'assets',
		'cacheFile' => 'mojl-cache.json',
		'cacheSave' => false,
		'cacheTTL' => 1 * 24 * 60 * 60 * 1000,
	];
	
	function test_trueExists() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings);
			$this->assertTrue($builder->exists('src/foo'));
		});
	}

	function test_trueExistsStandalone() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings);
			$this->assertTrue($builder->exists('src/bar'));
		});
	}

	function test_falseNotExists() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings);
			$this->assertFalse($builder->exists('src/zote'));
		});
	}
}
