<?php
defined('_JEXEC') or die('Restricted Access');

require_once __DIR__ . '/helper.php';

$mode = $params->get('mode', '1');
$pie_size = $params->get('pie_size', '175');
$pie_legend = $params->get('pie_legend', '1') ? '$(myPieChart.generateLegend()).insertAfter("#einsatzChart");' : '';

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
                            legendCallback: function(chart) {
                                var text = [];
                                text.push('<ul class="' + chart.id + '-legend dl-horizontal unstyled">');

                                var data = chart.data;
                                var datasets = data.datasets;
                                var labels = data.labels;

                                if (datasets.length) {
                                  for (var i = 0; i < datasets[0].data.length; ++i) {
                                    text.push('<li class="text-center"><small><span class="badge" style="background-color:' + datasets[0].backgroundColor[i] + '; margin:2px;">&nbsp;</span> ');
                                    if (labels[i]) {
                                      text.push(labels[i]);
                                    }
                                    text.push('</small></li>');
                                  }
                                }

                                text.push('</ul>');
                            return text.join('');
                            },
                            legend: {
                                display: false
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
                    ${pie_legend}
                }
            });
            return false;
        });
    })(jQuery)
JS;

$js_legend = <<<JS
JS;

switch ($mode) {
    case 0:
        $next = modEinsatzStatsHelper::getNext();
        break;

    case 1:
        JHtml::_('jquery.framework', false);
        $doc = JFactory::getDocument();
        $doc->addScript('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js');
        $doc->addScriptDeclaration($js);
        break;
}


require(JModuleHelper::getLayoutPath('mod_einsatz_stats'));

?>
