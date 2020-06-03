<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Mojl as Mojl;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * Test the Aliaser class.
 */
final class MojlTest extends TestCase {

	// to-do: move this to a base class
	function get_mojl($sandboxed_docroot, $options = []) {
		$mojl = new Mojl();
		$sandbox = realpath(__DIR__ . '/sandbox/');
		$options['doc_root'] = realpath($sandbox . $sandboxed_docroot);
		$mojl->config($options);
		return $mojl;
	}
	
	function test_testing() {
		$mojl = $this->get_mojl('test_testing', ['debug' => true]);
		$this->assertTrue($mojl->config()['debug']);
	}
}
