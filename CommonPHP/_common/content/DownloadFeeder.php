<?
require_once("_common/content/ContentFeeder.php");

/**	An implementation of ContentFeeder forcing browsers to download file to client's machine.
*	This is an abstract class: getContent() and getDownloadedName() are still need to be implemented.
*	Based on article: http://www.onjava.com/pub/a/onjava/excerpt/jebp_3/index3.html
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.2 $ $Date: 2006/08/10 02:27:46 $ */
class DownloadFeeder extends ContentFeeder {
	function getContentType() {
		/* set the Content-Type header to a nonstandard value such as application/x-download. 
		It's very important that this header is something unrecognized by browsers because browsers often try to do something special when they recognize the content type.*/
		return "application/x-download";
	}

	function getDisposition() {
		return "attachment; filename=".$this->getDownloadName();
	}

	/** Generates filename to be used by default in the Save As dialog. */
	function getDownloadName() {
		die("Should be implemented by class derived from DownloadFeeder");
	}
}
?>