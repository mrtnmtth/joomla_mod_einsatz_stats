<?php
defined('_JEXEC') or die('Restricted Access');

class modEinsatzStatsHelper {

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

	public static function getStatsByType($year) {
		$latest = substr(self::executeQuery(self::$qLatestTimestamp), 0, 4);
		if (($year > $latest) or !(ctype_digit($year)))
			$year = $latest;

		$db = JFactory::getDbo();
		$query =
			'SELECT arten.title AS label,
				count(data1) AS value,
				arten.marker AS color
			FROM #__eiko_einsatzberichte AS berichte
			INNER JOIN #__eiko_einsatzarten AS arten
			ON berichte.data1=arten.id
			WHERE berichte.state=1 AND berichte.date1 LIKE '.$db->quote($year.'%').'
			GROUP BY data1
			ORDER BY arten.ordering;';
		$result = self::executeQuery($query, 1);
		$data = new StdClass;
		$data->datasets[] = new StdClass;
		foreach ($result as $i) {
			$data->labels[] = $i->label;
			//chart.js needs values as integers
			$data->datasets[0]->data[] = intval($i->value);
			$data->datasets[0]->backgroundColor[] = $i->color;
			$data->datasets[0]->hoverBackgroundColor[] = $i->color . 'bb';
		}
		return $data;
	}

	public static function getYears() {
		$db = JFactory::getDbo();
		$query =
			'SELECT id, title, marker AS color
			FROM #__eiko_einsatzarten
			WHERE state=1
			ORDER BY ordering;';
		$categories = $db->setQuery($query)->loadObjectList();

		$query =
			'SELECT data1 AS id,
				YEAR(date1) AS year,
				count(data1) AS value
			FROM #__eiko_einsatzberichte
			WHERE state=1
			GROUP BY data1, YEAR(date1)
			ORDER BY year, id;';
		$results = $db->setQuery($query)->loadObjectList();

		$years = array();
		/* Restructure result data by years first to be later able to see if there
		   is no data for a certain year in a certain category */
		foreach ($results as $r) {
			if (!array_key_exists($r->year, $years))
				$years[$r->year] = array();
			$years[$r->year][$r->id] = $r->value;
		}

		$data = new StdClass;
		$data->labels = array_keys($years);
		foreach ($categories as $i => $cat) {
			$data->datasets[$i] = new StdClass;
			$data->datasets[$i]->label = $cat->title;
			$data->datasets[$i]->backgroundColor = $cat->color . 'bb';
			$data->datasets[$i]->borderWidth = 1;
			$data->datasets[$i]->borderColor = $cat->color;
			$data->datasets[$i]->hoverBackgroundColor = $cat->color;
			$data->datasets[$i]->data = array();
			foreach ($data->labels as $y) {
				if (array_key_exists($cat->id, $years[$y]))
					$data->datasets[$i]->data[] = (int) $years[$y][$cat->id];
				else
					$data->datasets[$i]->data[] = 0;
			}
		}
		return $data;
	}

	public static function getAjax() {
		// get data from Ajax request
		$input = JFactory::getApplication()->input;

		if ($input->exists('year'))
		{
			// only allow unsigned integers
			$year = $input->get('year', date('Y'), 'UINT');

			return json_encode(self::getStatsByType($year));
		}

		if ($input->exists('all'))
		{
			return json_encode(self::getYears());
		}

		return http_response_code(400);
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
