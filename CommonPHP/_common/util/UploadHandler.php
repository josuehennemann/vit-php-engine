<?
/** This is uploaded file handler.
	@author Vitaly E. Lischenko
	@version $Revision: 1.1 $ $Date: 2006/03/10 13:30:12 $*/
class UploadHandler {
	var $upl;

	function UploadHandler() {
		$upl = null;
	}

	/** @param name name of file provided in <input type='file'> of the uploading form. */
	function handleFile($name) {
		//put uploaded file info into $upl variable
		$this->upl = $_FILES[$name];

		if ( NULL == $this->upl ) {
			die("file $name is null or not defined in FILES variable");
		}
		
		//Check for possible upload errors
		if ( !is_uploaded_file($this->getUploadedTmpName()) ) {
/** //requires PHP >= 4.2.0
			switch($upl['error']) {
				case 0: //no error; possible file attack!
					echo "There was a problem with your upload, possible file attack.";
			     	break;
				case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
					echo "The file you are trying to upload is too big.";
					break;
				case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
					echo "The file you are trying to upload is too big.";
					break;
				case 3: //uploaded file was only partially uploaded
					echo "The file you are trying upload was only partially uploaded.";
					break;
				case 4: //no file was uploaded
					echo "You must select an image for upload.";
					break;
				default: //a default error, just in case!  :)
				echo "There was a problem with your upload.";
				break;
			}
*/
			echo "There was a problem with your upload.";
			die("Exiting!");
		}

		//Perform uploaded file's parameters checks
		if (!$this->isAcceptableName($name = $this->getUploadedName())) {
			die("This filename/extension ($name) is not accepted");
		}
		if (!$this->isAcceptableType($type = $this->getUploadedType())) {
			die("This mime type ($type) is not accepted");
		}
		if (!$this->isAcceptableSize($size = $this->getUploadedSize())) {
			die("This file is not accepted because of its size ($size)");
		}
	}

	/** Moves uploaded file to specified destination. 
	*	@return <code>ture</code> on success, <code>false</code> otherwise. */
	function moveUploadedFile($destination) {
		$source = $this->getUploadedTmpName();
		$toRet = move_uploaded_file($source, $destination);

		if ($toRet) {
			$this->upl['tmp_name'] = $destination;
		}
		return $toRet;
	}

	/** @return original name of uploaded file. */
	function getUploadedName() {
		return $this->upl['name'];
	}

	/** @return mime type of uploaded file. */
	function getUploadedType() {
		return $this->upl['type'];
	}

	/** @return size (in bytes) of uploaded file. */
	function getUploadedSize() {
		return $this->upl['size'];
	}

	/** @return temporary filename of the file in which the uploaded file was 
	*	stored on the server.*/
	function getUploadedTmpName() {
		return $this->upl['tmp_name'];
	}


/*virtual protected*/
	/** This method determines (based on original file name) whether file 
	*	should be accepted. This may, for instance, include extension filters
	*	or something else. 
	*	If this method returns <code>FALSE</code>, the file is rejected.
	*	The default implementation is to always return <code>TRUE</code>.
	*	Derived classes may override this method to implement their specific 
	*	filtering.*/
	function isAcceptableName($name) {
		return true;
	}

/*virtual protected*/
	/** This method determines (based on mime-type of file) whether file 
	*	should be accepted.
	*	If this method returns <code>FALSE</code>, the file is rejected.
	*	The default implementation is to always return <code>TRUE</code>.
	*	Derived classes may override this method to implement their specific 
	*	filtering. */
	function isAcceptableType($name) {
		return true;
	}

/*virtual protected*/
	/** This method determines (based on size of file) whether file 
	*	should be accepted.
	*	If this method returns <code>FALSE</code>, the file is rejected.
	*	The default implementation is to always return <code>TRUE</code>.
	*	Derived classes may override this method to implement their specific 
	*	filtering. */
	function isAcceptableSize($size) {
		return true;
	}

}
?>