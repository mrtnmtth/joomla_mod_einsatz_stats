<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_einsatz_stats
 *
 * @copyright   Copyright (C) 2014 - 2018 Martin Matthaei
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted Access');

class ModEinsatzStatsHelper {

	public static function getNext() {
		$avg = self::queryReportsTimeMean() * 60;	// value in seconds
		// can be NULL if MIN(date1) is 0000-00-00
		if ($avg == 0) return false;
		$last = self::queryReportsTimeLatest();	// timestamp format: YYYY-MM-DD HH:MM:SS
		$next = strtotime($last.' + '.$avg.' seconds');	// unix timestamp in integer
		while ($next < time()) {
			$next = $next + $avg;
		}
		return $next;
	}

	public static function getStatsByType($year) {
		$latest = substr(self::queryReportsTimeLatest(), 0, 4);
		if (($year > $latest) or !(ctype_digit($year)))
			$year = $latest;
		$result = self::queryReportsXType($year);

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
		$types = self::queryTypes();
		$reports = self::queryReportsByTypeAndYear();

		$years = array();
		/* Restructure result data by years first to be later able to see if there
		   is no data for a certain year in a certain category */
		foreach ($reports as $r) {
			if (!array_key_exists($r->year, $years))
				$years[$r->year] = array();
			$years[$r->year][$r->id] = $r->value;
		}

		$data = new StdClass;
		$data->labels = array_keys($years);
		foreach ($types as $i => $t) {
			$data->datasets[$i] = new StdClass;
			$data->datasets[$i]->label = $t->title;
			$data->datasets[$i]->backgroundColor = $t->color . 'bb';
			$data->datasets[$i]->borderWidth = 1;
			$data->datasets[$i]->borderColor = $t->color;
			$data->datasets[$i]->hoverBackgroundColor = $t->color;
			$data->datasets[$i]->data = array();
			foreach ($data->labels as $y) {
				if (array_key_exists($t->id, $years[$y]))
					$data->datasets[$i]->data[] = (int) $years[$y][$t->id];
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


	private static function queryReportsTimeLatest() {
		$db = JFactory::getDbo();
		$query = 'SELECT MAX(date1) FROM #__eiko_einsatzberichte';
		$result = $db->setQuery($query)->loadResult();
		return $result;
	}

	private static function queryReportsTimeMean() {
		$db = JFactory::getDbo();
		$query =
			'SELECT
				CASE
				WHEN COUNT(date1) < 2 THEN 0
				ELSE ROUND(
					TIMESTAMPDIFF(
						MINUTE,
						MIN(date1),
					MAX(date1)) / (COUNT(date1)-1))
				END
			AS mean_time
			FROM #__eiko_einsatzberichte	';
		$result = $db->setQuery($query)->loadResult();
		return $result;
	}

	private static function queryReportsByTypeAndYear() {
		$db = JFactory::getDbo();
		$query =
			'SELECT data1 AS id,
				YEAR(date1) AS year,
				count(data1) AS value
			FROM #__eiko_einsatzberichte
			WHERE state=1
			GROUP BY data1, YEAR(date1)
			ORDER BY year, id;';
		$result = $db->setQuery($query)->loadObjectList();
		return $result;
	}

	private static function queryReportsXType($year) {
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
		$result = $db->setQuery($query)->loadObjectList();
		return $result;
	}

	private static function queryTypes() {
		$db = JFactory::getDbo();
		$query =
			'SELECT id, title, marker AS color
			FROM #__eiko_einsatzarten
			WHERE state=1
			ORDER BY ordering;';
		$result = $db->setQuery($query)->loadObjectList();
		return $result;
	}
}
