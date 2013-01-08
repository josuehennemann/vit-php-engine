<?
require_once("ControlElement.php");

/** Implementation of control element representing filter.
 * Filter is used to restrict table rows to ones containing specific value in specified column.  
 * 
 *	@author Vitaly E. Lischenko
 *	@version $Revision: 1.4 $ $Date: 2006/08/17 20:09:32 $ */
class Filter extends ControlElement {
	var $selected;
	var $columnName;
	var $filterValuesQuery;
	
	/** Constructs new filter given db table visualizer and column name. 
	* @param $dbtv parent db table visualizer.
	* @param $columnName name of a column filter is created for. */
	function Filter(&$dbtv, $columnName) {
		parent::ControlElement($dbtv);		
		$this->columnName = $columnName;
		
		//pregenerate and store query on construction so it's not affected by other filters
		$this->filterValuesQuery = $this->generateFilterValuesQuery($this->columnName);
	}

	/** Visualizes this filter.
	 * Default implementation is to render select box containing filter values. 
	 * Parameters are used to choose selected value.*/
	function visualize() {
		$filterName = $this->generateId(); 
		$rs = $this->dbConn->query($this->filterValuesQuery);
		?><select id="<?=$filterName?>" name="<?=$filterName?>" onchange="submit()"><?
			?><option value="NULL" <?= ($this->selected === NULL) ? "selected" : "" ?>>All<?
			while($rs->nextRow()) {
				$value = $rs->nextField();
				?><option value="<?= $value ?>" <?= ($this->selected == $value) ? "selected" : "" ?>><?=$value?><?
			}
		?></select><?
	}

	/** Updates filter parameters to reflect changes user may have done.
	 * Default implementation is to analyze GET vars to see whether user 
	 * has chosen some value. */
	function update() {		
		$paramName = $this->generateId();
		if ( isset($_GET[$paramName]) && $_GET[$paramName] !== "NULL") {
			$this->selected = $_GET[$paramName]; 
		}
		if (@$_GET[$paramName] === "NULL") {
			$this->selected = NULL;
		}
	}

	/** Implementation of application: change dbtv's "where" specification to account filter's effect. */
	function apply() {
		if ( NULL === $this->getSelected() ) {	//filter incative, do nothing
			return;
		}
		$originalWhere = $this->dbtv->getWhereSpec();
		$changedWhere = ""; 
		if (!empty($originalWhere)) {
			$changedWhere = "($originalWhere) AND ";				
		}
		//append filter's subclause
		$changedWhere .= $this->getColumnName()."='".$this->getSelected()."'";  
		
		$this->dbtv->setWhereSpec($changedWhere);
	}
		
	/** @return currently selected element of a filter. */
	function getSelected() {
		return $this->selected;
	}
	
	/** @return column name. */
	function getColumnName() {
		return $this->columnName;
	}
	
	/** Protected.
	 * Generates query used to fetch values for this filter.
	 * Default implementation is to fetch all distinct values of the given column.
	 * @param $columnName name of a column to fetch possible values for. */
	function generateFilterValuesQuery($columnName) {
		$toRet = "SELECT DISTINCT($columnName)";
		$toRet.= "FROM ";
		$toRet.= $this->dbtv->getTableSpec();
		
		$toRet.= "WHERE ";
		$whereSpec = $this->dbtv->getWhereSpec();
		if ( !empty($whereSpec) ) {
			$toRet.= " (";
			$toRet.= $whereSpec;
			$toRet.= ") ";
			$toRet.= "AND ";
		}
		$toRet.= "LENGTH($columnName)>0 ";
		$toRet.= "ORDER BY $columnName ASC";

		return $toRet;
	}
	
	/** Generates id for this filter. */
	function generateId() {
		return "filter_{$this->columnName}"; 
	}
	
}
?>