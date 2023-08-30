<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use PHPUnit\Framework\TestCase as TestCase;

final class PathRelativeTest extends TestCase {

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
		);
		$expected = '../..';
		$this->assertEquals($expected, $actual);
	}

	function test_pathRelative_commonParent() {
		$actual = Util::pathRelative(
			'foo/bar/zote/sbor',
			'foo/bar/thed/sneg',
		);
		$expected = '../../thed/sneg';
		$this->assertEquals($expected, $actual);
	}

	function test_pathRelative_slashes() {
		$actual = Util::pathRelative(
			'foo/bar/zote/sbor/',
			'foo/bar/thed/sneg/',
		);
		$expected = '../../thed/sneg';
		$this->assertEquals($expected, $actual);

		$actual = Util::pathRelative(
			'foo/bar/zote/sbor',
			'foo/bar/thed/sneg/',
		);
		$expected = '../../thed/sneg';
		$this->assertEquals($expected, $actual);

		$actual = Util::pathRelative(
			'foo/bar/zote/sbor/',
			'foo/bar/thed/sneg',
		);
		$expected = '../../thed/sneg';
		$this->assertEquals($expected, $actual);
	}

	function test_pathRelative_windows() {
		$actual = Util::pathRelative(
			'c:\\foo\\bar\\zote\\sbor\\',
			'c:\\foo\\bar\\thed\\sneg\\',
		);
		$expected = '../../thed/sneg';
		$this->assertEquals($expected, $actual);
	}
}
