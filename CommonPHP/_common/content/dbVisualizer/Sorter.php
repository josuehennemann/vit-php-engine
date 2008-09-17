<?
require_once("ControlElement.php");

/** Implementation of control element representing sorter.
 * Filter is used to sort rows by values of specified column.  
 * 
 *	@author Vitaly E. Lischenko
 *	@version $Revision: 1.9 $ $Date: 2006/08/24 19:41:59 $ */
class Sorter extends ControlElement {
	var $sortOrder;
	var $columnName;
	/** String used to render this sorter. */
	var $caption;
	
	/** Constructs new sorter given db table visualizer and column name. 
	* @param $dbtv parent db table visualizer.
	* @param $columnName name of a column filter is created for. 
	* @param $caption optional parameter used to set caption - a string used
	* to visualize sorter. If <code>NULL</code> or not specified, column 
	* name is used. */
	function Sorter(&$dbtv, $columnName, $caption=NULL) {
		parent::ControlElement(&$dbtv);
		$this->columnName = $columnName;
		$this->sortOrder = NULL;
		$this->setCaption($caption);
	}

	/** @return name of a column this sorter is created for. */
	function getColumnName() {
		return $this->columnName;
	}
	/** @return sort order string ("ASC" or "DESC"). */
	function getSortOrder() {
		return $this->sortOrder;
	}

	/** Gets caption used to render sorter.
	 * @return caption used to render sorter. */
	function getCaption() {
		return $this->caption;
	} 
	/** Sets caption used to render sorter.
	 * @param $newCap new caption string to use. If <code>NULL</code> or not specified, column 
	* name is used.*/
	function setCaption($newCap) {
		$this->caption = ( NULL == $newCap ) ? $this->columnName : $newCap;
	} 
	
	/** Visualizes sorter given its name and parameters.
	 * Default implementation is to render column name linked to reverse sorting.
	 * Only one sorter of this class may be selected as they all share the same hidden input fields.*/
	function visualize() {
		$sorterName = $this->generateId();
		global $_printedSorterInputs;
		if ( !isset($_printedSorterInputs) || !$_printedSorterInputs ) {
			?><input type="hidden" name="selectedSorter" id="selectedSorter" value="<?=$sorterName?>"><?
			?><input type="hidden" name="sortOrder" id="sortOrder" value="<?=$this->sortOrder?>"><?
			$_printedSorterInputs = true;
		}
		?><a class="sorter" onclick="document.getElementById('selectedSorter').value='<?=$sorterName?>'; document.getElementById('sortOrder').value='<?=$this->sortOrder == "ASC" ? "DESC" : "ASC"?>'; document.getElementById('_ceForm').submit();"><?
			?><?=$this->getCaption()?><?
		?></a><?
	}

	/** Updates sorter parameters to reflect changes user may have done.
	 * Default implementation is to analyze GET vars to see whether user 
	 * has chosen some sort value. */
	function update() {
		if ( isset($_GET["selectedSorter"]) && $_GET["selectedSorter"] !== "NULL") {
			$id = $this->generateId();
			if ($_GET["selectedSorter"] == $id) {
				$this->sortOrder = $_GET["sortOrder"];
			}
			return;
		} 
		$this->sortOrder = NULL;
	}

	/** Implementation of application: change dbtv's "order" specification to account sorter's effect. */
	function apply() {
		if ( NULL === $this->sortOrder ) {	//sorter incative, do nothing
			return;
		}

		$originalOrder = $this->dbtv->getOrderSpec();
		
		$changedOrder = "";						 
		if (!empty($originalOrder)) {
			$changedOrder = "$originalOrder, ";				
		}
		//append sorter's subclause
		$changedOrder .= $this->getColumnName()." ".$this->getSortOrder();  
		
		$this->dbtv->setOrderSpec($changedOrder);
	}
	
	/** Generates id for this sorter. */
	function generateId() {
//		return "sorter_".$this->columnName; 
		return md5("sorter_".$this->columnName); 
	}
	
}
?>