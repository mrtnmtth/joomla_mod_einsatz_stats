<?php
defined('_JEXEC') or die('Restricted Access');
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

JLoader::import('mod_einsatz_stats.helper', JPATH_SITE.DS.'modules');

$next = modEinsatzStats::getNext();

require(JModuleHelper::getLayoutPath('mod_einsatz_stats'));

?>
