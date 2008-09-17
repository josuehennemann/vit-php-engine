<?
require_once("_common/content/Tag.php");

/** Represents HTML 'meta' tag. 
	@author Vitaly E. Lischenko
	@version $Revision: 1.1 $ $Date: 2006/08/10 02:25:44 $ */
class Meta extends Tag {
	/** determines whether to use 'http-equiv' or 'name' attribute */
	var $useHttpEquiv;	
	
	/** @param $useHttpEquiv determines whether to use 'http-equiv' or 'name' attribute */
	function Meta($name = NULL, $contents = NULL, $useHttpEquiv=false) {
		if (NULL == $name) {
			return;
		}
		$this->useHttpEquiv = $useHttpEquiv;

		parent::Tag("meta"); 
		parent::setAttribute($this->useHttpEquiv ? "http-equiv": "name", $name);
		parent::setAttribute("content", $contents); 
	}
}
?>