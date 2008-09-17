<?
require_once("_common/content/ContentDispatcher.php");

/** An abstract authorizing/authenticating system.
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.1 $ $Date: 2006/03/10 13:30:12 $ */
class Auth {
/* virtual protected */
	/** This method is responsible for asking credentials from user, such as popping up dialog window etc. */
	function askForCredentials() {
		die("Should be implemented in class derived from Auth");
	}

/* virtual protected */
	/** This method is used to specify whether authorization required for current page (all
	*	requires information is supposed to be taken from context, e.g. a page URI).
	*	If this method returns <code>FALSE</code> the page is considered to be auth-free.
	*	Default implementation is to always return <code>TRUE</code>.
	*	Derived classes can override this method to make some of the pages free of auth.
	*	@return whether the current page requires authentiaction. */
	function isAuthRequired() {
		return true;
	}

/* virtual protected */
	/** This method is responsible for deciding whether to allow access to the page for some
	*	user providing credentials.
	*	Derived classes MUST override this to implement their specific checks (such as 
	*	quering DB or checking /etc/password).
	*	If this method returns <code>TRUE</code>, access is granted; otherwise the access is denied. */
	function checkCredentials($user, $pass) {
		die("Should be implemented in class derived from Auth");
	}

/* virtual protected */
	/** This method is responsible for clearing credentials (for example, during logout operation).*/
	function clearCredentials() {
		die("Should be implemented in class derived from Auth");
	}

/* virtual protected */
	/** This method is responsible for dening access. It's safe 
	*	(but is not obligatory) to <code>die()</code> in this method.
	*	Default implementation is just to die with an 'Access denied' message. */
	function denyAccess() {
		die("Access denied");
	}

/* virtual protected */
	/** @return username string */
	function getUser() {
		die("Should be implemented in class derived from Auth");	
	}

/* virtual protected */
	/** @return password string */
	function getPass() {
		die("Should be implemented in class derived from Auth");
	}
}