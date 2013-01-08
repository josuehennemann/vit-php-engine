<?
require_once("_common/content/DownloadFeeder.php");

/**	Concrete implementation of DownloadFeeder which feeds file specified on construction.
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.2 $ $Date: 2006/08/10 02:27:46 $ */
class FileDownloadFeeder extends DownloadFeeder {

	/** name of a file to upload to client */
	var $filename;
	var $downloadFilename;

	/** If not <code>null</code>, specifies content-type of a file being fed. */
	var $forcedContentType;

	/** @param $filename name of a file to feed
	*	@param $forcedContentType if specified, overrides content-type to specified value. 
	*	Note that you may experience some problems using this. I.e. images are usually 
	*	<I>displayed</I> by browsers, even if the browsers are told to save them. */
	function FileDownloadFeeder($filename, $forcedContentType = null) {
		$this->filename = $filename;
		$this->forcedContentType = $forcedContentType;
		$this->setDownloadName($filename);
	}

	function getContent() {
		ob_start();
		@readfile($this->filename);
		$toRet = ob_get_contents();
		ob_end_clean();
		return $toRet;
	}

	function setDownloadName($df) {
		$this->downloadFilename = $df;
	}

	function getDownloadName() {
		return $this->downloadFilename;
	}

	function getContentType() {
		if ( null == $this->forcedContentType ) {
			return $this->forcedContentType;
		} else {
			parent::getContentType();
		}
	}

}
?>