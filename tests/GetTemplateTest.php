<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use PHPUnit\Framework\TestCase as TestCase;

final class GetTemplateTest extends TestCase {

	function test_getTemplate_dirNamed() {
		$base = __DIR__ . '/GetTemplateTest';
		$module = 'src/foo';
		$expected = "$base/src/foo/foo.tpl.php";
		$actual = Util::getTemplate($base, $module);
		$this->assertEquals($expected, $actual);
	}

	function test_getTemplate_standalone() {
		$base = __DIR__ . '/GetTemplateTest';
		$module = 'src/bar';
		$expected = "$base/src/bar.tpl.php";
		$actual = Util::getTemplate($base, $module);
		$this->assertEquals($expected, $actual);
	}
	
	function test_getTemplate_noExist() {
		$base = __DIR__ . '/GetTemplateTest';
		$module = 'src/zote';
		$expected = false;
		$actual = Util::getTemplate($base, $module);
		$this->assertEquals($expected, $actual);
	}
	
}
