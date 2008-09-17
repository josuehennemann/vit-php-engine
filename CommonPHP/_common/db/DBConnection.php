<?
/** This class represents abstract DB connection.
*	@author Vitaly Lischenko
*	@version $Revision: 1.1 $ */
class DBConnection {
	var $server;
	var $username;
	var $password;
	var $dbName;

	function DBConnection($server, $username, $password, $dbName) {
		$this->server 	= $server;
		$this->username = $username;
		$this->password = $password;
		$this->dbName 	= $dbName;
	}

	function query($q, $doNotDie = false) {
		die("Abstract function called (overriden version defined in derived class should be called instead)");
	}

}
?>