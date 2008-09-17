<?
require_once("_common/util/Properties.php");

/** Manages number of independant configurations sections, allowing to read/write their properties.
*	@author Vitaly E. Lischenko
*	@version $Revision: 1.1 $ $Date: 2006/03/10 13:30:12 $ */
class ConfigManager {
	/** An array of configuration sections (<code>Section</code> objects).
	*	Each section is stored in a separate file and is read/written independantly.
	*	Sections array is supposed to be filled by derived classes. */
	var $sections;

	/** Buffers the properties for sections already read, so the same property file is not read*/
	var $sectionProperties;

	function ConfigManager() {
		$this->sections = array();
	}

	/** Gets property of a section.
	*	@param $name name of a section to get properies for.
	*	@returns section property corresponding to specified name, 
	*		or <code>FALSE</code> if there's no section with such name. */
	function getSectionProperties($name) {
		$sect = $this->sections[$name];
		if (NULL == $sect) {
			return FALSE;
		}
		return $sect->getProperty();
	}

	/** Sets section property to specified Properties object. */
	function setSectionProperty($name, $prop) {
		$sect = $this->sections[$name];
		if (NULL == $sect) {
			return FALSE;
		}
		return $sect->save($prop);
	}

	/** @return array of names for available sections. */
	function getSectionNames() {
		return array_keys($this->sections);
	}
}

class Section {
	/** Name of the file assigned to this section (to store config values in). */
	var $filename;

	/** Properties object for this section if the sectin file was already read, <code>NULL</code> otherwise.*/
	var $prop;

	function Section($filename) {
		$this->filename = $filename;
		$this->prop = NULL;
	}

	function &getProperty() {
		if (NULL == $this->prop) {
			$this->load();
		}
		return $this->prop;
	}

	
	/** protected */
	function load() {
		$this->prop = new Properties();
		$res = $this->prop->load($this->filename);
		if ( FALSE === $res ) {
			echo("Error loading section from file: ".$this->filename);
		}
		return $res;
	}

	/** Forces saving of properties to assigned file, optianally overriding the values data.
	*	@param $newValues contains <code>Properties</code> object holding new data.*/
	function save(&$newValues) {
		if (NULL != $newValues) {
			$this->prop = &$newValues;
		}
		$res = $this->prop->save($this->filename);
		if ( FALSE === $res ) {
			echo("Error saving section to file: ".$this->filename);
		}
		return $res;
	}
}
?>