<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_einsatz_stats
 *
 * @copyright   Copyright (C) 2014 - 2018 Martin Matthaei
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted Access');

JLoader::register('ModEinsatzStatsHelper', __DIR__ . '/helper.php');

/**
 * @var \Joomla\CMS\Object\CMSObject $params
 */
$mode = $params->get('mode', '1');
$pie_size = $params->get('pie_size', '175');
$pie_legend = $params->get('pie_legend', '1') ? 'true' : 'false';
$all_stats = $params->get('all_stats', '1') == 1;

switch ($mode) {
    case 0:
        $next = ModEinsatzStatsHelper::getNext();
        break;

    case 1:
        JHtml::_('jquery.framework', false);
        $doc = JFactory::getDocument();

        // remove Mootools libs because of conflicts with modern JS
        foreach ($doc->_scripts as $path => $attribs) {
            if (strstr($path, 'mootools') !== false) {
                unset($doc->_scripts[$path]);
            }
        }

        $doc->addScriptDeclaration('const showLegend = ' . $pie_legend . ';');
        $doc->addScript(JURI::root() . 'modules/mod_einsatz_stats/js/mod_einsatz_stats.js');
        break;
}


require(JModuleHelper::getLayoutPath('mod_einsatz_stats'));
