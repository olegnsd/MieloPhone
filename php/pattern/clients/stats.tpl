<div class="" style="margin-bottom: 5px">
    <form class="form-inline" role="form" method="post">
	<div class="form-group">
	    <input type="text" name="daterange" class="form-control" value="{!DATEFROM!} - {!DATETO!}" style="width: 310px" />
	</div>
	<div class="form-group">
	    <input type="submit" class="btn btn-success" value="Сформировать" />
	</div>
    </form>
</div>

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
<div id="container2" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
<div id="container3" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

    <script src="/js/hightcharts/js/highcharts.js"></script>
<script type="text/javascript" src="/js/moment.min.js"></script>
<script type="text/javascript" src="/js/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="/css/daterangepicker.css" />


<script type="text/javascript">

$(function () {
    $('input[name="daterange"]').daterangepicker({
        timePicker: true,
	"timePicker24Hour": true,
        timePickerIncrement: 30,
        locale: {
            format: 'DD.MM.YYYY HH:mm:SS'
        }
    });

    $('#container').highcharts({
        chart: {
            type: 'spline'
        },
        title: {
            text: 'Статистика обзвона "{!NAME!}"'
        },
        xAxis: {
            type: 'datetime',
            labels: {
                overflow: 'justify'
            }
        },
        yAxis: {
            title: {
                text: 'Количество'
            },
            minorGridLineWidth: 0,
            gridLineWidth: 0,
            alternateGridColor: null,
        },
        tooltip: {
		formatter: function() {
		    var s = [];
		    $.each(this.points, function(i, point) {
			s.push("<span style=\"color: "+point.series.color+"\">" + point.series.name+':</span> '+point.y);
		    });

		    return '<b>Количество на '+Highcharts.dateFormat('%d.%m.%Y %H', this.x)+ ':59:59</b><br/>' + s.join("<br/>");
		},
	    shared: true
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
                },
                pointInterval: 3600000, // one hour
                pointStart: Date.UTC({!DATESTART!})
            }
        },
        series: [{
            name: 'Всего звонков',
	    color: '#555',
            data: [{!RINGS!}]
        }, {
            name: 'Поднята трубка',
	    color: '#3482df',
            data: [{!ACTIVE!}]
        }, {
            name: 'Нажата кнопка',
	    color: '#85ec65',
            data: [{!SUCCESS!}]
        }, {
            name: 'В черном списке',
	    color: '#f94545',
            data: [{!BLACK!}]
        }],
        navigation: {
            menuItemStyle: {
                fontSize: '10px'
            }
        }
    });

	$('#container2').highcharts({
            chart: {
                zoomType: 'x'
            },
            title: {
                text: 'Активность канала "{!NAME!}"'
            },
            xAxis: {
                type: 'datetime'
            },
            yAxis: {
                title: {
                    text: 'Активность'
                }
            },
	    tooltip: {
		formatter: function() {
		    var s = [];
		    $.each(this.points, function(i, point) {
			s.push("<span style=\"color: "+point.series.color+"\">" + point.series.name+':</span> '+point.y);
		    });

		    return '<b>Активность на '+Highcharts.dateFormat('%d.%m.%Y %H:%M', this.x)+ ':</b><br/>' + (this.y == 0? "Не активно": "Активно");
		},
	    shared: true
    	    },
            legend: {
                enabled: false
            },
            plotOptions: {
                area: {
                    fillColor: {
                        linearGradient: {
                            x1: 0,
                            y1: 0,
                            x2: 0,
                            y2: 1
                        },
                        stops: [
                            [0, Highcharts.getOptions().colors[0]],
                            [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                        ]
                    },
                    marker: {
                        radius: 2
                    },
                    lineWidth: 1,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    threshold: null
                }
            },

            series: [{
                type: 'area',
		name: '',
                data: [{!ACTIVES!}]
            }]
        });



    $('#container3').highcharts({
        chart: {
            type: 'spline'
        },
        title: {
            text: 'Баланс канала "{!NAME!}"'
        },
        xAxis: {
            type: 'datetime',
            labels: {
                overflow: 'justify'
            }
        },
        yAxis: {
            title: {
                text: 'Баланс (руб.)'
            },
            minorGridLineWidth: 0,
            gridLineWidth: 0,
            alternateGridColor: null,
        },
        tooltip: {
		formatter: function() {
		    var s = [];
		    $.each(this.points, function(i, point) {
			s.push("<span style=\"color: "+point.series.color+"\">" + point.series.name+':</span> '+point.y);
		    });

		    return '<b>Баланс на '+Highcharts.dateFormat('%d.%m.%Y %H:%M', this.x)+ '</b><br/>' + s.join("<br/>")+' руб.';
		},
	    shared: true
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
        series: [{
	    name: 'Баланс',
            data: [{!BALANCE!}]
        }],
        navigation: {
            menuItemStyle: {
                fontSize: '10px'
            }
        }
    });


});


</script>