<?
require_once("_common/auth/Auth.php");

/** An auth class using HTTP's "WWW-Authenticate:: Basic" method.
*	@author Vitaly E. Lischenko
*	@version $Revision $ $Date: 2006/03/10 13:30:12 $ */
class BasicAuth extends Auth {

	var $realm;

	var $isLogout;

	function BasicAuth($realm = "protected area") {
		$this->realm = $realm;
		$this->isLogout = false;
	}

	function askForCredentials() {
		header("WWW-Authenticate: Basic realm=\"".$this->realm."\"");
		header("HTTP/1.0 401 Unauthorized");
		$this->denyAccess();
	}

	function checkCredentials($user, $pass) {
		die("Not implemented (you should override checkCredentials() in class derived from BasicAuth)");
	}

	function getUser() {
		return @$_SERVER['PHP_AUTH_USER'];
	}

	function getPass() {
		return @$_SERVER['PHP_AUTH_PW'];
	}

	function clearCredentials() {
		header("HTTP/1.0 401 Unauthorized");
		$this->askForCredentials();
	}
}