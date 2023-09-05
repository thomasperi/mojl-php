<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use ThomasPeri\Mojl\HashCache as HashCache;
use ThomasPeri\Mojl\TemplateHelper as TemplateHelper;
use PHPUnit\Framework\TestCase as TestCase;

final class TemplateHelperLinkTest extends TestCase {

	static private $defaults = [
		// 'buildDevDir' => 'dev',
		// 'buildDistDir' => 'dist',
		'pageRelativeUrls' => false,
		// 'isDev' => false,
		// 'collatePages' => false,
		// 'buildAssetsDir' => 'assets',
		'maxIncludeDepth' => 100,
		'cacheFile' => 'mojl-cache.json',
		'cacheSave' => false,
		'cacheTTL' => 1 * 24 * 60 * 60 * 1000,
		'trimIncludes' => true,
	];
	
	static function ob($fn) {
		$result = '';
		ob_start();
		try {
			$fn();
		} finally {
			$result = ob_get_clean();
		}
		return $result;
	}

	function test_relativeToAbsolute() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
			]);
			$settings->_cache = new HashCache($settings);

			$tpl = new TemplateHelper($settings, '/abc/def/index.html');
			$actual = self::ob(fn () =>
				$tpl->include('src/foo', ['theLink' => 'ghi'])
			);
			$expected = 'foo(/abc/def/ghi)';
			$this->assertEquals($expected, $actual);
		});
	}

	function test_absoluteToRelative() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'pageRelativeUrls' => true,
			]);
			$settings->_cache = new HashCache($settings);

			$tpl = new TemplateHelper($settings, '/abc/def/index.html');
			$actual = self::ob(fn () =>
				$tpl->include('src/foo', ['theLink' => '/abc/ghi'])
			);
			$expected = 'foo(../ghi)';
			$this->assertEquals($expected, $actual);
		});
	}

}
