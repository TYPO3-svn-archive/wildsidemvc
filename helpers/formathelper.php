<?php
class tx_ambpmservices_formathelper {
	
	/**
	 * Formats the dateobject as the string dd-mm-yyyy
	 * @return 
	 * @param $date Object
	 */	
	function formatDate($date) {
		if($date>0)
			return strftime('%d-%m-%y', $date);
		return '';
	}
	
	/**
	 * Formats the dateobject as the string hh:mm (24-hour clock) 
	 * @return 
	 * @param $date Object
	 */
	function formatTime($date) {
		if($date>0)
			return strftime('%H:%M', $date);
		return '';
	}
	
	/**
	 * Returns the day from a dateobject
	 * @return 
	 */
	function getDay() {		
		if($date>0)
			return strftime('%d', $date);
		return '';
	}
	
	/**
	 * Returns the month of a dateobject
	 * @return 
	 */
	function getMonth() {
		if($date>0)
			return strftime('%m', $date);
		return '';		
	}
	
	/**
	 * Returns the year of a dateobject
	 * @return 
	 */
	function getYear() {
		if($date>0)
			return strftime('%y', $date);
		return '';		
	}
	
	/**
	 * Returns the hour of a dateobject
	 * @return 
	 * @param $date Object
	 */
	function getHour($date) {
		if($date>0)
			return strftime('%H', $date);
		return '';	
	}
	
	/**
	 * Returns the minutes of a dateobject
	 * @return 
	 * @param $date Object
	 */
	function getMinute($date) {
		if($date>0)
			return strftime('%M', $date);
		return '';	
	}
}
?>
