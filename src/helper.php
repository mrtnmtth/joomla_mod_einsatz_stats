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
		$query = $db->getQuery(true);

		$query->select('MAX(' . $db->qn('date1') . ')');
		$query->from($db->qn('#__eiko_einsatzberichte'));

		$result = $db->setQuery($query)->loadResult();
		return $result;
	}

	private static function queryReportsTimeMean() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select(
			'CASE WHEN COUNT(' . $db->qn('date1') . ') < 2 THEN 0 ' .
			'ELSE ROUND(TIMESTAMPDIFF(' .
			'MINUTE, MIN(' . $db->qn('date1') . '), MAX(' . $db->qn('date1') . ')' .
			') / (COUNT(' . $db->qn('date1') . ')-1)) ' .
			'END AS ' . $db->qn('mean_time'));
		$query->from($db->qn('#__eiko_einsatzberichte'));

		$result = $db->setQuery($query)->loadResult();
		return $result;
	}

	private static function queryReportsByTypeAndYear() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->qn('data1', 'id'));
		$query->select('YEAR(' . $db->qn('date1') . ') AS ' . $db->qn('year'));
		$query->select('COUNT(' . $db->qn('data1') . ') AS ' . $db->qn('value'));
		$query->from($db->qn('#__eiko_einsatzberichte'));
		$query->where($db->qn('state') . ' = 1');
		$query->group($db->qn(array('data1', 'year')));
		$query->order($db->qn(array('year', 'id')));

		$result = $db->setQuery($query)->loadObjectList();
		return $result;
	}

	private static function queryReportsXType($year) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->qn('a.title', 'label'));
		$query->select('COUNT(' . $db->qn('data1') . ') AS ' . $db->qn('value'));
		$query->select($db->qn('a.marker', 'color'));
		$query->from($db->qn('#__eiko_einsatzberichte', 'b'));
		$query->join('INNER', $db->qn('#__eiko_einsatzarten', 'a') .
			' ON (' . $db->qn('b.data1') . ' = ' . $db->qn('a.id') . ')');
		$query->where($db->qn('b.state') . ' = 1 ' .
			'AND ' . $db->qn('b.date1') . ' LIKE ' . $db->q($year.'%'));
		$query->group($db->qn('data1'));
		$query->order($db->qn('a.ordering'));

		$result = $db->setQuery($query)->loadObjectList();
		return $result;
	}

	private static function queryTypes() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->qn(array('id', 'title')));
		$query->select($db->qn('marker', 'color'));
		$query->from($db->qn('#__eiko_einsatzarten'));
		$query->where($db->qn('state') . ' = 1');
		$query->order($db->qn('ordering'));

		$result = $db->setQuery($query)->loadObjectList();
		return $result;
	}
}
