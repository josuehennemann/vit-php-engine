<?
require_once("_common/xls/AbstractExcelParser.php");
require_once("_common/xls/eep/ExcelExplorer.php");

/** A simple reading wrapper around Excel Explorer Pro.
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.1 $ $Date: 2006/03/10 13:30:12 $ */
class EEPWrapper extends AbstractExcelParser {
	/** Excel Parser object being wrapped. */
    var $wrapped;
	var $filename;

	/** Constructs wrapper and opens an XLS using specified filename.
	*	@param $filename name of a file to parse.
	*	@param $prereadFile whether to read xls file contents first and work with the string (rather then with the file).
	*	@param $options optional array parameter containing Execel Explorer options (optimizations).*/
	function EEPWrapper($filename, $prereadFile = true, $options = NULL) {
		$this->wrapped = new ExcelExplorer();

		if (!$prereadFile) {
			if ( NULL == $options ) {
				$this->wrapped->Explore_file($filename);
			} else {
				$this->wrapped->Explore_file($filename, $options);
			}
		} else {	//Prereading file
			$fd = fopen( $filename, 'rb');
			$content = fread ($fd, filesize($filename));
			fclose($fd);
			if ( NULL == $options ) {
				$this->wrapped->Explore($content);
			} else {
				$this->wrapped->Explore_file($content, $options);
			}
			unset( $content, $fd );
		}
	}

	/** @returns version of an excel file being parsed. */
	function getVersion() {
		return "not implemented";
	}

	/** @return number of worksheets in parsed file. */
	function worksheetsCount() {
		return $this->wrapped->GetWorksheetsNum();
	}

	/** @return number of worksheets in parsed file. */
	function worksheetName($ws) {
		$title = $this->wrapped->GetWorksheetTitle($ws);

//		TODO: find out why does it return string instead of text object
//		var_dump($title);
//		return $title->asHTML();

		return $title;
	}

	/** @return maximum row number (but not maximum row quantity!). */
	function getMaxRow($ws) {
		return $this->wrapped->GetLastRowIndex($ws);
	}

	/** @return maximum column number (but not maximum column quantity!). */
	function getMaxCol($ws) {
		return $this->wrapped->GetLastColumnIndex($ws);
	}

	/** Gets data from the specified cell, looking up the sst if needed. */
	function getData($col, $row, $worksheet) {
		$type = $this->wrapped->GetCellType($worksheet, $col, $row);
		$data = $this->wrapped->GetCellData($worksheet, $col, $row);

		switch ( $type ) {
		    case 1: // number
		      return $data; break;
		    case 2: // percentage
		      return ($data*100).'%'; break;
		    case 3: // text
		      return $this->wrapped->AsHTML($data); break;
		    case 4: // logical
		      return $data; break;
		    case 5: // error code
		      switch ( $data ) {
		        case 0x00:	//#NULL!
		        case 0x07:	//#DIV/0
		        case 0x0F:	//#VALUE!
		        case 0x17:	//#REF!
		        case 0x1D:	//#NAME?
		        case 0x24:	//#NUM!
		        case 0x2A:	//#N/A!
					return NULL;
					break;
		        default:
		          echo("Warning: Unknown error code<br>");
		          return NULL;
		          break;
		      }
		      break;

		    case 6: // date
		      return $data['string']; break;
		    case 0: // empty
		    case 7: // blank
		    case 8: // merged
		    	return NULL;
				break;
		    default: // unknown
 	            echo("Warning: Unknown cell type<br>");
		    	return NULL;
				break;
		  }
		echo("Debug assertion - you shouldn't see this<br>");
	}


	/** Gets font from the specified cell, looking up the fonts table.
	*	@returns an array of font properties, use <code>$font['name'], $font['height'], 
	*	$font['color'], $font['italic'], $font['strike'], $font['bold'], $font['script'], 
	*	$font['underline']</code> to access them.*/
	function getFont($col, $row, $worksheet) {
		return $this->wrapped->GetCellFont($worksheet, $col, $row);
	}

/*private static*/
	function errCodeToString($errCode) {
		$toRet = "unknown error";
		switch($errCode) {
			case 0: $toRet = "no errors"; break;
			case 1: $toRet = "file corrupted or not in Excel 5.0 and above format"; break;
			case 2: $toRet = "unknown Excel file version"; break;
			default: $toRet = "unknown error"; break;
		}
		return $toRet;
	}
}
?>