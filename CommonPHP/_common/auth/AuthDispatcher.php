<?
require_once("_common/content/ContentDispatcher.php");

/** An auth dispatcher capable of using some auth object to perform auth(-enication/-orization).
*	Implemented as a decorator over ContentDispatcher.
*	@author Vitaly E. Lischenko
*	@version $Revision $ $Date: 2006/03/10 13:30:12 $ */
class AuthDispatcher extends ContentDispatcher {
	var $wrappedDispatcher;
	var $authObject;

	/** @param wrappedDipsatcher - a dispatcher object to be provided with authorizing capability. 
	*	@param authObject - object to be used to perform auth operations. */
	function AuthDispatcher(&$wrappedDispatcher, &$authObject) {
		$this->wrappedDispatcher = &$wrappedDispatcher;
		$this->authObject = &$authObject ;
	}

	/** Overrides <code>renderPage()</code> to provide authorizing. */
	function renderPage() {
		if (!$this->authObject->isAuthRequired()) {
			//just render page and return
			$this->wrappedDispatcher->renderPage();
			return;
		} else {	//perform auth checking
			$user = $this->authObject->getUser();
			$pass = $this->authObject->getPass();

			if ( empty($user) || empty($pass) ) { //seems to be first time check, forcing check
				$this->authObject->askForCredentials();
			} else {
				if ($this->authObject->checkCredentials($user, $pass)) {
					$this->wrappedDispatcher->renderPage();
					return;
				} else {
					$this->authObject->clearCredentials();
					$this->renderPage();
				}
			}
		}			
	}
}
