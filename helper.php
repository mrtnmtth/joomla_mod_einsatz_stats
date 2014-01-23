<?php
defined('_JEXEC') or die('Restricted Access');

class modReports2Oraculum {
	
	public function getNext() {
		$avg = self::getOverallAvgTime() * 60;	// value in seconds
		$last = self::getLatestTimestamp();	// timestamp
		$next = strtotime($last.' + '.$avg.' minutes');	// unix timestamp in integer
		//TODO: return error if avg = NULL or 0
		while ($next < time()) {
			$next = $next + $avg;
		}
		return $next;
	}
	
	public function getNextType($next) {
		$query = 'SELECT MAX(id) FROM #__reports_data';
		$j = self::query($query);
		$results = array();
		for ($i = 0; $i < $j; $i++) {
			$query = 'SELECT
  				CASE
					WHEN COUNT(date1) < 2 THEN 0
					ELSE ROUND(
						TIMESTAMPDIFF(
					    MINUTE,
    					MIN(date1), 
						MAX(date1)) / (COUNT(date1)-1))
				END
				as mean_time
			FROM #__reports WHERE data1 = (SELECT title FROM #__reports_data WHERE id = '.$i.')';
			$results[$i] = self::query($query);
		}
		return $results;
	}
	
	private function getOverallAvgTime() {
		$db = JFactory::getDBO();
		
		// returns average time between each report in minutes
		$query = 'SELECT
  				CASE
					WHEN COUNT(date1) < 2 THEN 0
					ELSE ROUND(
						TIMESTAMPDIFF(
					    MINUTE,
    					MIN(date1), 
						MAX(date1)) / (COUNT(date1)-1))
				END
				as mean_time
			FROM #__reports';
		$db->setQuery($query);
		$result = $db->loadResult();
		
		return $result;
	}
	
	private function getLatestTimestamp() {
		$db = JFactory::getDBO();
		
		$query = 'SELECT MAX(date1) FROM #__reports';
		
		$db->setQuery($query);
		$result = $db->loadResult();
		
		return $result;
	}
	
	private function query($query) {
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$result = $db->loadResult();		
		return $result;
	}
}
