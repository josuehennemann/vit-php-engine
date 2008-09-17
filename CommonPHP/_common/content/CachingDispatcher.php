<?
require_once("_common/content/ContentDispatcher.php");

define("DEFAULT_CACHE_DIR", "./cache");
define("DEFAULT_CACHE_LIFETIME", 600);	//measured in seconds

/** A decorator around another dispatcher providing caching capability.
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.2 $ $Date: 2006/08/10 02:27:46 $ */
class CachingDispatcher extends ContentDispatcher {
	var $wrappedDispatcher;

	var $cacheLifetime;

	var $cacheDir;

	/** Property object containing information about last cached page.
	*	Is <code>NULL</code> if info is not available (e.g., no page were cached by this object).
	*	<p>Property names:<p>
	*	'id'				cache id of the page. <code>FALSE</code> means page is non-cacheable
	*	'cachedFileExisted'	whether cached page file existed prior to rendering of the last page
	*	'oldCachedFileSize'	size of the old cached page file (if it existed)
	*	'cachedFileAge'		age of the cached page file (if it existed)
	*	'cacheHit'			whether the page was taken from cache
	*	'cachedFileSize'	size of the new cached page file
	*/
	var $lastPageInfo;

	/** @param wrappedDipsatcher - a dispatcher object to be provided with caching capability. */
	function CachingDispatcher(&$wrappedDispatcher) {
		$this->wrappedDispatcher = &$wrappedDispatcher;
		$this->cacheLifetime = NULL; //invalidate
		$this->cacheDir = NULL; //invalidate
		$this->lastPageInfo = FALSE;	//contains no info on construction
	}

	function getLastPageInfo() {
		return $this->lastPageInfo;
	}

	/** After the specified time period the cached copy is considered old and page is regenerated 
	*	on next request. */
	function setCacheLifetime($lifetime) {
		if ($lifetime <= 0) {
			die("Cache lifetime should be greater than zero! ($lifetime)");
		}
		$this->cacheLifetime = $lifetime;
	}

	/** Sets the cache storage directory. */
	function setCacheDir($cacheDir) {
		if (!file_exists($cacheDir)) {
			if ( mkdir($cacheDir, 0700) ) {
				echo("Created cache dir: $cacheDir<br>");
			} else {
				echo("Failed to create cache dir: $cacheDir<br>");
			}
		}

		$this->cacheDir = $cacheDir;
	}

	/** Overrides <code>renderPage()</code> to include caching. */
	function renderPage() {
		$this->lastPageInfo = new Properties();

		if (NULL == $this->cacheLifetime) {	//lifetime was not set, use default
			$this->setCacheLifetime(DEFAULT_CACHE_LIFETIME);
		}
		if (NULL == $this->cacheDir) {	//dir was not set, use default
			$this->setCacheDir(DEFAULT_CACHE_DIR);
		}
		$cacheId = $this->generateCacheId();
				
		if ( FALSE !== $cacheId ) {	//Page is cacheable
			$cachedFileName = $this->cacheDir."/".md5($cacheId);	//md5 is used to insure that cacheId does not contain filesysem-affecting substrings (such as ".." or "/")

			$cacheFileExists = file_exists($cachedFileName);
			$this->lastPageInfo->setProperty('cachedFileExisted', $cacheFileExists);

			if ($cacheFileExists) {
				$this->lastPageInfo->setProperty('oldCachedFileSize', filesize($cachedFileName));
				$modificationTime = filemtime($cachedFileName);
				$fileAge = time() - $modificationTime;
				$this->lastPageInfo->setProperty('cachedFileAge', $fileAge);
			}

			if ( $cacheFileExists && $fileAge < $this->cacheLifetime ) {	//found matching copy
				//echo("Taking contents from $cachedFileName<br>");
				$this->lastPageInfo->setProperty('cacheHit', true);
				include($cachedFileName);
				return;
			} else {	//render page to cache
				$this->lastPageInfo->setProperty('cacheHit', false);
				ob_start();	//enable buffering to put generated page into cache later

				$this->wrappedDispatcher->renderPage();
			
				$pageContents = ob_get_contents();	//page contents to be cached
				ob_end_flush();

				$fid = fopen($cachedFileName, "wb");
				if (FALSE == $fid) {
					die("caching failed!");
				}

				fputs($fid, $pageContents);	//write contents to cache file

				fclose($fid);

				$this->lastPageInfo->setProperty('cachedFileSize', strlen($pageContents));
			}
		} else {	//Caching is disabled for this page: just render, do not store
			$this->wrappedDispatcher->renderPage();
		}
		$this->lastPageInfo->setProperty('id', $cacheId);
	}

	/** Clears the cache, deleting all its entries. */
	function clearCache() {
		clearstatcache();
		//Load Directory Into Array
		$files = array();
		$handle = opendir($this->cacheDir);
		while ($file = readdir($handle))
			$files[count($files)] = $file;

		//Clean up and sort
		closedir($handle);
		// sort($files);

		foreach($files as $fil) {
			if ( $fil!="." && $fil!=".." ) {
				unlink($this->cacheDir."/".$fil);
			}
		}
	}

/* virtual protected */
	/** This method is used to generate page identifier for cache storage (by default it's done
	*	by hashing URI).
	*	If the storage contains page with the same identifier, page's contents are taken from cache.
	*	By convention, if for some page this method returns <code>FALSE</code>, the page is not cached.
	*	Therefore, you can override this method to make some pages non-cacheable (just return 
	*	<code>FALSE</code> for them).
	*	@return identifier to be used to identify cached page within cache storage. */
	function generateCacheId() {
		return md5($_SERVER['REQUEST_URI']);
	}
}
?>