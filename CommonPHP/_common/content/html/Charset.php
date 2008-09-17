<?
require_once("_common/content/Tag.php");

/** Charset tag ('meta' tag, actually).
	@author Vitaly E. Lischenko
	@version $Revision: 1.1 $ $Date: 2006/08/10 02:25:44 $*/
class Charset extends Tag {
	function Charset($charset = "iso-8859-1") {
		if (NULL == $charset) {
			return;
		}
		parent::Tag("meta");
		parent::setAttribute("http-equiv", "Content-Type");
		parent::setAttribute("content", "text/html; charset=$charset"); 
	}
}
?>