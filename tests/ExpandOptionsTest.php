<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use PHPUnit\Framework\TestCase as TestCase;

final class ExpandOptionsTest extends TestCase {

	function test_expandOptions_undefined() {
		$error = false;
		try {
			Util::expandOptions([]);
		} catch (\Exception $e) {
			$error = true;
		}
		$this->assertTrue($error);
	}

	function test_expandOptions_base() {
		$actual = Util::expandOptions([
			'base' => '/foo/bar',
		]);
		$expected = [
			'base' => '/foo/bar',
			'maxIncludeDepth' => 100,
			'pageRelativeUrls' => false,
			'isDev' => false,
		];
		$this->assertEquals($expected, $actual);
	}
	
	function test_expandOptions_maxIncludeDepth() {
		$actual = Util::expandOptions([
			'base' => '/foo/bar',
			'maxIncludeDepth' => 5,
		]);
		$expected = [
			'base' => '/foo/bar',
			'maxIncludeDepth' => 5,
			'pageRelativeUrls' => false,
			'isDev' => false,
		];
		$this->assertEquals($expected, $actual);
	}

	function test_expandOptions_isDev() {
		$actual = Util::expandOptions([
			'base' => '/foo/bar',
			'isDev' => true,
		]);
		$expected = [
			'base' => '/foo/bar',
			'maxIncludeDepth' => 100,
			'pageRelativeUrls' => false,
			'isDev' => true,
		];
		$this->assertEquals($expected, $actual);
	}
	
	function test_expandOptions_ignore_unknown() {
		$actual = Util::expandOptions([
			'base' => '/foo/bar',
			'foo' => 'bar',
		]);
		$expected = [
			'base' => '/foo/bar',
			'maxIncludeDepth' => 100,
			'pageRelativeUrls' => false,
			'isDev' => false,
		];
		$this->assertEquals($expected, $actual);
	}
	
}
