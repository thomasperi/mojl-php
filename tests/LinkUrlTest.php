<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use ThomasPeri\Mojl\Options as Options;
use ThomasPeri\Mojl\HashCache as HashCache;
use PHPUnit\Framework\TestCase as TestCase;

final class LinkUrlTest extends TestCase {

	function test_absoluteFromRelative() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = Options::expand(['base' => $base]);
			$currentPage = '/foo/bar/zote/index.html';
			$url = '../../sbor/';
			$actual = Util::linkUrl($settings, $currentPage, $url);
			$expected = '/foo/sbor/';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_relativeFromRelativeForPageRelativeUrls() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = Options::expand(['base' => $base, 'pageRelativeUrls' => true]);
			$currentPage = '/foo/bar/zote/index.html';
			$url = '../../sbor/';
			$actual = Util::linkUrl($settings, $currentPage, $url);
			$expected = '../../sbor/';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_absoluteFromAbsolute() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = Options::expand(['base' => $base]);
			$currentPage = '/foo/bar/zote/index.html';
			$url = '/sbor/thed/sneg/';
			$actual = Util::linkUrl($settings, $currentPage, $url);
			$expected = '/sbor/thed/sneg/';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_relativeFromAbsoluteForPageRelativeUrls() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = Options::expand(['base' => $base, 'pageRelativeUrls' => true]);
			$currentPage = '/foo/bar/zote/index.html';
			$url = '/foo/sbor/';
			$actual = Util::linkUrl($settings, $currentPage, $url);
			$expected = '../../sbor/';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_schemeRelativeFromSchemeRelative() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = Options::expand(['base' => $base]);
			$currentPage = '/foo/bar/zote/index.html';
			$url = '//example.com/sbor/thed/sneg/';
			$actual = Util::linkUrl($settings, $currentPage, $url);
			$expected = '//example.com/sbor/thed/sneg/';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_schemeRelativeFromSchemeRelativeForPageRelativeUrls() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = Options::expand(['base' => $base, 'pageRelativeUrls' => true]);
			$currentPage = '/foo/bar/zote/index.html';
			$url = '//example.com/sbor/thed/sneg/';
			$actual = Util::linkUrl($settings, $currentPage, $url);
			$expected = '//example.com/sbor/thed/sneg/';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_fullFromFull() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = Options::expand(['base' => $base]);
			$currentPage = '/foo/bar/zote/index.html';
			$url = 'https://example.com/sbor/thed/sneg/';
			$actual = Util::linkUrl($settings, $currentPage, $url);
			$expected = 'https://example.com/sbor/thed/sneg/';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_fullFromFullWithPageRelativeUrls() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = Options::expand(['base' => $base, 'pageRelativeUrls' => true]);
			$currentPage = '/foo/bar/zote/index.html';
			$url = 'https://example.com/sbor/thed/sneg/';
			$actual = Util::linkUrl($settings, $currentPage, $url);
			$expected = 'https://example.com/sbor/thed/sneg/';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_preserveTailSlashAbsence() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = Options::expand(['base' => $base]);
			$currentPage = '/foo/bar/zote/index.html';
			$url = '../../sbor';
			$actual = Util::linkUrl($settings, $currentPage, $url);
			$expected = '/foo/sbor';
			$this->assertEquals($actual, $expected);
		});
	}

	function test_preserveTailSlashAbsenceWithPageRelativeUrls() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = Options::expand(['base' => $base, 'pageRelativeUrls' => true]);
			$currentPage = '/foo/bar/zote/index.html';
			$url = '/foo/sbor';
			$actual = Util::linkUrl($settings, $currentPage, $url);
			$expected = '../../sbor';
			$this->assertEquals($actual, $expected);
		});
	}

}
