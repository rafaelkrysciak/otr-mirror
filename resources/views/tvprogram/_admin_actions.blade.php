@if(Auth::user() && Auth::user()->isAdmin())
    <br>
    <div class="btn-group-vertical btn-group-lg center-block" role="group">
        <a href="{{url('tvprogram/'.$tvProgram->id.'/edit')}}" class="btn btn-default"
           data-toggle="modal" data-target="#iframeModal" data-remote="">
            <i class="glyphicon glyphicon-edit"></i> Edit
        </a>
        @if($tvProgram->film_id)
            <a href="{{url('film/'.$tvProgram->film_id.'/edit')}}" class="btn btn-default"
               data-toggle="modal" data-target="#iframeModal" data-remote="">
                <i class="glyphicon glyphicon-edit"></i> Film Edit
            </a>
        @endif
        <a href="{{url('tvprogram', ['tv_program_id' => $tvProgram->id])}}" class="btn btn-danger"
           data-method="delete" data-confirm="Are you sure?" data-handler="form">
            <i class="glyphicon glyphicon-remove"></i> Delete
        </a>

        <button type="button" class="btn @if($tvProgram->film_mapper_id) btn-primary @else btn-default @endif"
                data-toggle="modal" data-target="#iframeModal" data-remote=""
        @if($tvProgram->film_mapper_id)
                data-src="{{action('FilmMapperController@edit', ['film_mapper' => $tvProgram->film_mapper_id])}}">
            @else
                data-src="{{action('FilmMapperController@create', ['tv_program_id' => $tvProgram->id])}}">
            @endif
            <i class="glyphicon glyphicon-link"></i> Mapper
        </button>
    </div>
    <br>
    <table class="table table-condensed">
        <caption>Stats</caption>
        <tr>
            <th>Quality</th>
            <th>Downloads</th>
        </tr>
        @foreach($stats['formats'] as $quality => $downloads)
            <tr>
                <td>{{$quality}}</td>
                <td>{{$downloads}}</td>
            </tr>
        @endforeach
        <tr>
            <th>Total</th>
            <th>{{$stats['total']}}</th>
        </tr>
        <tr>
            <th>Film</th>
            <th>{{$stats['film']}}</th>
        </tr>
    </table>

    <div id="download-chart"></div>

    @section('scripts')
        @parent

        <!-- Highcharts -->
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/highcharts-more.js"></script>
        <script src="https://code.highcharts.com/modules/solid-gauge.js"></script>

        <script>

            $(drawDownloadsChart);

            function drawDownloadsChart() {

                $.getJSON( "{{url('stats/downloads-by-tv-program-id/'.$tvProgram->id)}}", function( downloads ) {

                    var downloadData = [];
                    $.each(downloads, function(key, value) {
                        if(value != null) {
                            value[0] = (new Date(value[0])).getTime();
                            downloadData.push([value[0], value[1]]);
                        }
                    });

                    Highcharts.chart('download-chart', {
                        title: {text: 'Downloads'},
                        xAxis: {type: 'datetime'},
                        yAxis: [{title: {text: 'Downloads'}}],
                        tooltip: {dateTimeLabelFormats: {hour:"%A, %b %e, %Y"}},
                        series: [{
                            data: downloadData,
                            type: 'column',
                            name: 'Downloads',
                            marker: {enabled: false}
                        }]
                    });
                });
            }
        </script>
    @stop
@endif
