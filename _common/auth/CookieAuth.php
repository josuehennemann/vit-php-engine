<?
require_once("_common/auth/Auth.php");

/** An auth class using cookies to store user/pass data.
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.1 $ $Date: 2006/03/10 13:30:12 $ */
class CookieAuth extends Auth {
	function CookieAuth() {
	}

	function getUser() {
		$storedName = &$_COOKIE['uname'];
		$postName = @$_POST['uname'];
		if ( !empty($postName) ) {
			setcookie('uname', $postName);
		}
		return $storedName;
	}

	function getPass() {
		$storedPass = &$_COOKIE['pass'];
		$postPass = @$_POST['pass'];
		if ( !empty($postPass) ) {
			setcookie('pass', $postPass);
		}
		return $storedPass;
	}

	function askForCredentials() {
//var_dump($_COOKIE);
		$user = $this->getUser();
		$pass = $this->getPass();
		if ( empty($user) || empty($pass) ) {
			echo("Username and password are supposed to be passed using POST method and cannot be empty<br>");
			$this->denyAccess();
		}
	}
}
