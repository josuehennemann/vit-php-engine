<?
require_once("_common/xls/pepp/excelparser.php");
require_once("_common/xls/AbstractExcelParser.php");

/** A simple reading wrapper around PHP Excel Parser Pro.
*	NOTE: works with php >= 4.3 only !!
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.1 $ $Date: 2006/03/10 13:30:12 $ */
class PEPPWrapper extends AbstractExcelParser {
	/** Excel Parser object being wrapped. */
    var $wrapped;
	var $filename;

	/** Constructs wrapper and opens an XLS using specified filename. 
	*	@param $filename name of a file to parse.
	*	@param $prereadFile whether to read xls file contents first and work with the string (rather then with the file).
	*/
	function PEPPWrapper($filename, $prereadFile = true) {
		$this->filename = $filename;

		$this->wrapped = new ExcelFileParser( /*$logfilename, $logtype*/ );
//		$this->wrapped = new ExcelFileParser( "/tmp/xlsparserlog.txt", ABC_TRACE );

		if (!$prereadFile) {

			$err = $this->wrapped->ParseFromFile($filename);

		} else {	//Prereading file
			$fd = fopen( $filename, 'rb');
			$content = fread ($fd, filesize($filename));
			fclose($fd);

			$err = $this->wrapped->ParseFromString($content);

			unset( $content, $fd );
		}

		if ( 0 != $err ) {
			die("Parse error: ".$this->errCodeToString($err));
		}
	}

	/** @returns version of an excel file being parsed. */
	function getVersion($asString = false) {
		$ver = $this->wrapped->biff_version;
		if (!$asString) {
			return $ver;
		} else {
			$toRet = "unknown version";
			switch ($ver) {
				case 7:
					$toRet = "Excel 5-7"; break;
				case 8:
					$toRet = "Excel 2000"; break;
				case 10:
					$toRet = "Excel XP"; break;
				default:
					$toRet = "unknown version"; break;
			}
			return $toRet;
		}
	}

	/** @return number of worksheets in parsed file. */
	function worksheetsCount() {
		return count($this->wrapped->worksheet['name']);
	}

	/** @return number of worksheets in parsed file. */
	function worksheetName($ws) {
		$isUncompressedUC = $this->wrapped->worksheet['unicode'][$ws];
		return $isUncompressedUC ?
			$this->uc2html($this->wrapped->worksheet['name'][$ws]) :
			$this->wrapped->worksheet['name'][$ws];
	}

	/** @return maximum row number (but not maximum row quantity!). */
	function getMaxRow($ws) {
		return $this->wrapped->worksheet['data'][$ws]['max_row'];
	}

	/** @return maximum column number (but not maximum column quantity!). */
	function getMaxCol($ws) {
		return $this->wrapped->worksheet['data'][$ws]['max_col'];
	}

	/** Gets data from the specified cell, looking up the sst if needed. */
	function getData($col, $row, $worksheet) {
		$cell = $this->wrapped->worksheet['data'][$worksheet]['cell'][$row][$col];

		$type = $cell['type'];
		$data = $cell['data'];

		if( 0 == $type ) {
			$isUncompressed = $this->wrapped->sst['unicode'][$data];
			if ($isUncompressed) {	// (!$isUncompressed) ??
				return $this->uc2html($this->wrapped->sst['data'][$data]);
			}
			return $this->wrapped->sst['data'][$data];
		} else {
			return $data;
		}
	}

	/** Gets font from the specified cell, looking up the fonts table.
	*	@returns an array of font properties, use <code>$font['size'], $font['italic'], 
	*	$font['strikeout'], $font['bold'], $font['script'], $font['underline'], $font['name']</code>
	*	to access them. */
	function getFont($col, $row, $worksheet) {
		$cell = $this->wrapped->worksheet['data'][$worksheet]['cell'][$row][$col];

		$font = $cell['font'];
		return $font;
	}

/*private static*/
	function errCodeToString($errCode) {
		$toRet = "unknown error";
		switch($errCode) {
			case 0: $toRet = "no errors"; break;
			case 1: $toRet = "file read error"; break;
			case 2: $toRet = "file is too small to be an Excel file"; break;
			case 3: $toRet = "Excel file head read error"; break;
			case 4: $toRet = "file read error"; break;
			case 5: $toRet = "not Excel file or Excel version earlier than Excel 5.0"; break;
			case 6: $toRet = "corrupted file"; break;
			case 7: $toRet = "data not found"; break;
			case 8: $toRet = "unknown file version"; break;
			default: $toRet = "unknown error"; break;
		}
		return $toRet;
	}

/*private static*/
	function uc2html($str) {
		$ret = '';
		for( $i=0; $i<strlen($str)/2; $i++ ) {
			$charcode = ord($str[$i*2])+256*ord($str[$i*2+1]);
			$ret .= '&#'.$charcode;
		}
		return $ret;
	}
}
?>