<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use ThomasPeri\Mojl\HashCache as HashCache;
use ThomasPeri\Mojl\TemplateHelper as TemplateHelper;
use PHPUnit\Framework\TestCase as TestCase;

final class TemplateHelperIncludeTest extends TestCase {

	static private $defaults = [
		// 'buildDevDir' => 'dev',
		// 'buildDistDir' => 'dist',
		// 'pageRelativeUrls' => false,
		// 'isDev' => false,
		// 'collatePages' => false,
		// 'buildAssetsDir' => 'assets',
		'trimIncludes' => true,
		'maxIncludeDepth' => 100,
		'cacheFile' => 'mojl-cache.json',
		'cacheSave' => false,
		'cacheTTL' => 1 * 24 * 60 * 60 * 1000,
	];
	
	function test_includeTemplate() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings);
			$actual = $builder->include('src/foo', ['a' => 1]);
			$expected = 'foo(1)';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_includeTemplateFromTemplate() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings);
			$actual = $builder->include('src/bar', ['a' => 2]);
			$expected = 'bar(foo(2))';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_includeTemplateRelative() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings);
			$actual = $builder->include('src/zote', ['a' => 3]);
			$expected = 'zote(foo(3))';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_includeTemplateRelativeFromStandalone() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings);
			$actual = $builder->include('src/sbor', ['a' => 4]);
			$expected = 'sbor(foo(4))';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_succeedExactlyAtRecursionLimit() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'maxIncludeDepth' => 5,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings);
			$actual = $builder->include('src/thed', ['a' => 1]);
			$expected = 'thed 1 ( thed 2 ( thed 3 ( thed 4 ( thed 5 ( end ) ) ) ) )';
			$this->assertEquals($expected, $actual);
		});
	}

	function test_failExceedingRecursionLimit() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'maxIncludeDepth' => 4,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings);
			$actual = null;
			try {
				$actual = $builder->include('src/thed', ['a' => 1]);
			} catch (\Exception $e) {
				$actual = $e;
			}
			$expected = 'maxIncludeDepth exceeded (4)';
			$this->assertTrue($actual instanceof \Exception);
			$this->assertEquals($expected, $actual->getMessage());
		});
	}

	function test_trimWhitespace() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings);
			$actual = $builder->include('src-trim/foo');
			$expected = 'outer(inner)';
			$this->assertEquals($expected, $actual);
		});
	}

	function test_dontTrimWhitespace() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'trimIncludes' => false,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings);
			$actual = $builder->include('src-trim/foo');
			$expected = ' outer( inner ) ';
			$this->assertEquals($expected, $actual);
		});
	}

}
