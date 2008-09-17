<?
require_once("DBConnection.php");
require_once("ResultSet.php");

/** This class represents MySQL DB connection derived from DBConnection.
*	@author Vitaly Lischenko
*	@version $Revision: 1.1 $ */
class MySQLConnection extends DBConnection {

	/** MySQL connection link identifier. */
	var $lnkId;
	var $lastInsertId;

	function MySQLConnection($server, $username, $password, $dbName, $connectionCharset="latin1") {
		parent::DBConnection($server, $username, $password, $dbName);
		$this->lnkId = mysql_connect($this->server, $this->username, $this->password)
			or die( "Cannot connect to MySQL server {$this->server}: ".mysql_error() );
		mysql_select_db($this->dbName, $this->lnkId) 
			or die ( "Can't select {$this->dbName}: ".mysql_error() );

		if (!empty($connectionCharset)) {
			mysql_set_charset($connectionCharset, $this->lnkId) or die("Problem setting charset:".mysql_error());
		}
		$this->lastInsertId = -1;

//		register_shutdown_function(array(&$this, "destroy"));
	}

	/** @param q mysql query
	*	@param doNotDie specifies whether to die on errors. */
	function query($q, $doNotDie = false) {
		$queryResult = mysql_query($q, $this->lnkId);

		//TODO: add transaction?..
		$id = mysql_insert_id($this->lnkId);
		if (0 != $id) {	//id was generated
			$this->lastInsertId = $id;
		}

		if (FALSE == $queryResult) {
			$errTxt = "Query error: ".mysql_error()." (During query: $q)<br>\n";
			if ($doNotDie) {
				echo $errTxt, "<br>";
				return;
			} else {
				die( $errTxt );
			}
		}
		if (TRUE === $queryResult) {	//successfully queried, but no result returned (e.g. for "INSERT", "CREATE ..." statement)
			return TRUE;
		} else {	//returning result
			$toRet = new ResultSet($queryResult);
			return $toRet;
		}

	}

	function getLastInsertId() {
		return $this->lastInsertId;
	}


	/** Emulates "source" command of mysql client utility: runs external file as mysql source. */
	function runSource($filename) {
		$sqlCommands = implode("", file($filename))	//for PHP < 4.3.0
		//$sqlCommands = file_get_contents($filename)
			or die("Cannot open sql source file: $filename");
		$splitted = array();
		$this->splitSqlFile($splitted, $sqlCommands);
		foreach($splitted as $query) {
			$this->query($query, true);
		}
	}

	function destroy() {
		mysql_close($lnkId);
	}

/* The function below is taken from http://martin.f2o.org/download/remote-sql/read_dump.php */
/* $Id: MySQLConnection.php,v 1.1 2006/03/10 13:30:12 vit Exp $ */
	/**
	* Removes comment lines and splits up large sql files into individual queries
	*
	* Last revision: September 23, 2001 - gandon
	*
	* @param   array    the splitted sql commands
	* @param   string   the sql commands
	* @param   integer  the MySQL release number (because certains php3 versions
	*                   can't get the value of a constant from within a function)
	*
	* @return  boolean  always true
	*
	* @access  public
	*/

	function splitSqlFile(&$ret, $sql, $release = 32349) {
	    $sql          = trim($sql);
	    $sql_len      = strlen($sql);
	    $char         = '';
	    $string_start = '';
	    $in_string    = FALSE;
	    $time0        = time();

	    for ($i = 0; $i < $sql_len; ++$i) {
	        $char = $sql[$i];

	        // We are in a string, check for not escaped end of strings except for
	        // backquotes that can't be escaped
	        if ($in_string) {
    	        for (;;) {
	                $i         = strpos($sql, $string_start, $i);
	                // No end of string found -> add the current substring to the
	                // returned array
	                if (!$i) {
	                    $ret[] = $sql;
	                    return TRUE;
	                }
	                // Backquotes or no backslashes before quotes: it's indeed the
	                // end of the string -> exit the loop
	                else if ($string_start == '`' || $sql[$i-1] != '\\') {
	                    $string_start      = '';
	                    $in_string         = FALSE;
	                    break;
	                }
    	            // one or more Backslashes before the presumed end of string...
	                else {
	                    // ... first checks for escaped backslashes
	                    $j                     = 2;
	                    $escaped_backslash     = FALSE;
	                    while ($i-$j > 0 && $sql[$i-$j] == '\\') {
	                        $escaped_backslash = !$escaped_backslash;
	                        $j++;
	                    }
    	                // ... if escaped backslashes: it's really the end of the
	                    // string -> exit the loop
    	                if ($escaped_backslash) {
        	                $string_start  = '';
	                        $in_string     = FALSE;
	                        break;
    	                }
	                    // ... else loop
	                    else {
	                        $i++;
	                    }
    	            } // end if...elseif...else
	            } // end for
	        } // end if (in string)

	        // We are not in a string, first check for delimiter...
	        else if ($char == ';') {
	            // if delimiter found, add the parsed part to the returned array
	            $ret[]      = substr($sql, 0, $i);
	            $sql        = ltrim(substr($sql, min($i + 1, $sql_len)));
	            $sql_len    = strlen($sql);
	            if ($sql_len) {
	                $i      = -1;
	            } else {
	                // The submited statement(s) end(s) here
	                return TRUE;
	            }
	        } // end else if (is delimiter)

	        // ... then check for start of a string,...
    	    else if (($char == '"') || ($char == '\'') || ($char == '`')) {
	            $in_string    = TRUE;
	            $string_start = $char;
	        } // end else if (is start of string)

	   	    // ... for start of a comment (and remove this comment if found)...
	        else if ($char == '#'
    	             || ($char == ' ' && $i > 1 && $sql[$i-2] . $sql[$i-1] == '--')) {
	            // starting position of the comment depends on the comment type
	            $start_of_comment = (($sql[$i] == '#') ? $i : $i-2);
	            // if no "\n" exits in the remaining string, checks for "\r"
    	        // (Mac eol style)
	            $end_of_comment   = (strpos(' ' . $sql, "\012", $i+2))
    	                          ? strpos(' ' . $sql, "\012", $i+2)
	                              : strpos(' ' . $sql, "\015", $i+2);
	   	        if (!$end_of_comment) {
    	            // no eol found after '#', add the parsed part to the returned
	       	        // array if required and exit
    	            if ($start_of_comment > 0) {
	                    $ret[]    = trim(substr($sql, 0, $start_of_comment));
    	            }
	                return TRUE;
    	        } else {
	                $sql          = substr($sql, 0, $start_of_comment)
    	                          . ltrim(substr($sql, $end_of_comment));
	                $sql_len      = strlen($sql);
    	            $i--;
	            } // end if...else
	        } // end else if (is comment)

	        // ... and finally disactivate the "/*!...*/" syntax if MySQL < 3.22.07
    	    else if ($release < 32270
	                 && ($char == '!' && $i > 1  && $sql[$i-2] . $sql[$i-1] == '/*')) {
	            $sql[$i] = ' ';
	        } // end else if

	        // loic1: send a fake header each 30 sec. to bypass browser timeout
	        $time1     = time();
	        if ($time1 >= $time0 + 30) {
	            $time0 = $time1;
	            header('X-pmaPing: Pong');
	        } // end if
	    } // end for

	    // add any rest to the returned array
	    if (!empty($sql) && ereg('[^[:space:]]+', $sql)) {
	        $ret[] = $sql;
	    }

	    return TRUE;
	} // end of the 'PMA_splitSqlFile()' function
}
?>