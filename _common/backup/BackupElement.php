<?
//require_once("_common/Some.php");

/** 
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.1 $ $Date: 2006/03/10 13:30:12 $ */
class BackupElement {
	var $localFile;
	var $comments;	//array of comments

	/** @param $localFile local file name of an element.
	*	@param $comments array of comments: ('commentName'=>'commentValue')*/
	function BackupElement($localFile, $comments = NULL) {
		$this->localFile = $localFile;
		$this->comments = $comments;
	}

	function getLocalFile() {
		return $this->localFile;
	}

	function getComments() {
		return $this->comments;
	}
}
?>