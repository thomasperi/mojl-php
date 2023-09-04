<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use PHPUnit\Framework\TestCase as TestCase;

final class IncludeTemplateTest extends TestCase {

	function test_output() {
		$templatePath = __DIR__ . '/IncludeTemplateTest/src/foo/foo.tpl.php';

		// includeTemplate just passes the helper and props on to the template,
		// so they don't need to be real-world values in this test.
		$helper = 123;
		$props = 456;

		$expected = '123 | 456';

		$actual = trim(Util::includeTemplate($templatePath, $helper, $props));

		$this->assertEquals($expected, $actual);
	}

	function test_return() {
		$templatePath = __DIR__ . '/IncludeTemplateTest/src/bar/bar.tpl.php';

		// includeTemplate just passes the helper and props on to the template,
		// so they don't need to be real-world values in this test.
		$helper = 123;
		$props = 456;

		$expected = '123 : 456';

		$actual = Util::includeTemplate($templatePath, $helper, $props);

		$this->assertEquals($expected, $actual);
	}

	function test_failIfBoth() {
		$templatePath = __DIR__ . '/IncludeTemplateTest/src/zote/zote.tpl.php';

		// includeTemplate just passes the helper and props on to the template,
		// so they don't need to be real-world values in this test.
		$helper = 123;
		$props = 456;

		$expected = "Template $templatePath had non-empty values for both its output and its return value";

		try {
			$actual = Util::includeTemplate($templatePath, $helper, $props);
		} catch (\Exception $e) {
			$actual = $e;
		}

		$this->assertEquals($expected, $actual->getMessage());
	}

}
