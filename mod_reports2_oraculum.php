<?php
defined('_JEXEC') or die('Restricted Access');
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

JLoader::import('mod_reports2_oraculum.helper', JPATH_SITE.DS.'modules');

$next = modReports2Oraculum::getNext();
//$array = modReports2Oraculum::getNextType($next);

require(JModuleHelper::getLayoutPath('mod_reports2_oraculum'));

?>
