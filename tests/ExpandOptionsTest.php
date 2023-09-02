<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Options as Options;
use PHPUnit\Framework\TestCase as TestCase;

final class ExpandOptionsTest extends TestCase {

	function test_expandOptions_undefined() {
		$error = false;
		try {
			Options::expand();
		} catch (\Exception $e) {
			$error = true;
		}
		$this->assertTrue($error);
	}

	function test_expandOptions_nonexistent() {
		$error = false;
		try {
			Options::expand([
				'base' => __DIR__ . '/ExpandOptionsTest/zote',
			]);
		} catch (\Exception $e) {
			$error = true;
		}
		$this->assertTrue($error);
	}

	function test_expandOptions_nondirectory() {
		$error = false;
		try {
			Options::expand([
				'base' => __DIR__ . '/ExpandOptionsTest/bar.txt',
			]);
		} catch (\Exception $e) {
			$error = true;
		}
		$this->assertTrue($error);
	}

	function test_expandOptions_relative() {
		$error = false;
		$orig_cwd = getcwd();
		chdir(__DIR__);
		try {
			Options::expand([
				'base' => './ExpandOptionsTest/foo',
			]);
		} catch (\Exception $e) {
			$error = true;
		} finally {
			chdir($orig_cwd);
		}
		$this->assertTrue($error);
	}

	function test_expandOptions_base() {
		$actual = Options::expand([
			'base' => __DIR__ . '/ExpandOptionsTest/foo',
		]);
		$expected = array_merge(Options::$defaults, [
			'base' => __DIR__ . '/ExpandOptionsTest/foo',
		]);
		$this->assertEquals($expected, $actual);
	}
	
	function test_expandOptions_maxIncludeDepth() {
		$actual = Options::expand([
			'base' => __DIR__ . '/ExpandOptionsTest/foo',
			'maxIncludeDepth' => 5,
		]);
		$expected = array_merge(Options::$defaults, [
			'base' => __DIR__ . '/ExpandOptionsTest/foo',
			'maxIncludeDepth' => 5,
		]);
		$this->assertEquals($expected, $actual);
	}

	function test_expandOptions_isDev() {
		$actual = Options::expand([
			'base' => __DIR__ . '/ExpandOptionsTest/foo',
			'isDev' => true,
		]);
		$expected = array_merge(Options::$defaults, [
			'base' => __DIR__ . '/ExpandOptionsTest/foo',
			'isDev' => true,
		]);
		$this->assertEquals($expected, $actual);
	}
	
	function test_expandOptions_ignore_unknown() {
		$actual = Options::expand([
			'base' => __DIR__ . '/ExpandOptionsTest/foo',
			'foo' => 'bar',
		]);
		$expected = array_merge(Options::$defaults, [
			'base' => __DIR__ . '/ExpandOptionsTest/foo',
		]);
		$this->assertEquals($expected, $actual);
	}
	
}
