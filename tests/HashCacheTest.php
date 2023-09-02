<?php
namespace ThomasPeri\Mojl\Test;
use ThomasPeri\Mojl\Util as Util;
use ThomasPeri\Mojl\Options as Options;
use ThomasPeri\Mojl\HashCache as HashCache;
use PHPUnit\Framework\TestCase as TestCase;

final class HashCacheTest extends TestCase {

	static private $fooHash = 'C*7Hteo!D9vJXQ3UfzxbwnXaijM~';
	
	static private function retro($base, $relFile) {
		$absFile = $base. '/' . $relFile;
		$oneSecondAgo = time() - 1000;
		touch($absFile, $oneSecondAgo, $oneSecondAgo);
		return $absFile;
	}

	function test_HashCache_noEntries() {
		_CloneBox::run(__FILE__, function ($base) {
			$settings = Options::expand([
				'base' => $base,
			]);
			$cache = new HashCache($settings);
			
			$internalCache = $cache->getCache();
		
			$this->assertEquals(json_encode($internalCache->entries), '{}');
		});
	}

	function test_HashCache_noEntryBeforeCreated() {
		_CloneBox::run(__FILE__, function ($base) {
			$settings = Options::expand([
				'base' => $base,
			]);
			$cache = new HashCache($settings);
			$relFile = 'src/foo/foo.txt';
			
			$entry = $cache->readExistingEntry($relFile);
			$this->assertEquals($entry, null);
		});
	}

	function test_HashCache_freshEntryAfterCreated() {
		_CloneBox::run(__FILE__, function ($base) {
			$settings = Options::expand([
				'base' => $base,
			]);
			$cache = new HashCache($settings);
			$relFile = 'src/foo/foo.txt';
			
			$entryCreated = $cache->createEntry($relFile);
			$entryRead = $cache->readExistingEntry($relFile);

			$this->assertEquals($entryCreated, $entryRead);

			$this->assertEquals($entryRead->hash, self::$fooHash);
			$this->assertEquals($entryRead->relFile, $relFile);

			$this->assertTrue($cache->entryIsFresh($entryRead));
		});
	}

	function test_HashCache_noFreshEntryAfterModified() {
		_CloneBox::run(__FILE__, function ($base) {
			$settings = Options::expand([
				'base' => $base,
			]);
			$cache = new HashCache($settings);
			$relFile = 'src/foo/foo.txt';
			$absFile = self::retro($base, $relFile);
			
			$cache->createEntry($relFile);
		
			$entry = $cache->readExistingEntry($relFile);
			$this->assertTrue($cache->entryIsFresh($entry));
			
			file_put_contents($absFile, 'bar');

			$entry = $cache->readExistingEntry($relFile);
			$this->assertFalse($cache->entryIsFresh($entry));
		});
	}

	function test_HashCache_newFreshEntryIfNotFresh() {
		_CloneBox::run(__FILE__, function ($base) {
			$settings = Options::expand([
				'base' => $base,
			]);
			$cache = new HashCache($settings);
			$relFile = 'src/foo/foo.txt';
			$absFile = self::retro($base, $relFile);

			$createdEntry = $cache->createEntry($relFile);
			
			file_put_contents($absFile, 'bar');

			$existingEntry = $cache->readExistingEntry($relFile);
			$this->assertSame($createdEntry, $existingEntry);
			
			$freshEntry = $cache->getFreshEntry($relFile);
			$this->assertNotEquals($existingEntry, $freshEntry);
		});
	}

	function test_HashCache_sameFreshEntryIfFresh() {
		_CloneBox::run(__FILE__, function ($base) {
			$settings = Options::expand([
				'base' => $base,
			]);
			$cache = new HashCache($settings);
			$relFile = 'src/foo/foo.txt';
			$absFile = self::retro($base, $relFile);

			$createdEntry = $cache->createEntry($relFile);
			$reCreatedEntry = $cache->createEntry($relFile);
			$this->assertNotSame($createdEntry, $reCreatedEntry);

			$existingEntry = $cache->readExistingEntry($relFile);
			$freshEntry = $cache->getFreshEntry($relFile);
			$this->assertSame($reCreatedEntry, $existingEntry);
			$this->assertSame($existingEntry, $freshEntry);
		});
	}
	
	function test_HashCache_noCacheFileWithoutCacheSave() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = Options::expand([
				'base' => $base,
			]);
			$cache = new HashCache($settings);
			$relFile = 'src/foo/foo.txt';
			$absFile = self::retro($base, $relFile);

			$cache->getFreshEntry($relFile);
			$after = $box->snapshot();
			$this->assertEquals($after, ['src/foo/foo.txt' => 'foo']);
		});
	}
	
	function test_HashCache_cacheFileWithCacheSave() {
		_CloneBox::run(__FILE__, function ($base, $box) {
			$settings = Options::expand([
				'base' => $base,
			]);
			$cache = new HashCache($settings);
			$relFile = 'src/foo/foo.txt';
			$absFile = self::retro($base, $relFile);

			$cache->getFreshEntry($relFile);
			$cache->saveCache();
			$after = $box->snapshot();
			
			$fileList = array_keys($after);
			$this->assertEquals($fileList, [
				'mojl-cache.json',
				'src/foo/foo.txt',
			]);
			
			$savedCache = json_decode($after['mojl-cache.json']);
			$this->assertEquals(array_keys(get_object_vars($savedCache)), ['entries', 'expires']);
			
			$this->assertTrue($savedCache->expires > time());
			
			$savedEntries = $savedCache->entries;
			$this->assertEquals(array_keys(get_object_vars($savedEntries)), ['src/foo/foo.txt']);
			
			$savedEntry = $savedEntries->$relFile;
			$this->assertEquals(array_keys(get_object_vars($savedEntry)), ['mtime', 'hash', 'relFile']);
			$this->assertEquals($savedEntry->relFile, 'src/foo/foo.txt');
			$this->assertEquals($savedEntry->hash, self::$fooHash);
		});
	}
	
	

	// to-do: complete tests

}

