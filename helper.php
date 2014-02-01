<?php
defined('_JEXEC') or die('Restricted Access');

class modReports2Oraculum {

	public function getNext() {
		$avg = self::executeQuery(self::$qMeanTime) * 60;	// value in seconds
		// can be NULL if MIN(date1) is 0000-00-00
		if ($avg == 0) return false;
		$last = self::executeQuery(self::$qLatestTimestamp);	// timestamp format: YYYY-MM-DD HH:MM:SS
		$next = strtotime($last.' + '.$avg.' seconds');	// unix timestamp in integer
		while ($next < time()) {
			$next = $next + $avg;
		}
		return $next;
	}

	// All the SQL queries
	private static $qMeanTime = '	SELECT
  										CASE
											WHEN COUNT(date1) < 2 THEN 0
											ELSE ROUND(
												TIMESTAMPDIFF(
											    MINUTE,
    											MIN(date1), 
												MAX(date1)) / (COUNT(date1)-1))
										END
										as mean_time
									FROM #__reports		';

	private static $qLatestTimestamp = 'SELECT MAX(date1) FROM #__reports';

/*	public function getNextType($next) {
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
*/
	private function executeQuery($query) {
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$result = $db->loadResult();		
		return $result;
	}
}
