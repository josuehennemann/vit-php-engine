<?
require_once("_common/auth/Auth.php");

/** An auth class using sessions to store user/pass data.
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.1 $ $Date: 2006/03/10 13:30:12 $ */
class SessionAuth extends Auth {
	function SessionAuth() {
		session_start();
	}

	function getUser() {
		$storedName = &$_SESSION['uname'];
		$postName = @$_POST['uname'];
		if ( !empty($postName) ) {
			$storedName = $postName;
		}

		return $storedName;
	}

	function getPass() {
		$storedPass = &$_SESSION['pass'];
		$postPass = @$_POST['pass'];
		if ( !empty($postPass) ) {
			$storedPass = $postPass;
		}
		return $storedPass;
	}

	function askForCredentials() {
		$user = $this->getUser();
		$pass = $this->getPass();
		if ( empty($user) || empty($pass) ) {
			echo("Username and password are supposed to be passed using POST method and cannot be empty<br>");
			$this->denyAccess();
		}
	}

	function clearCredentials() {
		unset($_SESSION['uname']);
		unset($_SESSION['pass']);
		$this->denyAccess();
	}
}
