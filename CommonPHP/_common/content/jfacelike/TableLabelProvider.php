<?
require_once("_common/content/jfacelike/ILabelProvider.php");

/** Label provider for table viewers.
*  
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.10 $ $Date: 2006/08/23 19:31:02 $ */
class TableLabelProvider extends ILabelProvider {

	/** Abstract method to get label for specified column of a specified field. */
	function getColumnText($field, $fieldNumber) {
		die("getColumnText() should be implemented in derived class.");
	}
}
?>
