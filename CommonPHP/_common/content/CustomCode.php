<?
require_once("Tag.php");

/** Allows arbitrary code to be used as <code>MarkupCode</code> object.
	@author Vitaly E. Lischenko
	@version $Revision: 1.2 $ $Date: 2006/08/10 02:01:30 $ */
class CustomCode extends MarkupCode {

	/** @param $str HTML code in string form. */
	function CustomCode($str) {
		$this->contents = $str;
	}

	function render() {
		return $this->contents;	//do not try to call render() on contents, just return it, as it's string.
	}
}
?>