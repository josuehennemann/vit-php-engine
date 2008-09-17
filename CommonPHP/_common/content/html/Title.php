<?
require_once("_common/content/Tag.php");
require_once("_common/content/CustomCode.php");

/** Represents abstarct HTML 'title' tag.
	@author Vitaly E. Lischenko
	@version $Revision: 1.1 $ $Date: 2006/08/10 02:25:44 $ */
class Title extends Tag {
	function Title($title = NULL) {
		if (NULL == $title) {
			return;
		}
		parent::Tag("title", new CustomCode($title));
	}
}
?>