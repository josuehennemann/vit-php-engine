<?
require_once("_common/backup/BackupGroup.php");
require_once("_common/backup/BackupManifest.php");
require_once("_common/util/Properties.php");

/** 
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.1 $ $Date: 2006/03/10 13:30:12 $ */
class Backup {
	var $groups;
	var $manifest;

	function Backup() {
		$this->manifest = new BackupManifest();
		$this->groups = array();
	}

	function addBackupGroup(&$bg) {
		array_push($this->groups, $bg);
		$this->manifest->addBackupGroup($bg);
	}

	/** @param $fname name of a backup file to backup to. */
	function createFile($fname) {
		die("Should be implemented by class derived from Backup");
	}

	/** @param $fname name of a backup file to restore from. */
	function restoreFile($fname) {
		die("Should be implemented by class derived from Backup");
	}

}
?>