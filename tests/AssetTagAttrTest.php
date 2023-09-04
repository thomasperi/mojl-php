<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use ThomasPeri\Mojl\HashCache as HashCache;
use PHPUnit\Framework\TestCase as TestCase;

final class AssetTagAttrTest extends TestCase {

	static private $defaults = [
		'buildDevDir' => 'dev',
		'buildDistDir' => 'dist',
		'pageRelativeUrls' => false,
		'isDev' => false,
		'collatePages' => false,
		// 'buildAssetsDir' => 'assets',
		'cacheFile' => 'mojl-cache.json',
		'cacheSave' => false,
		'cacheTTL' => 1 * 24 * 60 * 60 * 1000,
	];

	static $fooHash = '?h=C*7Hteo!D9vJXQ3UfzxbwnXaijM~';
	static $barHash = '?h=Ys23Ag!5IOWqZCw9QGaVDdHwH00~';

	function test_devFile() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, ['base' => $base, 'isDev' => true]);
			$settings->_cache = new HashCache($settings);
			
			$currentPage = '/index.html';
			$type = 'txt';
			$collations = ['test'];
			$options = null;
			
			$actual = Util::assetTagAttr($settings, $currentPage, $type, $collations, $options);
			$expected = ['/test.txt' . self::$fooHash];
			$this->assertEquals($actual, $expected);
		});
	}

	function test_distFile() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, ['base' => $base]);
			$settings->_cache = new HashCache($settings);
			
			$currentPage = '/index.html';
			$type = 'txt';
			$collations = ['test'];
			$options = null;
			
			$actual = Util::assetTagAttr($settings, $currentPage, $type, $collations, $options);
			$expected = ['/test.txt' . self::$barHash];
			$this->assertEquals($actual, $expected);
		});
	}

	function test_omitHash() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, ['base' => $base]);
			$settings->_cache = new HashCache($settings);
			
			$currentPage = '/index.html';
			$type = 'txt';
			$collations = ['test'];
			$options = [ 'hash' => false ];
			
			$actual = Util::assetTagAttr($settings, $currentPage, $type, $collations, $options);
			$expected = ['/test.txt'];
			$this->assertEquals($actual, $expected);
		});
	}

	function test_multipleUrls() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, ['base' => $base]);
			$settings->_cache = new HashCache($settings);
			
			$currentPage = '/index.html';
			$type = 'txt';
			$collations = ['aaa', 'bbb'];
			$options = [ 'hash' => false ];
			
			$actual = Util::assetTagAttr($settings, $currentPage, $type, $collations, $options);
			$expected = ['/aaa.txt', '/bbb.txt'];
			$this->assertEquals($actual, $expected);
		});
	}

	function test_defaultCollation() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'collations' => [
					(object) [ 'name' => 'site' ]
				],
			]);
			$settings->_cache = new HashCache($settings);
			
			$currentPage = '/index.html';
			$type = 'txt';
			$collations = null;
			$options = [ 'hash' => false ];
			
			$actual = Util::assetTagAttr($settings, $currentPage, $type, $collations, $options);
			$expected = ['/site.txt'];
			$this->assertEquals($actual, $expected);
		});
	}

	function test_implicitCollationsFromSettings() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'collations' => [
					// Only the 'name' properties matter here,
					// but real collations would have 'modules' properties too.
					(object) [ 'name' => 'one' ],
					(object) [ 'name' => 'two' ],
					(object) [ 'name' => 'three' ],
				],
			]);
			$settings->_cache = new HashCache($settings);
			
			$currentPage = '/index.html';
			$type = 'txt';
			$collations = null;
			$options = [ 'hash' => false ];
			
			$actual = Util::assetTagAttr($settings, $currentPage, $type, $collations, $options);
			$expected = ['/one.txt', '/two.txt', '/three.txt'];
			$this->assertEquals($actual, $expected);
		});
	}

	function test_encodeSpecialChars() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [ 'base' => $base ]);
			$settings->_cache = new HashCache($settings);
			
			$currentPage = '/index.html';
			$type = 'txt';
			$collations = ['<"\'&>-test-<"\'&>']; // unrealistic, but test it anyway
			$options = [ 'hash' => false ];
			
			$actual = Util::assetTagAttr($settings, $currentPage, $type, $collations, $options);
			$expected = ['/&lt;&quot;&apos;&amp;&gt;-test-&lt;&quot;&apos;&amp;&gt;.txt'];
			$this->assertEquals($actual, $expected);
		});
	}

	function test_normalizeLeadingSlash() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [ 'base' => $base ]);
			$settings->_cache = new HashCache($settings);
			
			$currentPage = '/index.html';
			$type = 'txt';
			$collations = ['/test'];
			$options = [ 'hash' => false ];
			
			$actual = Util::assetTagAttr($settings, $currentPage, $type, $collations, $options);
			$expected = ['/test.txt'];
			$this->assertEquals($actual, $expected);
		});
	}

	function test_resolvePath() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [ 'base' => $base ]);
			$settings->_cache = new HashCache($settings);
			
			$currentPage = '/index.html';
			$type = 'txt';
			$collations = ['test/foo/..//./bar'];
			$options = [ 'hash' => false ];
			
			$actual = Util::assetTagAttr($settings, $currentPage, $type, $collations, $options);
			$expected = ['/test/bar.txt'];
			$this->assertEquals($actual, $expected);
		});
	}

	function test_relativize() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base,
				'pageRelativeUrls' => true,
			]);
			$settings->_cache = new HashCache($settings);
			
			$currentPage = '/bar/zote/index.html';
			$type = 'txt';
			$collations = ['foo/test'];
			$options = [ 'hash' => false ];
			
			$actual = Util::assetTagAttr($settings, $currentPage, $type, $collations, $options);
			$expected = ['../../foo/test.txt'];
			$this->assertEquals($actual, $expected);
		});
	}

	function test_pageCollations() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base . '/page-collations',
				'collatePages' => true,
				'collations' => [
					(object) [ 'name' => 'site' ],
					(object) [ 'name' => 'about/index', 'page' => '/about/index.html' ],
					(object) [ 'name' => 'index', 'page' => '/index.html' ],
				],
			]);
			$settings->_cache = new HashCache($settings);
			$type = 'js';
			$options = [ 'hash' => false ];
			
			{
				$currentPage = '/index.html';
				$collations = null;
			
				$actual = Util::assetTagAttr($settings, $currentPage, $type, $collations, $options);
				$expected = ['/site.js', '/index.js'];
				$this->assertEquals($actual, $expected);
			}

			{
				$currentPage = '/about/index.html';
				$collations = null;
			
				$actual = Util::assetTagAttr($settings, $currentPage, $type, $collations, $options);
				$expected = ['/site.js', '/about/index.js'];
				$this->assertEquals($actual, $expected);
			}
			
			{
				$currentPage = '/about/index.html';
				$collations = ['']; // only the page collation
			
				$actual = Util::assetTagAttr($settings, $currentPage, $type, $collations, $options);
				$expected = ['/about/index.js'];
				$this->assertEquals($actual, $expected);
			}

			{
				$currentPage = '/about/index.html';
				$collations = ['', 'site']; // custom order
			
				$actual = Util::assetTagAttr($settings, $currentPage, $type, $collations, $options);
				$expected = ['/about/index.js', '/site.js'];
				$this->assertEquals($actual, $expected);
			}
		});
	}

	function test_pageCollationsNonSubdirectoryPrefix() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = (object) array_merge(self::$defaults, [
				'base' => $base . '/page-collations-nosubdir',
				'collatePages' => true,
				'collations' => [
					(object) [ 'name' => 'site' ],
					(object) [ 'name' => 'about', 'page' => '/about.htm' ],
					(object) [ 'name' => 'about/us', 'page' => '/about/us.htm' ],
				],
				'templateOutputSuffix' => '.htm',
			]);
			$settings->_cache = new HashCache($settings);
			$type = 'js';
			$options = [ 'hash' => false ];
			
			{
				$currentPage = '/about.htm';
				$collations = null;
			
				$actual = Util::assetTagAttr($settings, $currentPage, $type, $collations, $options);
				$expected = ['/site.js', '/about.js'];
				$this->assertEquals($actual, $expected);
			}

			{
				$currentPage = '/about.htm';
				$collations = ['']; // only the page collation
			
				$actual = Util::assetTagAttr($settings, $currentPage, $type, $collations, $options);
				$expected = ['/about.js'];
				$this->assertEquals($actual, $expected);
			}

			{
				$currentPage = '/about/us.htm';
				$collations = ['', 'site']; // custom order
			
				$actual = Util::assetTagAttr($settings, $currentPage, $type, $collations, $options);
				$expected = ['/about/us.js', '/site.js'];
				$this->assertEquals($actual, $expected);
			}

		});
	}

}
