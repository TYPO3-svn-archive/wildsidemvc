<?php
class tx_wildsidemvc_formathelper {

	/**
	 * Formats the dateobject as the string dd-mm-yyyy
	 * @return
	 * @param $date Object
	 */
	public function formatDate($date) {
		if ($date > 0)
			return strftime ( '%d-%m-%y', $date );
		return '';
	}

	/**
	 * Formats the dateobject as the string yyyy-mm-dd (sql ready)
	 * @param <type> $date
	 * @return <type>
	 */
	public function formatSqlDate($date) {
		if ($date > 0)
			return strftime ( '%y-%m-%d', $date );
		return '';
	}

	/**
	 * Formats the dateobject as the string hh:mm (24-hour clock)
	 * @return
	 * @param $date Object
	 */
	public function formatTime($date) {
		if ($date > 0)
			return strftime ( '%H:%M', $date );
		return '';
	}

	/**
	 * Returns the day from a dateobject
	 * @return
	 */
	public function getDay() {
		if ($date > 0)
			return strftime ( '%d', $date );
		return '';
	}

	/**
	 * Returns the month of a dateobject
	 * @return
	 */
	public function getMonth() {
		if ($date > 0)
			return strftime ( '%m', $date );
		return '';
	}

	/**
	 * Returns the year of a dateobject
	 * @return
	 */
	public function getYear() {
		if ($date > 0)
			return strftime ( '%y', $date );
		return '';
	}

	/**
	 * Returns the hour of a dateobject
	 * @return
	 * @param $date Object
	 */
	public function getHour($date) {
		if ($date > 0)
			return strftime ( '%H', $date );
		return '';
	}

	/**
	 * Returns the minutes of a dateobject
	 * @return
	 * @param $date Object
	 */
	public function getMinute($date) {
		if ($date > 0)
			return strftime ( '%M', $date );
		return '';
	}

	/**
	 * Returns current timestamp
	 * @return <type>
	 */
	public function now() {
		return mktime ();
	}

	/**
	 * Returns only the first x number of words.
	 * @param <string> $string The string to be limited
	 * @param <int> $word_limit The limit - aka number of words.
	 * @return <type>
	 */
	public function limitWords($string, $word_limit) {
		$words = explode ( ' ', $string, ($word_limit + 1) );
		array_pop ( $words );
		return implode ( ' ', $words );
	}

	public function getSelect($rows, $idField, $valueField, $activeId, $onchange="", $id="", $name="") {
		$rval = '<select id="'.$id.'" name="'.$name.'" onchange="'.$onchange.'">';
		$rval .= '<option value="0"></option>';
        foreach($rows as $row) {
          if(!is_array($row)) {
          	break;
          }
       	  if($row[$idField]==$activeId) {
              $selected = 'selected="selected"';
          }
          else {
              $selected = '';
          }
          $rval .= '<option value="'.$row[$idField].'" '.$selected.' >'.$row[$valueField].'</option>';
        }
        $rval .= '</select>';
        return $rval;
	}

}
?>
