<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Runtime as Runtime;
use PHPUnit\Framework\TestCase as TestCase;

final class RuntimeTest extends TestCase {

	static $fooHash = '?h=C*7Hteo!D9vJXQ3UfzxbwnXaijM~';
	static $barHash = '?h=Ys23Ag!5IOWqZCw9QGaVDdHwH00~';

	static function ob($fn) {
		$result = '';
		ob_start();
		try {
			$fn();
		} finally {
			$result = ob_get_clean();
		}
		return $result;
	}
	
	static function unwhiten($html) {
		return preg_replace('#>\s+<#', '><', trim($html));
	}
	
	function test_basic() {
		$subBase = '/' . __FUNCTION__;
		_CloneBox::run(__FILE__, function ($base, $box) use ($subBase) {
			$base .= $subBase;
			$request = '/about/?one=1';
			
			$actual = self::ob(fn () =>
				Runtime::build($base, null, null, $request)
			);
			
			$expected = '
				<!DOCTYPE html>
				<html>
				<head>
					<title>About | Example Site</title>
					<link rel="stylesheet" href="/site.css' . self::$fooHash . '" /></head>
				<body>
					<h1>About</h1>
					<p>This is the about page</p>
					<div>FOOTER</div>
					<script src="/site.js' . self::$barHash . '"></script>
				</body>
				</html>
			';
			
			$this->assertEquals(
				self::unwhiten($actual),
				self::unwhiten($expected)
			);

		});
	}
	
}
