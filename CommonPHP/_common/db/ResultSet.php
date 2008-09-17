<?
/** This class represents query result. It's supposed to be constructed in DBConnection::query().
*	@author Vitaly Lischenko
*	@version $Revision: 1.1 $ */
class ResultSet {
	var $resId;
	var $rowsNum;
	var $fieldsNum;
	var $currentRow;
	var $currentFieldIndex;

	function ResultSet($resultId) {
		$this->resId = $resultId;
		$this->rowsNum = mysql_num_rows($this->resId);
		$this->fieldsNum = mysql_num_fields($this->resId);
	}

	/** @return number of rows in this result set. */
	function getNumRows() {
		return $this->rowsNum;
	}

	/** @return number of fields in this result set. */
	function getNumFields() {
		return $this->FieldsNum;
	}

	/** Moves internal pointer to the next row. You can use it like this:
	*	<code>
	*	while ( $rs.nextRow() ) {
	*		//process row here
	*	}
	*	</code>
	*	@return <code>FALSE</code> if no more rows available. */
	function nextRow() {
		$this->currentRow = mysql_fetch_array($this->resId, MYSQL_BOTH);
		if ($this->currentRow === FALSE) {
			return FALSE;
		}
		$this->currentFieldIndex = 0;
		return TRUE;
	}

	/** @return current row as a fields array (both indexed and associative, so
	*	you can use both <code>field[intIndex]</code> and <code>field["stringName"]</code>).
	*	@see ResultSet#nextField() as an alternative (iterating over fields).*/
	function getCurrentRow() {
		return $this->currentRow;
	}

	/** Moves internal pointer to the next field of a current row. 
	*	You can use it like this:
	*	<code>
	*	while ( rs.nextRow() ) {
	*		while ( ($field = rs.nextField()) !== FALSE ) {
	*			//process $field here
	*		}
	*	}
	*	</code>
	*	@return value of a field or <code>FALSE</code> if no more fields available in current row. */
	function nextField() {
		if ( $this->currentFieldIndex >= $this->fieldsNum ) {
			return FALSE;
		}
		return $this->currentRow[$this->currentFieldIndex++];
	}

	/** Gets field value.
	*	@param $index offset index (or associated name) of the field in current row to get value from.
	*	@return field value (from the current row) corresponding to specified index.*/
	function get($index) {
		return $this->currentRow[$index];
	}

	/**  Frees result memory.
	*	 @return TRUE on success or FALSE on failure.*/
	function free() {
		 return mysql_free_result($this->resId);
	}

	/**  Resets the result set.*/
	function reset() {
		mysql_field_seek($this->resId, 0);
		mysql_data_seek($this->resId, 0);
	}
	
	/** Helper method dumping result set contents to HTML table.
	*	<p>NOTE: dump starts from the current row, so it's supposed to be called just after
	*	construction (iteration over the rows or fields will affect output). */
	function toHTML() {
		echo("<table>");
		//TODO: output field names to <TH> here?..
		while ( $this->nextRow() ) {
			echo("\t<tr>");
			while ( $f = $this->nextField() ) {
				echo("\t\t<td>$f</td>");
			}
			echo("\t</tr>");
		}
		echo("</table>");
	}
}
?>