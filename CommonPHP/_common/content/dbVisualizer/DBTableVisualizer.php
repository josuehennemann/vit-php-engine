<?
require_once("Filter.php");
require_once("Sorter.php");

/** Visualizes databased table.
* 
*	Note: this simplified version generates MySQL-compliant queries. Bridging required?
* 
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.10 $ $Date: 2006/08/23 19:31:02 $ */
class DBTableVisualizer {
	var $dbConn;
	
	/** For correct rendering of sorters and filters keys of this array should be set. 
	 * The same values for keys should be used there in $filters, $sorters and $columnCaptions. */
	var $columnSpec;
	var $tableSpec;
	var $whereSpec;
	var $groupSpec;
	var $orderSpec;
	var $limitSpec;
	
	/** Associative array of captions for columns. Keys must match existing keys in $columnSpec.*/
	var $columnCaptions;
	
	/** Associative array containing registered filters. Keys must match existing keys in $columnSpec. */
	var $filters;
	
	/** Associative array containing registered sorters. Keys must match existing keys in $columnSpec. */
	var $sorters;
	
	/** Constructs new visualizer given the database connection. 
	 * @param dbConn database connection to use. */
	function DBTableVisualizer(&$dbConn) {
		$this->dbConn = $dbConn;
		$this->filters = array();
		$this->sorters = array();
	}

	function getColumnSpec() {
		return $this->columnSpec;
	}
	function setColumnSpec($columnSpec) {
		$this->columnSpec = $columnSpec;
	}
	
	function getTableSpec() {
		return $this->tableSpec;
	}
	function setTableSpec($tableSpec) {
		$this->tableSpec = $tableSpec;
	}
	
	function getWhereSpec() {
		return $this->whereSpec;
	}
	function setWhereSpec($whereSpec) {
		$this->whereSpec = $whereSpec;
	}
	
	function getGroupSpec() {
		return $this->groupSpec;
	}
	function setGroupSpec($groupSpec) {
		$this->groupSpec = $groupSpec;
	}
	
	function getOrderSpec() {
		return $this->orderSpec;
	}
	function setOrderSpec($orderSpec) {
		$this->orderSpec = $orderSpec;
	}

	function getLimitSpec() {
		return $this->limitSpec;
	}
	function setLimitSpec($limitSpec) {
		$this->limitSpec = $limitSpec;
	}
	
	/** Create filter for column having specified key. */
	function createFilter($colKey, $colName = NULL) {
		if ( NULL == $colName ) {
			if ($this->columnSpec[$colKey]) {
				$colName = $this->columnSpec[$colKey];	//lookup column specification for column definition 
			} else {
				$colName = $colKey;	//use colKey
			}
		}		
		$this->filters[$colKey] = &new Filter(&$this, $colName);
	}
	function deleteFilter($colKey) {
		unset($this->filters[$colKey]);
	}
	
	function createSorter($colKey, $colName = NULL) {
		if ( NULL == $colName ) {
			if ($this->columnSpec[$colKey]) {
				$colName = $this->columnSpec[$colKey];	//lookup column specification for column definition 
			} else {
				$colName = $colKey;	//use colKey
			}
		}
		$this->sorters[$colKey] = new Sorter(&$this, $colName, $this->columnCaptions[$colKey]);
	}
	function deleteSorter($colKey) {
		unset($this->sorters[$colKey]);
	}
	
	/** Visualizes table.
	 * Default implementation is to render filters, sorters, 
	 * columns descriptions and rows data into HTML. */
	function visualize() {
		//First, update and apply filters and sorters, as they may have impact on select query
		$keys = array_keys($this->filters);
		foreach($keys as $k) {
			$this->filters[$k]->update();
			$this->filters[$k]->apply();
		}
		$keys = array_keys($this->sorters);
		foreach($keys as $k) {
			$this->sorters[$k]->update();
			$this->sorters[$k]->apply();
		}

		$selQuery = $this->generateSelectQuery();
		$rs = $this->dbConn->query($selQuery);
		
		$this->visualizeTableHeader($rs);
		
		$this->visualizeControlElements($rs);
		
		$index = 0;
		while($rs->nextRow()) {
			$this->visualizeRow($index++, $rs);
		}
		$this->visualizeTableFooter($rs);
	}
	
	function visualizeControlElements($rs) {
		?><form id="_ceForm"><?
		$this->visualizeFilters($rs);
		$this->visualizeSorters($rs);
		?></form><?
	}
	
	/** Visualizes table header. */
	function visualizeTableHeader($rs) {
		?><table><?
	}
	
	/** Visualizes registered filters. */
	function visualizeFilters($rs) {
		foreach($this->filters as $filterName=>$filterObj) {	
			$filterObj->visualize();
		}
	}
	
	/** Visualizes registered sorters. */
	function visualizeSorters($rs) {
		foreach($this->sorters as $sorterName=>$sorterObj) {	
			$sorterObj->visualize();
		}
	}

	/** Visualizes single row.
	 * @param $rowNumber*/
	function visualizeRow($rowNumber, $rs) {		
		$fieldNumber = 0;
		if (0 == $rowNumber && !empty($this->columnCaptions)) {
			?><tr><?
				foreach($this->columnCaptions as $name=>$cap) {
					?><th><?=$cap?></th><?
				}
			?></tr><?
		}
		?><tr><?
			while( FALSE !== ($fld = $rs->nextField()) ) {
				$this->visualizeField($fieldNumber++, $fld);
			}
		?></tr><?
	}
	
	/** Visualizes single field. */
	function visualizeField($fieldNumber, $field) {
		?><td><?=empty($field) ? "&nbsp;" : $field?></td><?
	}
	
	/** Visualizes table footer. */
	function visualizeTableFooter($rs) {
		?></table><?
	}
	
	
	/** Protected. 
	 * Generates SELECT query based on this object state (columns, tables etc).
	 * Default implementation is to generate MySQL query (is it also ANSI SQL compatible?)*/
	function generateSelectQuery() {
		//die("createSelectQuery() is supposed to be overriden by derived class");
		$toRet = "SELECT ";

		$toRet.= implode( 
			",", 
			//array_map("addslashes",array_values($this->columnSpec))
			array_values($this->columnSpec) 
		);
		$toRet.= " ";
		$toRet.= "FROM ";
		$toRet.= $this->tableSpec;
//		$toRet.= addslashes($this->tableSpec);
		$toRet.= " ";

		if ( !empty($this->whereSpec) ) {
			$toRet.= "WHERE ";
			$toRet.= $this->whereSpec;	//TODO: addslashes? adding it causes error now
			$toRet.= " ";
		}
		
		if ( !empty($this->groupSpec) ) {
			$toRet.= "GROUP BY ";
			$toRet.= addslashes($this->groupSpec);
			$toRet.= " ";
		}

		if ( !empty($this->orderSpec) ) {
			$toRet.= "ORDER BY ";
			$toRet.= addslashes($this->orderSpec);
			$toRet.= " ";
		}
		
		if ( !empty($this->limitSpec) ) {
			$toRet.= "LIMIT ";
			$toRet.= addslashes($this->limitSpec);
		}
/*?><h1><?=$toRet?></h1><?//*/
		return $toRet;
	}
}
?>