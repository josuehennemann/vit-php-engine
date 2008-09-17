<?
require_once("_common/backup/BackupElement.php");

/** 
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.1 $ $Date: 2006/03/10 13:30:12 $ */
class BackupGroup {
	var $name;
	var $elements;

	/** Creates new group with specified name, optioanlly including all files from the specified directory. */
	function BackupGroup($name, $baseDir = NULL) {
		$this->name = $name;
		$this->elements = array();
		if (NULL != $baseDir) {
			$this->addDirFiles($baseDir);
		}
	}

	function getName() {
		return $this->name;
	}

	function getElements() {
		return $this->elements;
	}

	function addDirFiles($dir) {
		$current_dir = opendir($dir);
		while($entryname = readdir($current_dir)) {
			if ( is_dir("$dir/$entryname") && ($entryname != "." and $entryname!="..") ) {
       			$this->addAllFiles("$dir/$entryname");
    		} elseif ( $entryname != "." && $entryname!=".." ) {
				$this->addFile("$dir/$entryname");
			}
		}
		closedir($current_dir);
	}

	/** @param fname name of a file to add to group
	*	@param comments optional comments for this file */
	function addFile($fname, $comments = NULL) {
		if ( is_file($fname) ) {
			array_push($this->elements, new BackupElement($fname, $comments));
		} else {
			echo("Warning: file $fname does not exist or not a file<br>");
		}
	}

	/** @param elem <code>BackupElement</code> object to add to group */
	function addElement(&$elem) {
		array_push($this->elements, $elem);
	}
}
?>