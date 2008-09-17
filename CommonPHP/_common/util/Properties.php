<?
define("PROP_DELIMITERS", " :,=");

/** Java-like properties.
	@author Vitaly E. Lischenko
	@version 1.0 Jan 4, 2005
Below is the description from java docs adapted to be applicable for this class.
<p>
The Properties class represents a persistent set of properties. 
The Properties can be saved to a file or loaded from a file. 
Each key and its corresponding value in the property list is a string. 
<p>
A property list can contain another property list as its "defaults"; 
this second property list is searched if the property key is not found in the original property list.
*/
class Properties {
	var $propsArray;
	var $defaultPropsArray;

	/** Creates properties with specified properties for default values.
	 *	@param defaults <code>Properties</code> object containing default values.*/
	function Properties($defaults = NULL) {
		$this->propsArray = array();
		$this->defaultPropsArray = array();

		if (NULL != $defaults) {
			$this->defaultPropsArray = $defaults->propsArray;
		}
	}

	/** Loads properties from specified file.
	 *  Below is the description taken from java documentation adapted to be applicable for this method.
<p>
Reads a property list (key and element pairs) from the input file. 
Every property occupies one line of the input stream. 
Each line is terminated by a line terminator (\n or \r or \r\n). 
Lines from the input file are processed until end of file is reached.
A line that contains only blanks (trimmed by default by trim() function) 
or whose first non-whitespace character is an ASCII # or ! is ignored 
(thus, # or ! indicate comment lines). 
<p>
Every line other than a blank line or a comment line describes one property 
to be added to the table (except that if a line ends with \, then the following line, 
if it exists, is treated as a continuation line, as described below). 
The key consists of all the characters in the line starting with the first 
non-blank character and up to, but not including, the first ASCII =, :, or whitespace character
(see PROP_DELIMITERS constant). All of the key termination characters may be included in the key 
by preceding them with a \. Any blanks or PROP_DELIMITERS after the key is skipped; 
All remaining characters on the line become part of the associated element string. 
If the last character on the line is \, then the next line is treated as a continuation 
of the current line; the \ and line terminator are simply discarded, and any leading whitespace 
characters on the continuation line are also discarded and are not part of the element string. 
<p>
As an example, each of the following four lines specifies the key "Truth" and the associated element value "Beauty": 
<br>
<br>
 Truth = Beauty<br>
Truth:Beauty<br>
 Truth:Beauty<br>
<br> 
As another example, the following three lines specify a single property: <br>
<br>
<br>
 fruitsapple, banana, pear, \<br>
                                  cantaloupe, watermelon, \<br>
                                  kiwi, mango<br>
 <br>
The key is "fruits" and the associated element is: <br>
<br>
<br>
"apple, banana, pear, cantaloupe, watermelon, kiwi, mango"<br>
Note that a space appears before each \ so that a space will appear after each comma 
in the final result; the \, line terminator, and leading whitespace on the continuation 
line are merely discarded and are not replaced by one or more other characters. <br>
<br>
As a third example, the line: <br>
<br>
<br>
cheeses<br>
<br> 
specifies that the key is "cheeses" and the associated element is the empty string.
	 *	@param filename name of a file to load properties from.
	 * 	@return false if file open error occures. */
	function load($filename) {
		$fid = fopen($filename, "rb");
		if ( $fid === FALSE ) {
			return FALSE;
		}

		$line = '';	//accumulated line (may be constructed from number of multilines)
		while ( !feof($fid) ) {
			$currentLine = $this->readLineFrom($fid);
			$len = strlen($currentLine);

			//Check whether line is blank
			if ( $len <= 0 ) {
				continue;
			}

			//Prepare for appending to unfinished multiline, if any
			$totalLen = strlen($line);	//TODO: optimization possible (no need to call standard strlen(), can calculate itself)
			if ( $totalLen > 0 && "\\" == $line[$totalLen-1] ) {	//append to prev multiline (line finished with '\')
				$line = substr($line, 0, $totalLen-1);	//cut last '\' of the accumulated string
			} 

			//...and append current line to it
			$line .= substr($currentLine, 0, $len);

			//Check if last read line is not an unfinished multiline itself, and can be used for key-value parsing
			if ( "\\" != $currentLine[$len-1] ) {
				$this->processInputLine($line);
				$line = '';
			}
		}

		fclose($fid);
	}

	/** Saves properties to a file together with optional heading.
	*	@param filename name of a file to save properties to.
	*	@param heading optional heading to be put as comment in the beginning of the file. 
	 * 	@return false if file open error occures.*/
	function save($filename, $heading = NULL) {
		$fid = fopen($filename, "wb");
		if ( $fid === FALSE ) {
			return FALSE;
		}	

		if ( $heading != NULL ) {
			$headlines = explode("\r\n", $heading);
			foreach ($headlines as $line) {
				fputs($fid, "#$line\r\n");
			}
			fputs($fid, "\r\n");
		}
		$merged = array_merge($this->defaultPropsArray, $this->propsArray);
		foreach($merged as $k => $v) {
			fputs($fid, "$k=".addslashes($v)."\n");
		}

		fclose($fid);
	}

	function getProperty($k) {
		if ( array_key_exists($k, $this->propsArray) ) {
			return $this->propsArray[$k];
		} else {	//not set, so searching defaults then
			if ( array_key_exists($k, $this->defaultPropsArray) ) {
				return $this->defaultPropsArray[$k];
			} else {	//not set in defaults also, returning false
				return false;
			}
		}
	}

	function setProperty($k, $v) {
		$this->propsArray[$k] = $v;
	}

/*private*/
	function readLineFrom($fid) {
		$stats = fstat($fid);
		$size = $stats['size'];
		$line = fgets($fid, $size);

		if (false === $line || "" === $line) {
			return false;
		}
		$line = trim($line);
		return $line;
	}

/*private*/
	function processInputLine($line) {
		$len = strlen($line);

		if (0 == $len) {	//skip blanks
			return;
		}

		if ('#' == $line[0] || '!' == $line[0]) {	//skip comments
			return;
		}

		$line = stripslashes($line);

		//Extract key
		$kLen = strcspn($line, PROP_DELIMITERS);
		$k = substr($line, 0, $kLen);
		if (0 == $kLen) {
			die("Cannot find key in property: $line");
		}

		//Extract value
		$theRest = substr($line, $kLen);
		$v = ltrim($theRest, PROP_DELIMITERS);
		$v = trim($v);
		$this->propsArray[$k] = $v;
	}
}
?>