<?
require_once("includeAll.php");

/** This class represents WML deck with its specific structure: cards etc.
	@author Vitaly E. Lischenko
	@version $Revision: 1.1 $ $Date: 2006/08/10 02:25:20 $ */
class WMLDeck {
	//main tag of the page is named here, so it can be accessed from outside.
	var $wml;
	var $head;
		
	//these allows tweaking of standard parts (inners and attributes of main tags)
	var $headInners;
	
	//keeps array of WMLCard objects
	var $cards;

	//cached code
	var $code;

	function WMLDeck() {		
		$this->code = false;	//invalidate code

   		$this->wml = new Tag("wml");
   		
   		$this->head = new Tag("head");
   		$this->head->setForceSelfClose(TRUE);
   		
   		$this->headInners = array();
   		
   		$this->cards = array();		
	}

	function setCards($cards) {
		$this->cards = $cards;
		$this->code = false;	//invalidate code
	}
	
	function clearCards() {
		$this->cards = array();
		$this->code = false;	//invalidate code
	}
	
	function addCard($card) {
		array_push($this->cards, $card);
		$this->code = false;	//invalidate code		
	}

	function render() {
		if (false === $this->code) {
			$this->rebuildCode();
		}
		return $this->code->render();
	}

/* private */
	/** Internal method used to (re)generate page code. */
	function rebuildCode() {
		$this->code = new WMLCode();

		$this->code->addContents(new CustomCode("<?xml version=\"1.0\" encoding=\"UTF-8\"?><!DOCTYPE wml PUBLIC \"-//WAPFORUM//DTD WML 1.1//EN\" \"http://www.wapforum.org/DTD/wml_1.1.xml\">"));
		
		$this->wml = new Tag("wml");
		
		if (0 != count($this->headInners)) {
			foreach($this->headInners as $k=>$v) {
				$this->head->addInner($v);
			}
			$this->wml->addInner($this->head);
		}
						
		foreach($this->cards as $card) {
			$this->wml->addInner($card);
		}
		
		$this->code->addContents($this->wml);
	}
}
?>