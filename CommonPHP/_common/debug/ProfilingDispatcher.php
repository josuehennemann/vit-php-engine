<?
require_once("_common/content/ContentDispatcher.php");

/** Profiling dispatcher - a simple dispatcher that does nothing more (yet)
*	than measuring the time of page generation.
*	Implemented as a decorator over ContentDispatcher.
*	@author Vitaly E. Lischenko
*	@version $Revision $ $Date: 2006/03/10 13:30:12 $ */
class ProfilingDispatcher extends ContentDispatcher {
	var $wrappedDispatcher;

	var $elapsed;

	/** @param wrappedDipsatcher - a dispatcher object to be provided with profiling capability. */
	function ProfilingDispatcher(&$wrappedDispatcher) {
		$this->wrappedDispatcher = &$wrappedDispatcher;
		$elapsed = "Undefined till the rendering finished";
	}

	/** Overrides <code>renderPage()</code> to provide authorizing. */
	function renderPage() {
		$startTime = gettimeofday();

		$this->wrappedDispatcher->renderPage();

		$finishTime = gettimeofday();

		$dSec = $finishTime['sec'] - $startTime['sec'];
		$dUSec = $finishTime['usec'] - $startTime['usec'];

		$this->elapsed = $dSec + $dUSec/1000000.0;
	}

	function getElapsed() {
		return $this->elapsed;
	}
}