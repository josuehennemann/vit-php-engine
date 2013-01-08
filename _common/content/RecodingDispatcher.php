<?
require_once("_common/content/ContentDispatcher.php");

/** A decorator around another dispatcher providing recoding capability.
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.1 $ $Date: 2006/08/10 02:27:54 $ */
class RecodingDispatcher extends ContentDispatcher {
	var $wrappedDispatcher;

//	var $srcEncoding;
//	var $dstEncoding;
	
	/** @param wrappedDipsatcher - a dispatcher object to be provided with recoding capability. */
	function RecodingDispatcher(&$wrappedDispatcher) {
		$this->wrappedDispatcher = &$wrappedDispatcher;
	}

	/** Sets source encoding. */
//	function setSourceEncoding($enc) {
//		$this->srcEncoding = $enc;
//	}
	
//	/** Sets destination encoding. */
//	function setDestinationEncoding($enc) {
//		$this->dstEncoding = $enc;
//	}

	/** Overrides <code>renderPage()</code> to provide recoding. */
	function renderPage() {
		ob_start();

		$this->wrappedDispatcher->renderPage();	

		$pageContents = ob_get_contents();	//page contents to be recoded
		ob_end_clean();
				
		echo $this->recode($pageContents);
	}

	/** Recodes cp1251 text to utf8 entities. */
	function recode($s) {
		$t = "";
		
		//below code is from php manual		
		for($i=0, $m=strlen($s); $i<$m; $i++) {
		$c=ord($s[$i]);
		if ($c<=127) {$t.=chr($c); continue; }
		if ($c>=192 && $c<=207) {$t.=chr(208).chr($c-48); continue; }
		if ($c>=208 && $c<=239) {$t.=chr(208).chr($c-48); continue; }
		if ($c>=240 && $c<=255) {$t.=chr(209).chr($c-112); continue; }
		if ($c==184) { $t.=chr(209).chr(209); continue; };
		   if ($c==168) { $t.=chr(208).chr(129);  continue; };
		}
		$str = $t;

		return $this->numeric_entify_utf8($str);
	}
	
	/** Function from PHP manual.
	 * 
	 * UTF-8 encoding
	 * bytes bits representation
	 * 1   7  0bbbbbbb
	 * 2  11  110bbbbb 10bbbbbb
	 * 3  16  1110bbbb 10bbbbbb 10bbbbbb
	 * 4  21  11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
	 * Each b represents a bit that can be used to store character data.
	 * input CANNOT have single byte upper half extended ascii codes.*/
	function numeric_entify_utf8 ($utf8_string) {
		$out = "";
		$ns = strlen ($utf8_string);
		for ($nn = 0; $nn < $ns; $nn++) {
	  		$ch = $utf8_string [$nn];
			$ii = ord ($ch);
			//1 7 0bbbbbbb (127)
	   		if ($ii < 128) $out .= $ch;
				//2 11 110bbbbb 10bbbbbb (2047)
	  		else if ($ii >>5 == 6) {
				$b1 = ($ii & 31);
				$nn++;
				$ch = $utf8_string [$nn];
				$ii = ord ($ch);
				$b2 = ($ii & 63);
				$ii = ($b1 * 64) + $b2;
				$ent = sprintf ("&#%d;", $ii);
				$out .= $ent;
			}
			//3 16 1110bbbb 10bbbbbb 10bbbbbb
	  		else if ($ii >>4 == 14) {
	 			$b1 = ($ii & 31);
				$nn++;
				$ch = $utf8_string [$nn];
				$ii = ord ($ch);
				$b2 = ($ii & 63);
				$nn++;
				$ch = $utf8_string [$nn];
				$ii = ord ($ch);
				$b3 = ($ii & 63);
				$ii = ((($b1 * 64) + $b2) * 64) + $b3;
				$ent = sprintf ("&#%d;", $ii);
				$out .= $ent;
			}
			//4 21 11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
			else if ($ii >>3 == 30) {
				$b1 = ($ii & 31);
				$nn++;
				$ch = $utf8_string [$nn];
				$ii = ord ($ch);
				$b2 = ($ii & 63);
				$nn++;
				$ch = $utf8_string [$nn];
				$ii = ord ($ch);
				$b3 = ($ii & 63);
				$nn++;
				$ch = $utf8_string [$nn];
				$ii = ord ($ch);
				$b4 = ($ii & 63);
				$ii = ((((($b1 * 64) + $b2) * 64) + $b3) * 64) + $b4;
				$ent = sprintf ("&#%d;", $ii);
				$out .= $ent;
	   		}
		}
		return $out;
	}
	
}
?>