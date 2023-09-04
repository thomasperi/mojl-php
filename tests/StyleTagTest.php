<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use ThomasPeri\Mojl\HashCache as HashCache;
use PHPUnit\Framework\TestCase as TestCase;

final class StyleTagTest extends TestCase {

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

	function test_generateLinkTag() {
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
			
			$actual = Util::styleTag($settings, $currentPage, $collations, $options);
			$fooHash = self::$fooHash;
			$expected = "<link rel=\"stylesheet\" href=\"/site.css$fooHash\" />";
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
			
			$actual = Util::styleTag($settings, $currentPage, $collations, $options);
			$expected = '<link rel="stylesheet" href="/site.css" />';
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
			
			$actual = Util::styleTag($settings, $currentPage, $collations, $options);
			$expected = '<link rel="stylesheet" href="/one.css" /><link rel="stylesheet" href="/two.css" />';
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
			
			$actual = Util::styleTag($settings, $currentPage, $collations, $options);
			$expected = '<link rel="stylesheet" href="/aaa.css" /><link rel="stylesheet" href="/bbb.css" />';
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
			
			$actual = Util::styleTag($settings, $currentPage, $collations, $options);
			$expected = '<link rel="stylesheet" href="../site.css" />';
			$this->assertEquals($actual, $expected);
		});
	}

}
