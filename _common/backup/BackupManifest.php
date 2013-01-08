<?
define("MANIFEST_VERSION", 2);

/** 
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.1 $ $Date: 2006/03/10 13:30:12 $ */
class BackupManifest {
	var $prop;
	var $groupCount;

	function BackupManifest() {
		$this->prop = new Properties();
		$this->groupCount = 1;
		$this->prop->setProperty("version", MANIFEST_VERSION);
		$this->prop->setProperty("timestamp", time());
	}

	function addBackupGroup($bg) {
		$groupN = $this->groupCount;

		$this->prop->setProperty("group{$groupN}_name", $bg->getName());
		$elems = $bg->getElements();
//		$this->prop->setProperty("group$groupN_files", count($elems));
		$fileN = 1;
		foreach($elems as $elem) {
			$elemFile = $elem->getLocalFile();
			$this->prop->setProperty("group{$groupN}_file{$fileN}", $elemFile);
			$comments = $elem->getComments();
			if ( NULL != $comments ) {
				$commentN = 1;
				foreach( $comments as $commName=>$commValue ) {
					$this->prop->setProperty("group{$groupN}_file{$fileN}_comment{$commentN}_name", $commName);
					$this->prop->setProperty("group{$groupN}_file{$fileN}_comment{$commentN}_value", $commValue);
					++$commentN;
				}
			}
			++$fileN;
		}
		$this->groupCount++;
	}

	function save($fname) {
		$this->prop->save($fname);
	}

	/** @return array of <code>BackupGroup</code> objects formed from manifest loaded. */
	function load($fname) {
		$toRet = array();

		$this->prop->load($fname);

		$ver = $this->prop->getProperty("version");
		if ( MANIFEST_VERSION != $ver ) {
			echo("Warning: version mismatch (found ver=$ver, current version=".MANIFEST_VERSION.")");
		}

		$groupN = 1;
		while( FALSE !== ($groupName = $this->prop->getProperty("group{$groupN}_name")) ) {	//read next group, if any
			$buGroup = new BackupGroup($groupName);
			$fileN = 1;
			while( FALSE !== ($localFile = $this->prop->getProperty("group{$groupN}_file{$fileN}")) ) { //read next file, if any
				$commentN = 1;
				$comments = array();
				while( FALSE !== ($commName = $this->prop->getProperty("group{$groupN}_file{$fileN}_comment{$commentN}_name")) ) {	//read next file comment, if any
					$commValue = $this->prop->getProperty("group{$groupN}_file{$fileN}_comment{$commentN}_value");
					$comments[$commName] = $commValue;	//fill parameters array
					$commentN++;
				}

				if ( 0 == count($comments) ) {
					$comments = NULL;
				}
				$buElement = new BackupElement($localFile, $comments);
				$buGroup->addElement($buElement);
				$fileN++;
			}
			//$buGroup is formed at this point
			array_push($toRet, $buGroup);
			$groupN++;
		}
		$this->groupCount = $groupN;

		return $toRet;
	}

}
?>