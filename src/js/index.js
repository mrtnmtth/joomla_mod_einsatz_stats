import {
    Chart,
    ArcElement,
    BarElement,
    BarController,
    PieController,
    CategoryScale,
    LinearScale,
    Legend,
    Tooltip
} from 'chart.js';
import { htmlLegendPlugin } from "./chartjs_html_legend";

Chart.register(ArcElement, BarElement, BarController, PieController, CategoryScale, LinearScale, Legend, Tooltip);

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
    let config = {
        type: 'pie',
        data: data,
        options: {
            animation: {
                duration: 2000,
                easing: 'easeInOutQuad'
            },
            plugins: {
                legend: {
                    display: false
                },
                htmlLegend: {
                    containerId: 'einsatzChartContainer'
                },
                tooltip: {
                    titleFont: {
                        size: 9
                    },
                    bodyFont: {
                        size: 9
                    },
                    displayColors: false
                },
            },
        },
        plugins: []
    };

    if (showLegend) {
        config.plugins.push(htmlLegendPlugin);
    }

    return new Chart(ctx, config);
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
            responsive: true,
            animation: {
                duration: 2000,
                easing: 'easeInOutQuad'
            },
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true
                }
            },
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    mode: 'index',
                    position: 'nearest',
                    multiKeyBackground: '#000',
                    displayColors: true
                },
            }
        }
    });
}
