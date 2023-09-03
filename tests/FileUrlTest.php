<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use ThomasPeri\Mojl\Options as Options;
use ThomasPeri\Mojl\HashCache as HashCache;
use PHPUnit\Framework\TestCase as TestCase;

final class FileUrlTest extends TestCase {

	static private $defaults = [
		'pageRelativeUrls' => false,
		'buildAssetsDir' => 'assets',

		'cacheFile' => 'mojl-cache.json',
		'cacheSave' => false,
		'cacheTTL' => 1 * 24 * 60 * 60 * 1000,
	];

	function test_absoluteWithHashFromTemplateRelative() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, ['base' => $base]);
			$settings->_cache = new HashCache($settings);
			$currentTemplate = $base . '/' . 'src/home/foo/foo.tpl.js';
			$currentPage = '/foo/index.html';
			$filePath = 'images/icon.gif';
			$options = null;
			$actual = Util::fileUrl($settings, $currentTemplate, $currentPage, $filePath, $options);
			$expected = '/assets/src/home/foo/images/icon.gif?h=wyCFiYxuNtNh1LgBcIfekOG4Rlw~';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_absoluteWithHashFromAbsFilesystemPath() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, ['base' => $base]);
			$settings->_cache = new HashCache($settings);
			$currentTemplate = $base . '/' . 'src/home/foo/foo.tpl.js';
			$currentPage = '/foo/index.html';
			$filePath = $base . '/src/home/foo/images/icon.gif';
			$options = null;
			$actual = Util::fileUrl($settings, $currentTemplate, $currentPage, $filePath, $options);
			$expected = '/assets/src/home/foo/images/icon.gif?h=wyCFiYxuNtNh1LgBcIfekOG4Rlw~';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_pageRelativeWithPageRelativeUrlsOption() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, ['base' => $base, 'pageRelativeUrls' => true]);
			$settings->_cache = new HashCache($settings);
			$currentTemplate = $base . '/' . 'src/home/foo/foo.tpl.js';
			$currentPage = '/foo/index.html';
			$filePath = 'images/icon.gif';
			$options = null;
			$actual = Util::fileUrl($settings, $currentTemplate, $currentPage, $filePath, $options);
			$expected = '../assets/src/home/foo/images/icon.gif?h=wyCFiYxuNtNh1LgBcIfekOG4Rlw~';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_omitHashWhenHashIsFalse() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, ['base' => $base, 'pageRelativeUrls' => true]);
			$settings->_cache = new HashCache($settings);
			$currentTemplate = $base . '/' . 'src/home/foo/foo.tpl.js';
			$currentPage = '/foo/index.html';
			$filePath = 'images/icon.gif';
			$options = ['hash' => false];
			$actual = Util::fileUrl($settings, $currentTemplate, $currentPage, $filePath, $options);
			$expected = '../assets/src/home/foo/images/icon.gif';
			$this->assertEquals($actual, $expected);
		});
	}

}
