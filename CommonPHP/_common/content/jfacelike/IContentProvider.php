<?

/** Astract content provider for the framework.
*  
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.10 $ $Date: 2006/08/23 19:31:02 $ */
class IContentProvider {

	/** May be overriden to provide disposal logic. */
	function dispose() {
	}
	
	/** Notifies this content provider that the given viewer's input has been switched to a different element. */
	function inputChanged($viewer, $oldInput, $newInput) {	
	}
}
?>