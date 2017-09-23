@extends('app')

@section('content')

    <div class="row">
        <div class="col-md-6">
            <div id="nodes-worker"></div>
        </div>
        <div class="col-md-6">
            <div id="nodes-disk"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div id="quality"></div>
        </div>
        <div class="col-md-3">
            <div id="sizequality"></div>
        </div>
        <div class="col-md-3">
            <div id="lang"></div>
        </div>
        <div class="col-md-3">
            <div id="sizelang"></div>
        </div>
    </div>
    <div class="row"><br><br><br></div>
    <div class="row">
        <div id="views"></div>
    </div>
    <div class="row">
        <div id="payments"></div>
    </div>
    <div class="row">
        <div id="registrations"></div>
    </div>
    <div class="row">
        <div id="otrkeyfiles"></div>
    </div>
    <div class="row">
        <div id="stations"></div>
    </div>
    <div class="row">
        <div id="stations-avg"></div>
    </div>
@stop

@section('scripts')

    <!-- Highcharts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/solid-gauge.js"></script>




    <script>

        $(document).ready(function() {
            drawCharts();
        });

        function drawCharts() {
            drawNodes();
            drawTopStations();
            drawDownloadsByQuality();
            drawDownloadsByLanguage();
            drawContentSizeByLanguage();
            drawContentSizeByQuality();
            drawViewsAndDownloads();
            drawFileCount();
            drawPayments();
            drawRegistrations();
            setTimeout("redrawCharts()", 60000);
        }

        function redrawCharts() {
            drawNodes();
            drawViewsAndDownloads();
            drawPayments();
            setTimeout("redrawCharts()", 60000);
        }



        function drawNodes() {
            $.getJSON( "{{url('stats/node-stats')}}", function( nodes ) {

                var worker = [], space = [], outerRadius = 100, counter = 0;

                $.each(nodes, function(key, node) {
                    worker.push({
                        name: node.short_name,
                        radius: outerRadius - (counter*(48/nodes.length)) - 2,
                        innerRadius: outerRadius - (counter*(48/nodes.length)) - (48/nodes.length),
                        y: node.busy_workers
                    });
                    space.push({
                        name: node.short_name,
                        radius: outerRadius - (counter*(48/nodes.length)) - 2,
                        innerRadius: outerRadius - (counter*(48/nodes.length)) - (48/nodes.length),
                        y: Math.round((node.free_disk_space/1024/1024/1024)*100)/100
                    });
                    counter++;
                });

                // Worker
                Highcharts.chart('nodes-worker', {
                    chart: {type: 'solidgauge'},
                    credits: {enabled: false},
                    title: {text: 'Worker'},
                    pane: {
                        center: ['50%', '65%'],
                        size: '120%',
                        startAngle: -90,
                        endAngle: 90,
                        background: {
                            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                            innerRadius: '50%',
                            outerRadius: '100%',
                            shape: 'arc'
                        }
                    },

                    // the value axis
                    yAxis: {
                        stops: [
                            [0.1, '#55BF3B'], // green
                            [0.5, '#DDDF0D'], // yellow
                            [0.9, '#DF5353'] // red
                        ],
                        lineWidth: 0,
                        minorTickInterval: null,
                        min: 0,
                        max: 200,
                        tickPixelInterval: 400,
                        tickWidth: 0,
                        labels: {
                            y: 16
                        }
                    },

                    series: [{
                        name: 'Speed',
                        data: worker,
                        dataLabels: {
                            enabled: false
                        },
                        tooltip: {
                            pointFormat: '{point.name}: <b>{point.y}</b> worker'
                        }
                    }]
                });

                // Disk Space
                Highcharts.chart('nodes-disk', {
                    chart: {type: 'solidgauge'},
                    credits: {enabled: false},
                    title: {text: 'Disk Space'},
                    pane: {
                        center: ['50%', '65%'],
                        size: '120%',
                        startAngle: -90,
                        endAngle: 90,
                        background: {
                            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                            innerRadius: '50%',
                            outerRadius: '100%',
                            shape: 'arc'
                        }
                    },

                    // the value axis
                    yAxis: {
                        stops: [
                            [0.1, '#DF5353'], // red
                            [0.3, '#DDDF0D'], // yellow
                            [0.7, '#55BF3B'] // green
                        ],
                        lineWidth: 0,
                        minorTickInterval: null,
                        min: 0,
                        max: 100,
                        tickPixelInterval: 400,
                        tickWidth: 0,
                        labels: {
                            y: 16
                        }
                    },

                    series: [{
                        name: 'Disk Spave',
                        data: space,
                        dataLabels: {
                            enabled: false
                        },
                        tooltip: {
                            pointFormat: '{point.name}: <b>{point.y}</b> GB'
                        }
                    }]
                });

            });
        }


        function drawViewsAndDownloads() {

            $.getJSON( "{{url('stats/views-and-downloads')}}", function( viewsAndDownloads ) {

                var views = [], downloads = [];

                $.each(viewsAndDownloads, function(key, value) {
                    if(value != null) {
                        value[0] = (new Date(value[0])).getTime();
                        downloads.push([value[0], value[1]]);
                        views.push([value[0], value[2]]);
                    }
                });

                Highcharts.chart('views', {
                    chart: {type: 'spline'},
                    title: {text: 'Views and Downloads'},
                    xAxis: {type: 'datetime'},
                    yAxis: [{
                        title: {text: 'Views'}
                    },{
                        title: {text: 'Downloads'},
                        opposite: true
                    }],
                    series: [{
                        data: views,
                        type: 'spline',
                        name: 'Views',
                        marker: {enabled: false}
                    },{
                        data: downloads,
                        type: 'spline',
                        name: 'Downloads',
                        yAxis: 1,
                        marker: {enabled: false}
                    }]
                });
            });
        }


        function drawPayments() {
            $.getJSON( "{{url('stats/payments')}}", function( payments ) {
                var count = [], amount = [];

                $.each(payments, function(key, value) {
                    if(value != null) {
                        value[0] = (new Date(value[0])).getTime();
                        amount.push([value[0], value[1]]);
                        count.push([value[0], value[2]]);
                    }
                });

                Highcharts.chart('payments', {
                    title: {text: 'Payments'},
                    xAxis: {type: 'datetime'},
                    yAxis: [{
                        title: {text: 'Count'}
                    },{
                        title: {text: 'Amount'},
                        opposite: true
                    }],
                    series: [{
                        data: amount,
                        type: 'column',
                        name: 'Amount',
                        yAxis: 1,
                        marker: {enabled: false}
                    },{
                        data: count,
                        type: 'spline',
                        name: 'Count',
                        marker: {enabled: false}
                    }]
                });
            });
        }


        function drawRegistrations() {
            $.getJSON( "{{url('stats/registrations')}}", function( registrations ) {
                var confirmed = [], notConfirmed = [];

                $.each(registrations, function(key, value) {
                    if(value != null) {
                        value[0] = (new Date(value[0])).getTime();
                        confirmed.push([value[0], value[1]]);
                        notConfirmed.push([value[0], value[2]]);
                    }
                });

                Highcharts.chart('registrations', {
                    chart: {type: 'column'},
                    title: {text: 'Registrations'},
                    xAxis: {
                        type: 'datetime',
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    yAxis: {title: {text: 'Registrations'}},
                    plotOptions: {
                        series: {
                            stacking: 'normal'
                        }
                    },
                    series: [{
                        name: 'Confirmed',
                        data: confirmed
                    },{
                        name: 'Not Confirmed',
                        data: notConfirmed
                    }]
                });
            });
        }


        function drawDownloadsByQuality() {
            $.getJSON( "{{url('stats/downloads-by-quality')}}", function( qualityByDownloads ) {
                var seriesData = [];

                $.each(qualityByDownloads, function(key, value) {
                    if(value != null) {
                        seriesData.push({
                            name: value[0],
                            y: value[1]
                        });
                    }
                });

                Highcharts.chart('quality', {
                    chart: {
                        type: 'pie'
                    },
                    title: {text: 'Downloads by quality'},
                    tooltip: {pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'},
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {enabled: false},
                            showInLegend: true
                        }
                    },
                    series: [{
                        name: 'Downloads',
                        colorByPoint: true,
                        data: seriesData
                    }]
                });
            });
        }

        function drawDownloadsByLanguage() {
            $.getJSON( "{{url('stats/downloads-by-language')}}", function( languageByDownloads ) {
                var seriesData = [];

                $.each(languageByDownloads, function(key, value) {
                    if(value != null) {
                        seriesData.push({
                            name: value[0],
                            y: value[1]
                        });
                    }
                });

                Highcharts.chart('lang', {
                    chart: {
                        type: 'pie'
                    },
                    title: {text: 'Downloads by language'},
                    tooltip: {pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'},
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {enabled: false},
                            showInLegend: true
                        }
                    },
                    series: [{
                        name: 'Downloads',
                        colorByPoint: true,
                        data: seriesData
                    }]
                });
            });
        }

        function drawContentSizeByLanguage() {
            $.getJSON( "{{url('stats/content-size-by-language')}}", function( contentSizeBylanguage ) {
                var seriesData = [];

                $.each(contentSizeBylanguage, function(key, value) {
                    if(value != null) {
                        seriesData.push({
                            name: value[0],
                            y: value[1]
                        });
                    }
                });

                Highcharts.chart('sizelang', {
                    chart: {
                        type: 'pie'
                    },
                    title: {text: 'Content size by language'},
                    tooltip: {pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'},
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {enabled: false},
                            showInLegend: true
                        }
                    },
                    series: [{
                        name: 'Size',
                        colorByPoint: true,
                        data: seriesData
                    }]
                });
            });
        }

        function drawContentSizeByQuality() {
            $.getJSON( "{{url('stats/content-size-by-quality')}}", function( contentSizeByQuality ) {
                var seriesData = [];

                $.each(contentSizeByQuality, function(key, value) {
                    if(value != null) {
                        seriesData.push({
                            name: value[0],
                            y: value[1]
                        });
                    }
                });

                Highcharts.chart('sizequality', {
                    chart: {
                        type: 'pie'
                    },
                    title: {text: 'Content size by quality'},
                    tooltip: {pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'},
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {enabled: false},
                            showInLegend: true
                        }
                    },
                    series: [{
                        name: 'Size',
                        colorByPoint: true,
                        data: seriesData
                    }]
                });
            });
        }


        function drawFileCount() {

            $.getJSON("{{url('stats/file-count-by-day')}}", function (otrkeyFilesCountByDate) {
                var count = [];

                $.each(otrkeyFilesCountByDate, function (key, value) {
                    if (value != null) {
                        value.date = (new Date(value.date)).getTime();
                        count.push([value.date, value.count]);
                    }
                });

                Highcharts.chart('otrkeyfiles', {
                    chart: {type: 'column'},
                    title: {text: 'Files count'},
                    xAxis: {
                        type: 'datetime',
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    yAxis: {title: {text: 'Count'}},
                    plotOptions: {
                        series: {
                            stacking: 'normal'
                        }
                    },
                    series: [{
                        name: 'File count',
                        data: count
                    }]
                });
            });
        }


        function drawTopStations() {

            $.getJSON( "{{url('stats/top-stations')}}", function( topStationsByDownloads ) {
                var stations = [], downloads = [];

                $.each(topStationsByDownloads, function(key, value) {
                    if(value != null) {
                        stations.push([value[0]]);
                        downloads.push([value[1]]);
                    }
                });

                Highcharts.chart('stations', {
                    chart: {type: 'column'},
                    title: {text: 'Top stations by total download'},
                    xAxis: {categories: stations},
                    yAxis: {title: {text: 'Count'}},
                    series: [{
                        name: 'Downloads',
                        data: downloads
                    }]
                });
            });

            $.getJSON( "{{url('stats/top-stations-by-avg-download')}}", function( topStationsByAvgDownloads ) {
                var stations = [], downloads = [];

                $.each(topStationsByAvgDownloads, function(key, value) {
                    if(value != null) {
                        stations.push([value[0]]);
                        downloads.push([value[1]]);
                    }
                });

                Highcharts.chart('stations-avg', {
                    chart: {type: 'column'},
                    title: {text: 'Top stations by avg download'},
                    xAxis: {categories: stations},
                    yAxis: {title: {text: 'Count'}},
                    series: [{
                        name: 'Downloads',
                        data: downloads
                    }]
                });
            });

        }

    </script>
@stop