<?php
defined('_JEXEC') or die('Restricted Access');
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

JLoader::import('mod_einsatz_stats.helper', JPATH_SITE.DS.'modules');

$mode = $params->get('mode', '1');

switch ($mode) {
    case 0:
        $next = modEinsatzStats::getNext();
        break;
    case 1:
        $statsByType = modEinsatzStats::getStatsByType();
        break;
}

require(JModuleHelper::getLayoutPath('mod_einsatz_stats'));

?>
