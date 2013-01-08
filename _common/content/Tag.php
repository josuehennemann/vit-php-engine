<?
require_once("MarkupCode.php");

/** Abstract tag implementation. 
	@author Vitaly E. Lischenko
	@version $Revision: 1.2 $ $Date: 2006/08/10 02:01:30 $ */
class Tag extends MarkupCode {
	var $name;
	var $inner;
	var $isClosingRequired;
	var $attributes;
	/** determines whether single tag without inners should be closed. */
	var $forceSelfClose;
	
	/** Constructs tag using specified name (for example, "body"") and optional inner contents.*/
	function Tag($name, $inner = NULL) {
		$this->name = $name;
		$this->inner = array();

		$this->addInner($inner);
		$this->contents = false;
		$this->attributes = array();
		
		$this->forceSelfClose = false;
	}

	function addInner($inner) {
		if (NULL != $inner) {
			array_push($this->inner, $inner);			
		}
		$this->isClosingRequired = (NULL != $inner);
		$this->contents = false;	//ivalidate contents
	}

	/** Sets specified attribute to specified value, replacing old value, if any. */
	function setAttribute($attName, $attValue) {
		$this->attributes[$attName] = $attValue;
		$this->contents = false;	//ivalidate contents
	}

	/** Sets whether single tag without inners should be closed. */
	function setForceSelfClose($fc) {
		$this->forceSelfClose = $fc;
	}
	
	/** Renders this object to code. */
	function render() {
		if ( false === $this->contents ) {
			$this->rebuildContents();
		}
		return $this->contents;
	}

/* private */
	/** Internal method used to (re)generate tag code. */
	function rebuildContents() {
		$this->contents = "<";
		$this->contents.= $this->name;
		foreach ($this->attributes as $k=>$v) {
//echo $this->name.": $k=$v<br>";
			$this->contents .= " $k=\"$v\"";
		}
		if ( $this->forceSelfClose && !$this->isClosingRequired && 0 == count($this->inner) ) {
			$this->contents .= "/";
		}
		$this->contents .= ">";
		if ($this->isClosingRequired) {
			foreach($this->inner as $inner) {
				$this->contents .= $inner->render();
			}
			$this->contents .= "</";
			$this->contents.= $this->name;
			$this->contents .= ">";
		}		
	}
}
?>