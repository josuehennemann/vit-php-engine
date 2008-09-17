<?

/** Structured content provider that operates over a DB backend.
*  
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.10 $ $Date: 2006/08/23 19:31:02 $ */
class DBContentProvider extends IStructuredContentProvider {
	var $dbConn;
	
	/** Constructs new content provider that forwards input (SQL queries) to specified database. 
	 * @param dbConn database connection to use. */
	function DBTableVisualizer(&$dbConn) {
		$this->dbConn = $dbConn;
//		$this->filters = array();
//		$this->sorters = array();
	}
	
	/** Gets elements from a specified input. 
     * @param input is an SQL query string.
     * @return array of rows. */
	function getElements($input) {
		$rs = $this->dbConn->query($input);
		
		$toRet = array();
		
		$index = 0;
		while($rs->nextRow()) {
//			$this->visualizeRow($index++, $rs);
			$toRet[$index++] = $rs->getCurrentRow(); 
		}
	}
}
?>