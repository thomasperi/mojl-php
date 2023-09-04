<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use ThomasPeri\Mojl\HashCache as HashCache;
use ThomasPeri\Mojl\TemplateHelper as TemplateHelper;
use PHPUnit\Framework\TestCase as TestCase;

final class TemplateHelperScriptsTest extends TestCase {

	static private $defaults = [
		// 'buildDevDir' => 'dev',
		'buildDistDir' => 'dist',
		'pageRelativeUrls' => false,
		'isDev' => false,
		'collatePages' => false,
		'buildAssetsDir' => 'assets',
		'maxIncludeDepth' => 100,
		'cacheFile' => 'mojl-cache.json',
		'cacheSave' => false,
		'cacheTTL' => 1 * 24 * 60 * 60 * 1000,
		'trimIncludes' => true,
	];
	
	static $fooHash = '?h=C*7Hteo!D9vJXQ3UfzxbwnXaijM~';
	static $barHash = '?h=Ys23Ag!5IOWqZCw9QGaVDdHwH00~';

	function test_filenameFromSettings() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'collations' => [
					(object)['name' => 'site'],
				],
			]);
			$settings->_cache = new HashCache($settings);

			$currentPage = '/index.html';
			$tpl = new TemplateHelper($settings, $currentPage);
			$actual = $tpl->scripts();
			$fooHash = self::$fooHash;
			$expected = "<script src=\"/site.js$fooHash\"></script>";
			$this->assertEquals($actual, $expected);
		});
	}

	function test_omitHash() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'collations' => [
					(object)['name' => 'site'],
				],
			]);
			$settings->_cache = new HashCache($settings);

			$currentPage = '/index.html';
			$tpl = new TemplateHelper($settings, $currentPage);
			$actual = $tpl->scripts(null, ['hash' => false]);
			$fooHash = self::$fooHash;
			$expected = '<script src="/site.js"></script>';
			$this->assertEquals($actual, $expected);
		});
	}
	
	function test_collationsInSettings() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'collations' => [
					(object)['name' => 'one'],
					(object)['name' => 'two'],
				],
			]);
			$settings->_cache = new HashCache($settings);

			$currentPage = '/index.html';
			$tpl = new TemplateHelper($settings, $currentPage);
			$actual = $tpl->scripts(null, ['hash' => false]);
			$fooHash = self::$fooHash;
			$expected = '<script src="/one.js"></script><script src="/two.js"></script>';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_explicitCollations() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'collations' => [
					(object)['name' => 'site'],
				],
			]);
			$settings->_cache = new HashCache($settings);

			$currentPage = '/index.html';
			$tpl = new TemplateHelper($settings, $currentPage);
			$actual = $tpl->scripts(['aaa', 'bbb'], ['hash' => false]);
			$fooHash = self::$fooHash;
			$expected = '<script src="/aaa.js"></script><script src="/bbb.js"></script>';
			$this->assertEquals($actual, $expected);
		});
	}
	
	function test_relativize() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'pageRelativeUrls' => true,
				'collations' => [
					(object)['name' => 'site'],
				],
			]);
			$settings->_cache = new HashCache($settings);

			$currentPage = '/foo/index.html';
			$tpl = new TemplateHelper($settings, $currentPage);
			$actual = $tpl->scripts(null, ['hash' => false]);
			$fooHash = self::$fooHash;
			$expected = '<script src="../site.js"></script>';
			$this->assertEquals($actual, $expected);
		});
	}
	
}
