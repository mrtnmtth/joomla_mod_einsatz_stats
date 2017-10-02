<?php
defined('_JEXEC') or die('Restricted Access');

require_once __DIR__ . '/helper.php';

$mode = $params->get('mode', '1');
$pie_size = $params->get('pie_size', '175');
$pie_legend = $params->get('pie_legend', '1') ? 'true' : 'false';
$pie_legend_pos = $params->get('pie_legend_pos', 'top');

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
                    var myPieChart = new Chart(ctx,{
                        type: 'pie',
                        data: data,
                        options: {
                            legend: {
                                display: ${pie_legend},
                                position: '${pie_legend_pos}'
                            },
                            tooltips: {
                                titleFontSize: 9,
                                bodyFontSize: 9,
                                displayColors: false
                            },
                            animation: {
                                duration: 2000,
                                numSteps: 100,
                                easing: 'easeInOutQuad'
                            }
                        }
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
        $doc->addScript('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js');
        $doc->addScriptDeclaration($js);
        break;
}


require(JModuleHelper::getLayoutPath('mod_einsatz_stats'));

?>
