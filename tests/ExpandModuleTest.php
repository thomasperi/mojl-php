<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use PHPUnit\Framework\TestCase as TestCase;

final class ExpandModuleTest extends TestCase {

	function test_expandModule_oneLevelUp() {
		$stack = [
			['module' => 'src/zote'],
			['module' => 'src/sbor/thed'],
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
			['module' => 'src/zote'],
			['module' => 'src/sbor/thed'],
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
			['module' => 'src/zote'],
			['module' => 'src/sbor/thed'],
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
			['module' => 'src/zote'],
			['module' => 'src/sbor/thed'],
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
