<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use PHPUnit\Framework\TestCase as TestCase;

final class ExpandModuleTest extends TestCase {

	function test_expandModule_oneLevelUp() {
		$stack = [
			(object)['module' => 'src/zote'],
			(object)['module' => 'src/sbor/thed'],
		];
		$actual = Util::expandModule(
			'/foo/bar',
			$stack,
			'../sneg',
		);
		$expected = 'src/sneg';
		$this->assertEquals($expected, $actual);
	}

	function test_expandModule_sameLevel() {
		$stack = [
			(object)['module' => 'src/zote'],
			(object)['module' => 'src/sbor/thed'],
		];
		$actual = Util::expandModule(
			'/foo/bar',
			$stack,
			'./sneg',
		);
		$expected = 'src/sbor/sneg';
		$this->assertEquals($expected, $actual);
	}

	function test_expandModule_absolute() {
		$stack = [
			(object)['module' => 'src/zote'],
			(object)['module' => 'src/sbor/thed'],
		];
		$actual = Util::expandModule(
			'/foo/bar',
			$stack,
			'src/sneg',
		);
		$expected = 'src/sneg';
		$this->assertEquals($expected, $actual);
	}

	function test_expandModule_absolute_slash() {
		$stack = [
			(object)['module' => 'src/zote'],
			(object)['module' => 'src/sbor/thed'],
		];
		$actual = Util::expandModule(
			'/foo/bar',
			$stack,
			'/src/sneg',
		);
		$expected = 'src/sneg';
		$this->assertEquals($expected, $actual);
	}

}
