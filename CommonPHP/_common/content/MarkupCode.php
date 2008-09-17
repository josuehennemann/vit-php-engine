<?
/** Abstract base class for markup code. Implements composite pattern.
	@author Vitaly E. Lischenko
	@version $Revision: 1.1 $ $Date: 2006/08/10 02:27:54 $ */
class MarkupCode {
	var $contents;

	function MarkupCode($contents = NULL) {
		$this->contents = array();
		if (NULL != $contents) {
			array_push($this->contents, $contents);
		}
	}

	/** Appends specified contents to the end of internal content storage. */
	function addContents($contents) {
		array_push($this->contents, $contents);
		return $this;
	}

	function render() {
		if (NULL == $this->contents) {
			//die("Trying to render NULL content.");
			return "";	//Render to empty string
		}
		$toRet = "";
		foreach($this->contents as $cont) {
			$toRet .= $cont->render();
		}
		return $toRet;
	}
}
?>