<?php
defined('_JEXEC') or die('Restricted Access');

require_once __DIR__ . '/helper.php';

$mode = $params->get('mode', '1');
$pie_size = $params->get('pie_size', '175');

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
        $doc->addScript('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js');
        $doc->addScriptDeclaration($js);
        break;
}


require(JModuleHelper::getLayoutPath('mod_einsatz_stats'));

?>
