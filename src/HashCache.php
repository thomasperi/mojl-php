<?php
namespace ThomasPeri\Mojl;

class HashCache {
	private $base = '';
	private $cacheFile = '';
	private $cacheTTL = 0;
	private $cache = null;
	private $jsonString = '';
	
	function __construct($settings = []) {
		$this->base = $settings['base'];
		if ($settings['cacheFile']) {
			$this->cacheFile = $settings['base'] . '/' . $settings['cacheFile'];
			$this->cacheTTL = $settings['cacheTTL'];
		}
	}
	
	function stamp($relFile) {
		$entry = $this->getFreshEntry($relFile);
		$stamp = $entry ? $entry['hash'] : 'not-found';
		return "?h=$stamp";
	}
	
	function stampAbs($absFile) {
		$relFile = Util::pathRelative($this->base, $absFile);
		return $this->stamp($relFile);
	}
	
	function getMtime($absFile) {
		clearstatcache(false, $absFile);
		return filemtime($absFile);
	}

	function getCache() {
		if (!$this->cache) {
			if ($this->cacheFile && is_file($this->cacheFile)) {
				$jsonString = file_get_contents($this->cacheFile);
				$this->jsonString = $jsonString;
			} else {
				$jsonString = '{ "entries": {}, "expires": 0 }';
			}
			$this->cache = json_decode($jsonString);
		}
		return $this->cache;
	}
	
	function readExistingEntry($relFile) {
		if (property_exists($this->getCache()->entries, $relFile)) {
			return $this->getCache()->entries->$relFile;
		}
	}
	
	function getFreshEntry($relFile) {
		$entry = $this->readExistingEntry($relFile);
		if (!$this->entryIsFresh($entry)) {
			$entry = $this->createEntry($relFile);
		}
		return $entry; // undefined if the file doesn't exist
	}
	
	function entryIsFresh($entry) {
		if (!$entry) {
			return false;
		}
		$absFile = $this->base . '/' . $entry->relFile;
		return file_exists($absFile) && ($entry->mtime === $this->getMtime($absFile));
	}
	
	function createEntry($relFile) {
		// Before adding new entries is a good time to purge entries for files
		// that don't exist anymore, but not *every* time an entry is added.
		$now = time();
		if ($this->cache && $now > $this->cache->expires) {
			$this->cache->expires = $now + $this->cacheTTL;
			$entries = $this->cache->entries;
			foreach ($entries as $relFileProp => $entry) {
				$absFile = $this->base . '/' . $relFileProp;
				if (!file_exists($absFile)) {
					unset($entries->$relFileProp);
				}
			}
		}
		
		// Get on with creating an entry.
		$absFile = $this->base . '/' . $relFile;
		$hash = $this->createHash($absFile);
		if ($hash) {
			$mtime = $this->getMtime($absFile);
			$entry = new \StdClass();
			$entry->mtime = $mtime;
			$entry->hash = $hash;
			$entry->relFile = $relFile;
			$this->getCache()->entries->$relFile = $entry;
			return $entry;
		}
	}
	
	function getHash($relFile) {
		$entry = $this->getFreshEntry($relFile);
		if (!$this->entryIsFresh($entry)) {
			$entry = $this->createEntry($relFile);
		}
		if ($entry) {
			return $entry->hash;
		}
	}
	
	function createHash($absFile) {
		if (file_exists($absFile)) {
			$content = file_get_contents($absFile);
			$hash = base64_encode(sha1($content, true));

			// The hash is in base64, so replace the base64 characters
			// that are reserved for URIs with characters that aren't.
			// Seems nicer than url-encoding them, and since they never need
			// to be decoded, they don't need to be standard base64 values.
			return str_replace(['+', '=', '/'], ['*', '~', '!'], $hash);
		}
	}

	function saveCache() {
		if ($this->cache && $this->cacheFile) {
			$newJsonString = json_encode($this->cache);
			if ($newJsonString !== $this->jsonString) {
				Util::writeFileRecursive($this->cacheFile, $newJsonString);
			}
		}
	}
	
}
