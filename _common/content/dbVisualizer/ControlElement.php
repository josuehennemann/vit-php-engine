<?
/** Abstraction of a control element for database table visualizer.
 * Control element allows user to interact with the table visualizer to affect its state.  
 * 
 *	@author Vitaly E. Lischenko
 *	@version $Revision: 1.2 $ $Date: 2006/08/17 20:09:32 $ */
class ControlElement {
	/** Reference to parent object (of DBTableVisualizer class). */
	var $dbtv;
	var $dbConn;
	
	/** Constructs new control element for given db table visualizer. 
	* @param $dbtv parent db table visualizer.*/
	function ControlElement(&$dbtv) {
		$this->dbtv = &$dbtv;
		$this->dbConn = &$dbtv->dbConn;
	}

	/** Abstract method used to visualizes this object. */
	function visualize() {
		die("Implementation is to be provided by descendant class.");
	}

	/** Abstract method used to update control element's parameters to reflect 
	 * changes user may have done. */
	function update() {
		die("Implementation is to be provided by descendant class.");
	}
	
	/** Applies this control element. 
	 * Most likely it is made by changing dbtv's parameters. */
	function apply() {
		die("Implementation is to be provided by descendant class.");
	}
}
?>