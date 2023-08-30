<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use PHPUnit\Framework\TestCase as TestCase;

final class PathRelativeTest extends TestCase {

	function test_pathRelative_defaultSep() {
		// Ensure that the method works with no $sep argument,
		// because the rest of the tests use it,
		// but normal usage of the method does not.
		$sep = DIRECTORY_SEPARATOR;
		$actual = Util::pathRelative(
			"foo{$sep}bar{$sep}thed{$sep}",
			"foo{$sep}zote{$sep}sbor{$sep}",
		);
		$expected = "..{$sep}..{$sep}zote{$sep}sbor";
		$this->assertEquals($expected, $actual);
	}

	function test_pathRelative_inside() {
		$actual = Util::pathRelative(
			'foo/bar/zote/sbor',
			'foo/bar/zote/sbor/thed/sneg',
			'/'
		);
		$expected = 'thed/sneg';
		$this->assertEquals($expected, $actual);
	}

	function test_pathRelative_encosing() {
		$actual = Util::pathRelative(
			'foo/bar/zote/sbor/thed/sneg',
			'foo/bar/zote/sbor',
			'/'
		);
		$expected = '../..';
		$this->assertEquals($expected, $actual);
	}

	function test_pathRelative_commonParent() {
		$actual = Util::pathRelative(
			'foo/bar/zote/sbor',
			'foo/bar/thed/sneg',
			'/'
		);
		$expected = '../../thed/sneg';
		$this->assertEquals($expected, $actual);
	}

	function test_pathRelative_slashes() {
		$actual = Util::pathRelative(
			'foo/bar/zote/sbor/',
			'foo/bar/thed/sneg/',
			'/'
		);
		$expected = '../../thed/sneg';
		$this->assertEquals($expected, $actual);

		$actual = Util::pathRelative(
			'foo/bar/zote/sbor',
			'foo/bar/thed/sneg/',
			'/'
		);
		$expected = '../../thed/sneg';
		$this->assertEquals($expected, $actual);

		$actual = Util::pathRelative(
			'foo/bar/zote/sbor/',
			'foo/bar/thed/sneg',
			'/'
		);
		$expected = '../../thed/sneg';
		$this->assertEquals($expected, $actual);
	}

	function test_pathRelative_windows() {
		$actual = Util::pathRelative(
			'c:\\foo\\bar\\zote\\sbor\\',
			'c:\\foo\\bar\\thed\\sneg\\',
			'\\'
		);
		$expected = '..\\..\\thed\\sneg';
		$this->assertEquals($expected, $actual);
	}
}
