<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use PHPUnit\Framework\TestCase as TestCase;

final class PathResolveTest extends TestCase {

	function test_pathResolve_defaultSep() {
		// Ensure that the method works with no $sep argument,
		// because the rest of the tests use it,
		// but normal usage of the method does not.
		$sep = DIRECTORY_SEPARATOR;
		$actual = Util::pathResolve("foo{$sep}bar{$sep}..{$sep}thed");
		$expected = "foo{$sep}thed";
		$this->assertEquals($actual, $expected);
	}

	function test_pathResolve_tailSlashOnly() {
		$actual = Util::pathResolve('foo//bar/./zote/../../thed/', '/');
		$expected = 'foo/thed/';
		$this->assertEquals($actual, $expected);
	}

	function test_pathResolve_headSlashOnly() {
		$actual = Util::pathResolve('/foo//bar/./zote/../../thed', '/');
		$expected = '/foo/thed';
		$this->assertEquals($actual, $expected);
	}

	function test_pathResolve_slashBothEnds() {
		$actual = Util::pathResolve('/foo//bar/./zote/../../thed/', '/');
		$expected = '/foo/thed/';
		$this->assertEquals($actual, $expected);
	}
	
	function test_pathResolve_slashNeitherEnd() {
		$actual = Util::pathResolve('foo//bar/./zote/../../thed', '/');
		$expected = 'foo/thed';
		$this->assertEquals($actual, $expected);
	}

	function test_pathResolve_windows_absolute() {
		$actual = Util::pathResolve('c:\\foo\\\\bar\\.\\zote\\..\\..\\thed', '\\');
		$expected = 'c:\\foo\\thed';
		$this->assertEquals($actual, $expected);
	}

	function test_pathResolve_windows_relative() {
		$actual = Util::pathResolve('foo\\\\bar\\.\\zote\\..\\..\\thed', '\\');
		$expected = 'foo\\thed';
		$this->assertEquals($actual, $expected);
	}
	
}
