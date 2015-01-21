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
                    'data'   : value,
                    'format' : 'raw'
                };
            $.ajax({
                type   : 'GET',
                data   : request,
                success: function (response) {
                    var data = jQuery.parseJSON(response);
                    var ctx = $("#einsatzChart").get(0).getContext("2d");
                    var myPieChart = new Chart(ctx).Pie(data, {
                        animationEasing: 'easeInOutQuad',
                        tooltipFontSize: 9
                    });
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
        JHtml::_('jquery.framework', false);
        $doc = JFactory::getDocument();
        $doc->addScriptDeclaration($js);

        break;
}


require(JModuleHelper::getLayoutPath('mod_einsatz_stats'));

?>
