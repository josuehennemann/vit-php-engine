<?
require_once("_common/content/jfacelike/IContentProvider.php");

/** Structured content provider for the framework.
*  
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.10 $ $Date: 2006/08/23 19:31:02 $ */
class IStructuredContentProvider extends IContentProvider {

	/** Gets elements from a specified input. */
	function getElements($input) {
		die("getElements() should be implemented in derived class.");
	}
}
?>