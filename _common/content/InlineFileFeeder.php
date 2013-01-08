<?
require_once("_common/content/ContentFeeder.php");

/**	Feeds file using "inline" disposition.
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.2 $ $Date: 2006/08/10 02:27:46 $ */
class InlineFileFeeder extends ContentFeeder {

	/** Name of a file to feed. */
	var $filename;

	var $ext2mime;

	/** @param $ext2mime (optional) array to convert file extensions to mime type strings.
	*	Has a form of {"ext1" => "mime1", etc...}. If omitted, a predefined table will be used. */
	function InlineFileFeeder($ext2mime = NULL) {
		$this->ext2mime = 
			(NULL != $ext2mime) ? 
			$ext2mime:
			array(	//based on a table taken from http://archive.ncsa.uiuc.edu/SDG/Software/XMosaic/extension-map.html
				"pdf"	=>	"application/pdf",
				"ai"	=>	"application/postscript",
				"eps"	=>	"application/postscript",
				"ps"	=>	"application/postscript",
				"rtf"	=>	"application/rtf",
				""		=>	"application/slate",
				"tex"	=>	"application/x-tex",
				"texinfo"	=>	"application/x-texinfo",
				"texi"	=>	"application/x-texinfo",
				"t"		=>	"application/x-troff",
				"tr"	=>	"application/x-troff",
				"roff"	=>	"application/x-troff",
				"au"	=>	"audio/basic",
				"snd"	=>	"audio/basic",
				"aif"	=>	"audio/x-aiff",
				"aiff"	=>	"audio/x-aiff",
				"aifc"	=>	"audio/x-aiff",
				"wav"	=>	"audio/x-wav",
				"gif"	=>	"image/gif",
				"ief"	=>	"image/ief",
				"jpeg"	=>	"image/jpeg",
				"jpg"	=>	"image/jpeg",
				"jpe"	=>	"image/jpeg",
				"tiff"	=>	"image/tiff",
				"tif"	=>	"image/tiff",
				"xwd"	=>	"image/x-xwindowdump",
				"html"	=>	"text/html",
				"txt"	=>	"text/plain",
				"mpeg"	=>	"video/mpeg",
				"mpg"	=>	"video/mpeg",
				"mpe"	=>	"video/mpeg",
				"qt"	=>	"video/quicktime",
				"mov"	=>	"video/quicktime",
				"avi"	=>	"video/x-msvideo",
				"movie"	=>	"video/x-sgi-movie"
			);
		$this->filename = NULL;
	}

	/** Sets name of a file to be fed. */
	function setFilename($filename) {		
		$this->filename = $filename;
	}

	function getContent() {
		if ( NULL == $this->filename ) {
			echo("Name of a file to feed was not set");
			return;
		} else {
			ob_start();
			@readfile($this->filename);
			$toRet = ob_get_contents();
			ob_end_clean();
		}
		return $toRet;
	}

	function getContentType() {
		if ( NULL == $this->filename ) {
			echo("Name of a file to feed was not set");
			return;
		} else {
			$position = strrpos($this->filename, ".");
			$type = (FALSE === $position) ? "" : strtolower(substr($this->filename, $position + 1));
			if ( NULL != ($mime = @$this->ext2mime[$type]) ) {
				return $mime;
			} else {	//no mime type for such extension, using default
				return "application/octet-stream";
			}
		}
	}

	function getDisposition() {
		return "inline";
	}
}
?>