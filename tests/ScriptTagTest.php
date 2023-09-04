<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use ThomasPeri\Mojl\HashCache as HashCache;
use PHPUnit\Framework\TestCase as TestCase;

final class ScriptTagTest extends TestCase {

	static private $defaults = [
// 		'buildDevDir' => 'dev',
		'buildDistDir' => 'dist',
		'pageRelativeUrls' => false,
		'isDev' => false,
		'collatePages' => false,
// 		// 'buildAssetsDir' => 'assets',
		'cacheFile' => 'mojl-cache.json',
		'cacheSave' => false,
		'cacheTTL' => 1 * 24 * 60 * 60 * 1000,
	];
	
	static $fooHash = '?h=C*7Hteo!D9vJXQ3UfzxbwnXaijM~';
	static $barHash = '?h=Ys23Ag!5IOWqZCw9QGaVDdHwH00~';

	function test_generateScriptTag() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'collations' => [
					(object) [ 'name' => 'site' ]
				],
			]);
			$settings->_cache = new HashCache($settings);
			
			$currentPage = '/index.html';
			$collations = null;
			$options = null;
			
			$actual = Util::scriptTag($settings, $currentPage, $collations, $options);
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
					(object) [ 'name' => 'site' ]
				],
			]);
			$settings->_cache = new HashCache($settings);
			
			$currentPage = '/index.html';
			$collations = null;
			$options = [ 'hash' => false ];
			
			$actual = Util::scriptTag($settings, $currentPage, $collations, $options);
			$expected = '<script src="/site.js"></script>';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_settingsCollations() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'collations' => [
					(object) [ 'name' => 'one' ],
					(object) [ 'name' => 'two' ],
				],
			]);
			$settings->_cache = new HashCache($settings);
			
			$currentPage = '/index.html';
			$collations = null;
			$options = [ 'hash' => false ];
			
			$actual = Util::scriptTag($settings, $currentPage, $collations, $options);
			$expected = '<script src="/one.js"></script><script src="/two.js"></script>';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_explicitCollations() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'collations' => [
					(object) [ 'name' => 'site' ]
				],
			]);
			$settings->_cache = new HashCache($settings);
			
			$currentPage = '/index.html';
			$collations = ['aaa', 'bbb'];
			$options = [ 'hash' => false ];
			
			$actual = Util::scriptTag($settings, $currentPage, $collations, $options);
			$expected = '<script src="/aaa.js"></script><script src="/bbb.js"></script>';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_relativize() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'collations' => [
					(object) [ 'name' => 'site' ]
				],
				'pageRelativeUrls' => true,
			]);
			$settings->_cache = new HashCache($settings);
			
			$currentPage = '/foo/index.html';
			$collations = null;
			$options = [ 'hash' => false ];
			
			$actual = Util::scriptTag($settings, $currentPage, $collations, $options);
			$expected = '<script src="../site.js"></script>';
			$this->assertEquals($actual, $expected);
		});
	}

}
