jQuery(document).ready(function($) {
    let value = getSelectedYear();
    if (!value) {
        return;
    }

    let request = {
        'option' : 'com_ajax',
        'module' : 'einsatz_stats',
        'year'   : value,
        'format' : 'raw'
    };
    $.ajax({
        type   : 'GET',
        data   : request,
        success: function (response) {
            let data = $.parseJSON(response);
            let ctx = $("#einsatzChart").get(0).getContext("2d");
            createPieChart(ctx, data, showLegend);
        }
    });

    let barChart;
    let barChartData;

    $('#einsatzModalToggle').click(function() {
        if (!barChartData) {
            let request = {
                'option' : 'com_ajax',
                'module' : 'einsatz_stats',
                'all'    : '1',
                'format' : 'raw'
            };
            $.ajax({
                type   : 'GET',
                data   : request,
                success: function (response) {
                    barChartData = $.parseJSON(response);
                    barChart = createBarChart(barChartData);
                }
            });
        } else {
            barChart.destroy();
            setTimeout(function(){
                barChart = createBarChart(barChartData);
            }, 100);
        }
    });
});

function getSelectedYear() {
    const $ = jQuery;
    if ($('#filter_year_chzn>a>span').text()) {
        return $('#filter_year_chzn>a>span').text();
    } else if ($('#year').val()) {
        return $('#year').val();
    }
}

function createPieChart(ctx, data, showLegend) {
    let pieChart = new Chart(ctx,{
        type: 'pie',
        data: data,
        options: {
            legendCallback: buildLegendHtml,
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
        jQuery(pieChart.generateLegend()).insertAfter("#einsatzChart");
    }

    return pieChart;
}

function buildLegendHtml (chart) {
    let data = chart.data;
    let datasets = data.datasets;
    let labels = data.labels;
    let text = [];

    text.push('<ul class="' + chart.id + '-legend dl-horizontal unstyled">');

    if (datasets.length) {
        for (let i = 0; i < datasets[0].data.length; ++i) {
            text.push('<li><small><span class="badge" style="background-color:' + datasets[0].backgroundColor[i] + '; margin:2px;">&nbsp;</span> ');
            if (labels[i]) {
                text.push(labels[i]);
            }
            text.push('</small></li>');
        }
    }

    text.push('</ul>');
    return text.join('');
}

function createBarChart(data) {
    let ctx = jQuery('#einsatzModalChart');
    return new Chart(ctx,{
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