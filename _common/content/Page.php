<?
/** This class represents abstract page.
	@author Vitaly E. Lischenko
	@version $Revision: 1.2 $ $Date: 2006/08/10 02:01:30 $ */
class Page {

	var $code;	//cached code 

	function Page() {		
		$this->code = false;	//invalidate code
	}

	function render() {
		if (false === $this->code) {
			$this->rebuildCode();
		}
		return $this->code->render();
	}

/* protected */
	/** Internal method used to (re)generate page code. */
	function rebuildCode() {
		$this->code = new MarkupCode();
	}
}
?>