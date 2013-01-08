<?
/** Abstract dispatcher responsible for rendering page objects (depending on requests).
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.1 $ */
class ContentDispatcher {
	/** Renders page based on request (taken from globals). */
	function renderPage() {
		die("Not implemented (findPage() defined in derived class is supposed to be called instead!)");
	}
}
?>