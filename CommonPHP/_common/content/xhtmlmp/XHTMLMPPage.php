<?
require_once("_common/content/html/includeAll.php");

/** This class represents XHTML-MP page with its specific structure (title, keywords, body, etc).
	@author Vitaly E. Lischenko
	@version $Revision: 1.1 $ $Date: 2006/08/10 02:25:12 $ */
class XHTMLMPPage extends Page {
	var $css;
	var $title;
	var $keywords;
	var $description;

	//main tags of the page are named here, so they can be accessed from outside.
	var $html;
	var $head;
	var $body;

	//these allows tweaking of standard parts (inners and attributes of main tags)
	var $htmlInners;
	var $headInners;
	var $bodyInners;

	var $htmlAttrs;
	var $headAttrs;
	var $bodyAttrs;

	var $charsetValue;
	
	var $code;	//cached code 

	function XHTMLMPPage() {		
		$this->code = false;	//invalidate code

		$this->head = new Tag("head");
		$this->body = new Tag("body");
   		$this->html = new Tag("html");

		$this->htmlInners = array();
		$this->headInners = array();
		$this->bodyInners = array();

		$this->htmlAttrs = array();
		$this->headAttrs = array();	
		$this->bodyAttrs = array();

		$this->css 			= $this->setCss("");
		$this->title 		= new Title();
		$this->description 	= new Meta();
		$this->keywords 	= new Meta();
		$this->bodyCont		= new MarkupCode();
		
		$this->charsetValue = "utf-8";
	}

	function setTitle($title) {
		$this->title = new Title($title);
		$this->code = false;	//invalidate code
	}

	function setKeywords($keywords) {
		$this->keywords = new Meta("keywords", $keywords);
		$this->code = false;	//invalidate code
	}

	function setDescription($description) {
		$this->description = new Meta("description", $description);
		$this->code = false;	//invalidate code
	}

	function setCSS($css) {
		$this->css = new CSS($css);
		$this->css->setForceSelfClose(true);	//Important! In XHTML link tag should be closed!
		$this->code = false;	//invalidate code
	}

	function setBody($body) {
		$this->bodyCont = new CustomCode($body);
		$this->code = false;	//invalidate code
	}
	
	function setCharset($newCharset) {
		$this->charsetValue = $newCharset;
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
		$this->head = new Tag("head");
		$this->head->addInner($this->description);
		$this->head->addInner($this->keywords);
		$this->head->addInner($this->css);
		$this->head->addInner($this->title);
		foreach($this->headAttrs as $k=>$v) {
			$this->head->setAttribute($k, $v);
		}
		foreach($this->headInners as $k=>$v) {
			$this->head->addInner($v);
		}

		$this->body = new Tag("body");
		$this->body->addInner($this->bodyCont);
		foreach($this->bodyAttrs as $k=>$v) {
			$this->body->setAttribute($k, $v);
		}
		foreach($this->bodyInners as $k=>$v) {
			$this->body->addInner($v);
		}
	
   		$this->html = new Tag("html");
   		$this->html->setAttribute("xmlns","http://www.w3.org/1999/xhtml");
		$this->html->addInner($this->head);
		$this->html->addInner($this->body);
		foreach($this->htmlAttrs as $k=>$v) {
			$this->html->setAttribute($k, $v);
		}
		foreach($this->htmlInners as $k=>$v) {
			$this->html->addInner($v);
		}

		$this->code = new MarkupCode();
		$this->code->addContents(new CustomCode("<?xml version=\"1.0\" encoding=\"".$this->charsetValue."\"?>\r\n<!DOCTYPE html PUBLIC \"-//OMA//DTD XHTML Mobile 1.0//EN\"\n\"http://www.openmobilealliance.org/tech/DTD/xhtml-mobile10.dtd\">\r\n"));
		$this->code->addContents($this->html);
	}
}
?>