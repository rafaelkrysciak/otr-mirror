@extends('app')

@section('content')
    <div class="row">
        <div id="nodes-worker"></div>
    </div>
    <div class="row">
        <div id="nodes-disk"></div>
    </div>
    <div class="row">
        <div id="quality" class="col-md-6"></div>
        <div id="lang" class="col-md-6"></div>
    </div>
    <div class="row">
        <div id="sizequality" class="col-md-6"></div>
        <div id="sizelang" class="col-md-6"></div>
    </div>
    <div class="row">
        <div id="views"></div>
    </div>
    <div class="row">
        <div id="stations"></div>
    </div>
    <div class="row">
        <div id="stations-avg"></div>
    </div>
@stop

@section('scripts')

    <link href="//cdnjs.cloudflare.com/ajax/libs/metrics-graphics/2.4.0/metricsgraphics.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/metrics-graphics/2.4.0/metricsgraphics.js"></script>

    <script type="text/javascript" src="https://www.google.com/jsapi"></script>



    <script>

        var chartGlobalOptions = {
            width: 'auto',
            height: 400,
            hAxis: {textStyle: {fontSize: 9}},
            vAxis: {textStyle: {fontSize: 9}}
        };
        var gaugeGlobalOptions = {
            width: 500, height: 140,
            max: 500,
            redFrom: 400, redTo: 500,
            yellowFrom:300, yellowTo: 400,
            minorTicks: 10,
            majorTicks: ['','','','','','']
        };


        google.load('visualization', '1', {packages: ['corechart', 'bar', 'line', 'gauge']});
        google.setOnLoadCallback(drawCharts);


        function drawCharts() {
            drawNodes();
            drawTopStations();
            drawDownloadsByQuality();
            drawDownloadsByLanguage();
            drawContentSizeByLanguage();
            drawContentSizeByQuality();
            drawViewsAndDownloads();
            setTimeout("redrawCharts()", 60000);
        }

        function redrawCharts() {
            drawNodes();
            drawTopStations();
            drawViewsAndDownloads();
            setTimeout("redrawCharts()", 60000);
        }



        function drawNodes() {
            $.getJSON( "{{url('stats/node-stats')}}", function( nodes ) {


                var nodeStats = [['Label', 'Value']], total = 0;
                $.each(nodes, function(key, node) {
                    nodeStats.push([node.short_name+" worker", node.busy_workers]);
                    total += node.busy_workers;
                });
                nodeStats.push(["Total worker", total]);

                var data = google.visualization.arrayToDataTable(nodeStats);

                var options = $.extend({}, gaugeGlobalOptions, {
                    redFrom: 400, redTo: 500,
                    yellowFrom:300, yellowTo: 400
                });

                var chart = new google.visualization.Gauge(document.getElementById('nodes-worker'));
                chart.draw(data, options);


                // Disk Space
                var nodeStats = [['Label', 'Value']], total = 0;
                $.each(nodes, function(key, node) {
                    var space = parseInt(node.free_disk_space/1024/1024/1024);
                    nodeStats.push([node.short_name+" disk", space]);
                    total += space;
                });

                nodeStats.push(["Total disk", total]);

                var data = google.visualization.arrayToDataTable(nodeStats);

                var options = $.extend({}, gaugeGlobalOptions, {
                    redFrom: 0, redTo: 50,
                    yellowFrom:50, yellowTo: 100
                });

                var chart = new google.visualization.Gauge(document.getElementById('nodes-disk'));
                chart.draw(data, options);
            });
        }


        function drawViewsAndDownloads() {

            $.getJSON( "{{url('stats/views-and-downloads')}}", function( viewsAndDownloads ) {

                $.each(viewsAndDownloads, function(key, value) {if(value == null) return; value[0] = new Date(value[0])});

                var data = new google.visualization.DataTable();
                data.addColumn('date', 'Date');
                data.addColumn('number', 'Downloads');
                data.addColumn('number', 'Views');

                data.addRows(viewsAndDownloads);

                var options = $.extend({}, chartGlobalOptions, {
                    title: 'Views and Downloads'
                });

                var chart = new google.visualization.LineChart(
                        document.getElementById('views'));

                chart.draw(data, options);

            });
        }

        function drawDownloadsByQuality() {
            $.getJSON( "{{url('stats/downloads-by-quality')}}", function( qualityByDownloads ) {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Quality');
                data.addColumn('number', 'Downloads');

                data.addRows(qualityByDownloads);

                var options = $.extend({}, chartGlobalOptions, {
                    title: 'Total Downloads By Quality',
                    is3D: true,
                    width: 'auto'
                });

                var chart = new google.visualization.PieChart(
                        document.getElementById('quality'));

                chart.draw(data, options);
            });
        }

        function drawDownloadsByLanguage() {
            $.getJSON( "{{url('stats/downloads-by-language')}}", function( languageByDownloads ) {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Language');
                data.addColumn('number', 'Downloads');

                data.addRows(languageByDownloads);

                var options = $.extend({}, chartGlobalOptions, {
                    title: 'Total Downloads By Language',
                    width: 'auto',
                    is3D: true,
                    slices: {
                        0: {offset: 0.1},
                        1: {offset: 0.1}
                    }
                });

                var chart = new google.visualization.PieChart(
                        document.getElementById('lang'));

                chart.draw(data, options);
            });
        }

        function drawContentSizeByLanguage() {
            $.getJSON( "{{url('stats/content-size-by-language')}}", function( languageByDownloads ) {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Language');
                data.addColumn('number', 'Size');

                data.addRows(languageByDownloads);

                var options = $.extend({}, chartGlobalOptions, {
                    title: 'Content Size By Language',
                    width: 'auto',
                    is3D: true
                });

                var chart = new google.visualization.PieChart(
                        document.getElementById('sizelang'));

                chart.draw(data, options);
            });
        }

        function drawContentSizeByQuality() {
            $.getJSON( "{{url('stats/content-size-by-quality')}}", function( languageByDownloads ) {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Quality');
                data.addColumn('number', 'Size');

                data.addRows(languageByDownloads);

                var options = $.extend({}, chartGlobalOptions, {
                    title: 'Content Size By Quality',
                    width: 'auto',
                    is3D: true
                });

                var chart = new google.visualization.PieChart(
                        document.getElementById('sizequality'));

                chart.draw(data, options);
            });
        }

        function drawTopStations() {

            $.getJSON( "{{url('stats/top-stations')}}", function( topStationsByDownloads ) {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Station');
                data.addColumn('number', 'Downloads');

                data.addRows(topStationsByDownloads);

                var options = $.extend({}, chartGlobalOptions, {
                    title: 'Top Stations By Total Downloads'
                });

                var chart = new google.visualization.ColumnChart(
                        document.getElementById('stations'));

                chart.draw(data, options);
            });

            $.getJSON( "{{url('stats/top-stations-by-avg-download')}}", function( topStationsByAvgDownloads ) {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Station');
                data.addColumn('number', 'Downloads');

                data.addRows(topStationsByAvgDownloads);

                var options = $.extend({}, chartGlobalOptions, {
                    title: 'Top Stations By Avg Download'
                });

                var chart = new google.visualization.ColumnChart(
                        document.getElementById('stations-avg'));

                chart.draw(data, options);
            });

        }

    </script>
@stop