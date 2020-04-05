jQuery(document).ready(function($) {
    if ($('#filter_year_chzn>a>span').text()) {
        var value = $('#filter_year_chzn>a>span').text();
    } else if ($('#year').val()) {
        var value = $('#year').val();
    } else {
        return;
    }
    var request = {
        'option' : 'com_ajax',
        'module' : 'einsatz_stats',
        'year'   : value,
        'format' : 'raw'
    };
    $.ajax({
        type   : 'GET',
        data   : request,
        success: function (response) {
            var data = $.parseJSON(response);
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
                                text.push('<li><small><span class="badge" style="background-color:' + datasets[0].backgroundColor[i] + '; margin:2px;">&nbsp;</span> ');
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
            if (showLegend) {
                addLegend(myPieChart);
            }
        }
    });

    var data;
    var myBarChart;

    $('#einsatzModalToggle').click(function() {
        if (!data) {
            var request = {
                'option' : 'com_ajax',
                'module' : 'einsatz_stats',
                'all'    : '1',
                'format' : 'raw'
            };
            $.ajax({
                type   : 'GET',
                data   : request,
                success: function (response) {
                    data = $.parseJSON(response);
                    displayBarChart();
                }
            });
        } else {
            myBarChart.destroy();
            setTimeout(function(){
                displayBarChart();
            }, 100);
        }
    });

    function displayBarChart() {
        var ctx = $('#einsatzModalChart');
        myBarChart = new Chart(ctx,{
            type: 'bar',
            data: data,
            options: {
                title: {
                    display: true,
                    text: 'Übersicht nach Jahren'
                },
                legend: {
                    display: true
                },
                tooltips: {
                    mode: 'index',
                    position: 'nearest',
                    multiKeyBackground: '#000',
                    displayColors: true
                },
                animation: {
                    duration: 2000,
                    numSteps: 100,
                    easing: 'easeInOutQuad'
                },
                scales: {
                    xAxes: [{
                        stacked: true
                    }],
                    yAxes: [{
                        stacked: true
                    }]
                }
            }
        });
    }
});

function addLegend(myPieChart) {
    jQuery(myPieChart.generateLegend()).insertAfter("#einsatzChart");
}