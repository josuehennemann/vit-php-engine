<?
require_once("_common/content/jfacelike/IStructuredContentProvider.php");

/** Viewer for table.
*  
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.10 $ $Date: 2006/08/23 19:31:02 $ */
class TableViewer {
	var $contentProvider;
	var $labelProvider;
	var $input;

	/** Associative array of captions for columns. Keys must match existing keys in $columnSpec.*/
	var $columnCaptions;
	
	/** Associative array containing registered filters. Keys must match existing keys in $columnSpec. */
	var $filters;
	
	/** Associative array containing registered sorters. Keys must match existing keys in $columnSpec. */
	var $sorters;
	
	function TableViewer() {
		$this->filters = array();
		$this->sorters = array();
		$this->captions = null;
	}
	
	function setColumnCaptions($cc) {
		$this->columnCaptions = $cc;
	}
	
	/** @param $cp this table viewer accepts structured content provider intances. */
	function setContentProvider($cp) {
		$this->contentProvider = $cp;
	}
	
	function setLabelProvider($lp) {
		$this->labelProvider = $lp;
	}
	
	function setInput($i) {
		$this->input = $i;
	}
	
	/**
	 * Renders a table.
	 * Default implementation is to render filters, sorters, 
	 * columns descriptions and rows data into HTML.
	 */
	function render() {
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

		$elements = $this->contentProvider->getElements($this->input);
		
		$this->visualizeTableHeader($elements);
		
		$this->visualizeControlElements($elements);
		
		$index = 0;
		foreach($elements as $elem) {
			$this->visualizeRow($index++, $elem);
		}
		$this->visualizeTableFooter($elements);
	}
	
	function visualizeControlElements($elements) {
		?><form id="_ceForm"><?
		$this->visualizeFilters($elements);
		$this->visualizeSorters($elements);
		?></form><?
	}
	
	/** Visualizes table header. */
	function visualizeTableHeader($elements) {
		?><table><?
	}
	
	/** Visualizes registered filters. */
	function visualizeFilters($elements) {
		foreach($this->filters as $filterName=>$filterObj) {	
			$filterObj->visualize();
		}
	}
	
	/** Visualizes registered sorters. */
	function visualizeSorters($elements) {
		foreach($this->sorters as $sorterName=>$sorterObj) {	
			$sorterObj->visualize();
		}
	}

	/** Visualizes single row.
	 * @param $rowNumber*/
	function visualizeRow($rowNumber, $elements) {		
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
	function visualizeTableFooter($elements) {
		?></table><?
	}
	
}
?>