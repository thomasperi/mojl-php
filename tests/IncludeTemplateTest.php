<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use PHPUnit\Framework\TestCase as TestCase;

final class IncludeTemplateTest extends TestCase {

	function test_includeTemplate() {
		$templatePath = __DIR__ . '/IncludeTemplateTest/src/foo/foo.tpl.php';

		// includeTemplate just passes the helper and props on to the template,
		// so they don't need to be real-world values in this test.
		$helper = 123;
		$props = 456;

		$expected = '123 | 456';

		ob_start();
		Util::includeTemplate($templatePath, $helper, $props);
		$actual = trim(ob_get_clean());

		$this->assertEquals($expected, $actual);
	}

}
