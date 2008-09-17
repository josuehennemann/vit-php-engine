<?
require_once("_common/content/Tag.php");

/** Represents WML 'card' tag. 
	@author Vitaly E. Lischenko
	@version $Revision: 1.1 $ $Date: 2006/08/10 02:25:20 $ */
class WMLCard extends Tag {
	function WMLCard($id, $title, $inner) {
		parent::Tag("card", $inner);
		parent::setAttribute("id", $id);
		parent::setAttribute("title", $title); 
	}

}
?>