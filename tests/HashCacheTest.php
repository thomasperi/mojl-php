<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use ThomasPeri\Mojl\Options as Options;
use ThomasPeri\Mojl\HashCache as HashCache;
use PHPUnit\Framework\TestCase as TestCase;

final class HashCacheTest extends TestCase {

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

	// to-do: complete tests

}

