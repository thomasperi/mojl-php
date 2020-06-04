<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Mojl as Mojl;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * Test the Aliaser class.
 */
final class MojlTest extends TestCase {

	function get_mojl($sandboxed_docroot, $options = []) {
		$mojl = new Mojl();

		$sandbox = __DIR__ . '/sandbox/';
		$doc_root = $sandbox . '/' . $sandboxed_docroot;

		$options['doc_root'] = $doc_root;
		$options['modules_dir'] = $doc_root . '/modules';

		$mojl->config($options);
		return $mojl;
	}
	
	function test_single_module() {
		$mojl = $this->get_mojl(__FUNCTION__);

		ob_start();
		$mojl->include('foo-bar');
		$actual = ob_get_clean();
		$expected = 'Foo Bar';
		
		$this->assertEquals($expected, $actual);
	}

	function test_single_module_input() {
		$mojl = $this->get_mojl(__FUNCTION__);

		ob_start();
		$mojl->include('foo', ['a' => 'b', 'c' => 'd']);
		$actual = ob_get_clean();
		$expected = 'a=b;c=d;';
		
		$this->assertEquals($expected, $actual);
	}

	function test_input_module_non_existant() {
		$mojl = $this->get_mojl(__FUNCTION__);

		ob_start();
		$mojl->include('foo', ['hello' => 'world']);
		$actual = ob_get_clean();
		$matched = preg_match(
			// var_dump can output different things, so test more generally.
			// The output should be inside a pre tag, and it should include
			// the key and the value in that order.
			'@^<pre>[\S\s]+?hello[\S\s]+?world[\S\s]+?</pre>$@',
			$actual
		);
		
		$this->assertEquals($matched, 1);
	}

	function test_nested_module() {
		$mojl = $this->get_mojl(__FUNCTION__);

		ob_start();
		$mojl->include('foo-bar');
		$actual = ob_get_clean();
		$expected = 'Foo Zote Sbor Bar';
		
		$this->assertEquals($expected, $actual);
	}

	function test_nested_module_deep() {
		$mojl = $this->get_mojl(__FUNCTION__);

		ob_start();
		$mojl->include('a-b');
		$actual = ob_get_clean();
		$expected = 'A C G D E G F B';
		
		$this->assertEquals($expected, $actual);
	}

	function test_url_timestamp_true_default() {
		$mojl = $this->get_mojl(__FUNCTION__);

		ob_start();
		$mojl->include('foo');
		$actual = ob_get_clean();
		
		// Split along the equal sign,
		// expect the arrays' [0] values to be equal, 
		// and expect the actual array's [1] value
		// to be greater than or equal to that of the expected array,
		// because future tests will be done on the same or newer copies
		// of the space gif.
		$actual = explode('=', $actual);
		$expected = [
			'/modules/foo/images/spacer.gif?t',
			'1524185447'
		];
		$this->assertEquals($expected[0], $actual[0]);
		$this->assertTrue($expected[1] <= $actual[1]);
	}

	function test_url_timestamp_true_explicit() {
		$mojl = $this->get_mojl(__FUNCTION__);

		ob_start();
		$mojl->include('foo');
		$actual = ob_get_clean();
		
		// Split along the equal sign,
		// expect the arrays' [0] values to be equal, 
		// and expect the actual array's [1] value
		// to be greater than or equal to that of the expected array,
		// because future tests will be done on the same or newer copies
		// of the space gif.
		$actual = explode('=', $actual);
		$expected = [
			'/modules/foo/images/spacer.gif?t',
			'1524185447'
		];
		$this->assertEquals($expected[0], $actual[0]);
		$this->assertTrue($expected[1] <= $actual[1]);
	}

	function test_url_timestamp_false() {
		$mojl = $this->get_mojl(__FUNCTION__);

		ob_start();
		$mojl->include('foo');
		$actual = ob_get_clean();
		$expected = '/modules/foo/images/spacer.gif';
		$this->assertEquals($expected, $actual);

	}

	function test_get_url_timestamp_true_default() {
		$mojl = $this->get_mojl(__FUNCTION__);

		ob_start();
		$mojl->include('foo');
		$actual = ob_get_clean();
		
		// Split along the equal sign,
		// expect the arrays' [0] values to be equal, 
		// and expect the actual array's [1] value
		// to be greater than or equal to that of the expected array,
		// because future tests will be done on the same or newer copies
		// of the space gif.
		$actual = explode('=', $actual);
		$expected = [
			'***/modules/foo/images/spacer.gif?t',
			'1524185447'
		];
		$this->assertEquals($expected[0], $actual[0]);
		$this->assertTrue($expected[1] <= $actual[1]);
	}

	function test_get_url_timestamp_true_explicit() {
		$mojl = $this->get_mojl(__FUNCTION__);

		ob_start();
		$mojl->include('foo');
		$actual = ob_get_clean();
		
		// Split along the equal sign,
		// expect the arrays' [0] values to be equal, 
		// and expect the actual array's [1] value
		// to be greater than or equal to that of the expected array,
		// because future tests will be done on the same or newer copies
		// of the space gif.
		$actual = explode('=', $actual);
		$expected = [
			'***/modules/foo/images/spacer.gif?t',
			'1524185447'
		];
		$this->assertEquals($expected[0], $actual[0]);
		$this->assertTrue($expected[1] <= $actual[1]);
	}

	function test_get_url_timestamp_false() {
		$mojl = $this->get_mojl(__FUNCTION__);

		ob_start();
		$mojl->include('foo');
		$actual = ob_get_clean();
		$expected = '***/modules/foo/images/spacer.gif';
		$this->assertEquals($expected, $actual);
	}

	function test_url_outside_web_root() {
		$mojl = $this->get_mojl(__FUNCTION__ . '/htdocs');
		
		$expected = 'fail';
		$actual = 'fail'; // Gets set to something else if the include doesn't throw an Exception.

		ob_start();
		try {
			// This should fail because the module tries to load a url outside doc root.
			$mojl->include('foo');
			$actual = ob_get_contents();
		} catch (\Exception $e) {
		}
		ob_end_clean();
		
		$this->assertEquals($expected, $actual);
	}

	function test_url_non_existant() {
		$mojl = $this->get_mojl(__FUNCTION__ . '/htdocs');
		
		$expected = 'fail';
		$actual = 'fail'; // Gets set to something else if the include doesn't throw an Exception.

		ob_start();
		try {
			// This should fail because the module tries to load a url that doesn't exist.
			$mojl->include('foo');
			$actual = ob_get_contents();
		} catch (\Exception $e) {
		}
		ob_end_clean();
		
		$this->assertEquals($expected, $actual);
	}

	function test_mojl_exists() {
		$mojl = $this->get_mojl(__FUNCTION__);

		ob_start();
		$mojl->include('foo');
		$actual = ob_get_clean();
		$expected = 'success';
		$this->assertEquals($expected, $actual);
	}

	function test_outside_modules_dir() {
		$mojl = $this->get_mojl(__FUNCTION__ . '/htdocs');
		
		$expected = 'fail';
		$actual = 'fail'; // Gets set to something else if the include doesn't throw an Exception.

		ob_start();
		try {
			// This should fail because . and / are invalid module name characters.
			$mojl->include('../foo');
			$actual = ob_get_contents();
		} catch (\Exception $e) {
		}
		ob_end_clean();
		
		$this->assertEquals($expected, $actual);
	}
}
