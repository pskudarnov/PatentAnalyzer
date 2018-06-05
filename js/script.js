$(document).ready(function() {

    $('body').on("click", "#button_submit", function () {
        $("#patent_data").submit();
    });

    var time = performance.now();//и тут время на построение графика замерим. Только его из консоли переписывать предется так как js не умеет в файлы писать)
    //вроде все) ну го можно в любом из 5 браузеров)))
var mouthName = [
        "Январь",
        "Февраль",
        "Март",
        "Апрель",
        "Май",
        "Июнь",
        "Июль",
        "Август",
        "Сентябрь",
        "Октябрь",
        "Ноябрь",
        "Декабрь"
    ];

    var series = [];
    var event = JSON.parse($("#main_content").attr("data-content"));
    var oyText = $("#main_content").attr("data-oy");

    if (typeof(event) != 'undefined') {
        for (var key in event) {
            console.log("event[key]", event[key]);
            series.push(event[key]);
        }
    }

    $('#container').highcharts({
        credits: {
            enabled: false
        },
        chart: {
            type: 'spline'
        },
        plotOptions: {
            spline: {
                lineWidth: 4,
                states: {
                    hover: {
                        lineWidth: 5
                    }
                },
                marker: {
                    enabled: false
                }
            }
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: {
                year: '%Y'
            }
        },
        yAxis: {
            title: {
                text: oyText
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            formatter: function() {
                var s;
                var index = this.point.index;

                s = '<span style="font-size: 12px">'+this.series.userOptions.data[index][2]+'</span><br/>'+
                    '<span style="color:'+this.point.color+'">\u25cf</span> '+this.series.name+': <b>'+this.point.y+'</b><br/>';

                return s;
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: series
    });

    time = performance.now() - time;
    console.log('Время выполнения = ', time);
});