<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use PHPUnit\Framework\TestCase as TestCase;

final class PathResolveTest extends TestCase {

	function test_pathResolve_tailSlashOnly() {
		$actual = Util::pathResolve('foo//bar/./zote/../../thed/');
		$expected = 'foo/thed';
		$this->assertEquals($actual, $expected);
	}

	function test_pathResolve_headSlashOnly() {
		$actual = Util::pathResolve('/foo//bar/./zote/../../thed');
		$expected = '/foo/thed';
		$this->assertEquals($actual, $expected);
	}

	function test_pathResolve_slashBothEnds() {
		$actual = Util::pathResolve('/foo//bar/./zote/../../thed/');
		$expected = '/foo/thed';
		$this->assertEquals($actual, $expected);
	}
	
	function test_pathResolve_slashNeitherEnd() {
		$actual = Util::pathResolve('foo//bar/./zote/../../thed');
		$expected = 'foo/thed';
		$this->assertEquals($actual, $expected);
	}

	function test_pathResolve_windows_absolute() {
		$actual = Util::pathResolve('c:\\foo\\\\bar\\.\\zote\\..\\..\\thed', '\\');
		$expected = 'c:/foo/thed';
		$this->assertEquals($actual, $expected);
	}

	function test_pathResolve_windows_relative() {
		$actual = Util::pathResolve('foo\\\\bar\\.\\zote\\..\\..\\thed', '\\');
		$expected = 'foo/thed';
		$this->assertEquals($actual, $expected);
	}
	
}
