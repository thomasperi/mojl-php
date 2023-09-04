<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use ThomasPeri\Mojl\TemplateCache as TemplateCache;
use PHPUnit\Framework\TestCase as TestCase;

final class TemplateCacheTest extends TestCase {

	function test_cache() {
		$tplCache = new TemplateCache();
		$templatePath = __DIR__ . '/TemplateCacheTest/src/foo/foo.tpl.php';

		$actual = $tplCache->require($templatePath)('a', 'b');
		$expected = 'ab0';
		$this->assertEquals($actual, $expected);

		$actual = $tplCache->require($templatePath)('c', 'd');
		$expected = 'cd1';
		$this->assertEquals($actual, $expected);

		$actual = $tplCache->require($templatePath)('e', 'f');
		$expected = 'ef2';
		$this->assertEquals($actual, $expected);
	}

}
