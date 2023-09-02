<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use ThomasPeri\Mojl\Options as Options;
use ThomasPeri\Mojl\HashCache as HashCache;
use PHPUnit\Framework\TestCase as TestCase;

final class HashCacheTest extends TestCase {

	static private $fooHash = 'C*7Hteo!D9vJXQ3UfzxbwnXaijM~';

	function test_HashCache_noEntries() {
		$settings = Options::expand([
			'base' => __DIR__ . '/HashCacheTest'
		]);
		$cache = new HashCache($settings);
			
		$internalCache = $cache->getCache();
		
		$this->assertEquals(json_encode($internalCache->entries), '{}');
	}

	function test_HashCache_noEntryBeforeCreated() {
		$settings = Options::expand([
			'base' => __DIR__ . '/HashCacheTest'
		]);
		$cache = new HashCache($settings);
		$relFile = 'src/foo/foo.txt';
			
		$entry = $cache->readExistingEntry($relFile);
		$this->assertEquals($entry, null);
	}

	function test_HashCache_freshEntryAfterCreated() {
		$settings = Options::expand([
			'base' => __DIR__ . '/HashCacheTest'
		]);
		$cache = new HashCache($settings);
		$relFile = 'src/foo/foo.txt';
			
		$entryCreated = $cache->createEntry($relFile);
		$entryRead = $cache->readExistingEntry($relFile);

		$this->assertEquals($entryCreated, $entryRead);

		$this->assertEquals($entryRead->hash, self::$fooHash);
		$this->assertEquals($entryRead->relFile, $relFile);

		$this->assertTrue($cache->entryIsFresh($entryRead));
	}

	function test_HashCache_noFreshEntryAfterModified() {
		_Clone::run(__FILE__, function ($base) {
			$settings = Options::expand([
				'base' => $base,
			]);
			$cache = new HashCache($settings);
			$relFile = 'src/foo/foo.txt';
			
			$cache->createEntry($relFile);
		
			$entry = $cache->readExistingEntry($relFile);
			$this->assertTrue($cache->entryIsFresh($entry));
			
			sleep(1);
			
			$absFile = $base . '/'. $relFile;
			file_put_contents($absFile, 'bar');

			$entry = $cache->readExistingEntry($relFile);
			$this->assertFalse($cache->entryIsFresh($entry));
		});
	}

	// to-do: complete tests

}

