<?
/** An abstract class defining interface for excel parsers.
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.1 $ $Date: 2006/03/10 13:30:12 $ */
class AbstractExcelParser {

	/** @returns version of an excel file being parsed. */
	function getVersion() {
		die("Not implemented - use derived classes to provide implementation");
	}

	/** @return number of worksheets in parsed file. */
	function worksheetsCount() {
		die("Not implemented - use derived classes to provide implementation");
	}

	/** @return number of worksheets in parsed file. */
	function worksheetName($ws) {
		die("Not implemented - use derived classes to provide implementation");
	}

	/** @return maximum row number (but not maximum row quantity!). */
	function getMaxRow($ws) {
		die("Not implemented - use derived classes to provide implementation");
	}

	/** @return maximum column number (but not maximum column quantity!). */
	function getMaxCol($ws) {
		die("Not implemented - use derived classes to provide implementation");
	}

	/** Gets data from the specified cell, looking up the sst if needed. */
	function getData($col, $row, $worksheet) {
		die("Not implemented - use derived classes to provide implementation");
	}

	/** Gets font from the specified cell, looking up the fonts table.
	*	@returns an array of font properties */
	function getFont($col, $row, $worksheet) {
		die("Not implemented - use derived classes to provide implementation");
	}
}
?>