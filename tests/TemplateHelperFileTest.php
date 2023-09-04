<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use ThomasPeri\Mojl\HashCache as HashCache;
use ThomasPeri\Mojl\TemplateHelper as TemplateHelper;
use PHPUnit\Framework\TestCase as TestCase;

final class TemplateHelperFileTest extends TestCase {

	static private $defaults = [
		// 'buildDevDir' => 'dev',
		// 'buildDistDir' => 'dist',
		'pageRelativeUrls' => false,
		// 'isDev' => false,
		// 'collatePages' => false,
		'buildAssetsDir' => 'assets',
		'maxIncludeDepth' => 100,
		'cacheFile' => 'mojl-cache.json',
		'cacheSave' => false,
		'cacheTTL' => 1 * 24 * 60 * 60 * 1000,
		'trimIncludes' => true,
	];
	
	function test_relativePath() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings, '/abc/def/index.html');
			$actual = $builder->include('src/foo');
			$expected = 'foo(/assets/src/foo/icon.gif?h=wyCFiYxuNtNh1LgBcIfekOG4Rlw~)';
			$this->assertEquals($expected, $actual);
		});
	}

	function test_relativePathNoHash() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings);
			$actual = $builder->include('src/foo', ['options' => ['hash' => false]]);
			$expected = 'foo(/assets/src/foo/icon.gif)';
			$this->assertEquals($expected, $actual);
		});
	}

	function test_relativePathAsPageRelative() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'pageRelativeUrls' => true,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings, '/abc/def/index.html');
			$actual = $builder->include('src/foo', ['options' => ['hash' => false]]);
			$expected = 'foo(../../assets/src/foo/icon.gif)';
			$this->assertEquals($expected, $actual);
		});
	}

	function test_relativePathOutsideModule() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings);
			$actual = $builder->include('src/bar');
			$expected = 'bar(/assets/src/foo/icon.gif?h=wyCFiYxuNtNh1LgBcIfekOG4Rlw~)';
			$this->assertEquals($expected, $actual);
		});
	}

	function test_relativePathOutsideModulePageRelative() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'pageRelativeUrls' => true,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings, '/abc/def/index.html');
			$actual = $builder->include('src/bar');
			$expected = 'bar(../../assets/src/foo/icon.gif?h=wyCFiYxuNtNh1LgBcIfekOG4Rlw~)';
			$this->assertEquals($expected, $actual);
		});
	}

	function test_absolutePath() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings);
			$actual = $builder->include('src/zote');
			$expected = 'zote(/assets/src/foo/icon.gif?h=wyCFiYxuNtNh1LgBcIfekOG4Rlw~)';
			$this->assertEquals($expected, $actual);
		});
	}

	function test_absolutePathPageRelative() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'pageRelativeUrls' => true,
			]);
			$settings->_cache = new HashCache($settings);

			$builder = new TemplateHelper($settings, '/abc/def/index.html');
			$actual = $builder->include('src/zote');
			$expected = 'zote(../../assets/src/foo/icon.gif?h=wyCFiYxuNtNh1LgBcIfekOG4Rlw~)';
			$this->assertEquals($expected, $actual);
		});
	}

}
