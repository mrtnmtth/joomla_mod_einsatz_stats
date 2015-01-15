<?php
defined('_JEXEC') or die('Restricted Access');
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

require_once __DIR__ . '/helper.php';

$mode = $params->get('mode', '1');

$js = <<<JS
    (function ($) {
        $(document).ready(function() {
            var value = $('#year').val(),
                request = {
                    'option' : 'com_ajax',
                    'module' : 'einsatz_stats',
                    'data'   : '2013',
                    'format' : 'raw'
                };
            $.ajax({
                type   : 'GET',
                data   : request,
                success: function (response) {
                    //var response = parseJSON(data);
                    //var myPieChart = new Chart(ctx).Pie(response);
                }
            });
            return false;
        });
    })(jQuery)
JS;

switch ($mode) {
    case 0:
        $next = modEinsatzStatsHelper::getNext();
        break;

    case 1:
        $statsByType = modEinsatzStatsHelper::getStatsByType(date('Y'));

        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration($js);

        break;
}


require(JModuleHelper::getLayoutPath('mod_einsatz_stats'));

?>
