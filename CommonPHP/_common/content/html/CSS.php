<?
require_once("_common/content/Tag.php");

/** Container for CSS ('link' tag with css-specific parameters).
	@author Vitaly E. Lischenko
	@version $Revision: 1.1 $ $Date: 2006/08/10 02:25:44 $ */
class CSS extends Tag {
	function CSS($cssFile = NULL) {
		if (NULL == $cssFile) {
			return;
		}
		parent::Tag("link");
		parent::setAttribute("href", $cssFile);
		parent::setAttribute("rel", "stylesheet"); 
		parent::setAttribute("type", "text/css");
	}
}
?>