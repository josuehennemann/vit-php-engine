<?
/**	This feeder is intended to feed content using custom HTTP headers, such as "Content-Disposition" and "Content-Type".
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.2 $ $Date: 2006/08/10 02:27:46 $ */
class ContentFeeder {
	/** Feeds the content page based on request (taken from globals). */
	function feed() {
		header("Content-Type: ".$this->getContentType());
		header("Content-Disposition: ".$this->getDisposition());
		$content = @$this->getContent();
		$len = @strlen($content);
		header("Content-Length: ".$len);
		echo $content;
		exit;
	}

	function getContent() {
		die("Should be implemented in class derived from ContentFeeder");
	}

	function getContentType() {
		die("Should be implemented in class derived from ContentFeeder");
	}

	function getDisposition() {
		die("Should be implemented in class derived from ContentFeeder");
	}
}
?>