<?php
defined('_JEXEC') or die('Restricted Access');

class modEinsatzStats {

	public static function getNext() {
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

	public static function getStatsByType() {
		$year = date('Y');
		//$year = 2013;
		$qCountByType =
			'SELECT data1, count(data1) AS count
			FROM #__eiko_einsatzberichte
			WHERE state=1 AND date1 LIKE \''.$year.'%\'
			GROUP BY data1;';
			//TODO: Join with einsatzarten table for colors
		return self::executeQuery($qCountByType, 1);
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
									FROM #__eiko_einsatzberichte	';

	private static $qLatestTimestamp = 'SELECT MAX(date1) FROM #__eiko_einsatzberichte';

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
	private static function executeQuery($query, $returnArray=0) {
		$db = JFactory::getDBO();
		$db->setQuery($query);
		if ($returnArray)
			$result = $db->loadObjectList();
		else
			$result = $db->loadResult();
		return $result;
	}
}
