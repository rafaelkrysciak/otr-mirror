@extends('app')

@section('title')
    TV Programm - {{$date->format('Y-m-d')}}
@stop

@section('head')
    <style>
        /*.ui-state-highlight {}
        .droppable .ui-droppable-active .ui-state-active*/

        .ui-draggable-dragging {
            z-index: 101;
        }
    </style>
@stop


@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="page-header">
                <h1>TV Programm <small>{{$date->format('Y-m-d')}}</small></h1>
            </div>
            <h4>
                <span class="label label-default draggable" data-station="RTL">RTL</span>
                <span class="label label-default draggable" data-station="PRO7">PRO7</span>
                <span class="label label-default draggable" data-station="SAT1">SAT1</span>
                <span class="label label-default draggable" data-station="VOX">VOX</span>
                <span class="label label-default draggable" data-station="RTL2">RTL2</span>
                <span class="label label-default draggable" data-station="ARD">ARD</span>
                <span class="label label-default draggable" data-station="ZDF">ZDF</span>
                <span class="label label-default draggable" data-station="WDR">WDR</span>
                <span class="label label-default draggable" data-station="KABEL 1">KABEL 1</span>
            </h4>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form class="form-inline" id="config-form">
                <div class="form-group">
                    <label class="sr-only" for="config-stations">Sender</label>
                    <select name="station-group" id="config-stations" class="form-control">
                        <option value="public" {{$stationGroup == 'public' ? 'selected':''}}>Öffentlich-Rechtlichen (ARD, ZDF, WDR, etc)</option>
                        <option value="privat" {{$stationGroup == 'privat' ? 'selected':''}}>Privaten (PRO7, RTL, Sat1, etc)</option>
                        <option value="others" {{$stationGroup == 'others' ? 'selected':''}}>Sonstiege (ORF1, SF1, SPORT1, etc)</option>
                    </select>
                </div>
                <div class="input-group date">
                    <label class="sr-only" for="config-date">Datum</label>
                    <input type="text" name="date" class="form-control" id="config-date" value="{{$date->format('Y-m-d')}}"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                </div>
                <button type="submit" class="btn btn-primary">OK</button>
            </form>
        </div>
    </div>

    <br>
    <div class="table-responsive">
    <table class="table table-bordered table-fixed-header">
        <thead style="background-color: lightgray;opacity: 0.8;">
            <tr>
                @foreach($stations as $key => $station)
                    <th class="droppable" data-position="{{$key}}">{{$station}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
        @for($i = 0; $i <= 24; $i++ )
            @if($i > 19)
                <tr class="warning">
            @else
                <tr>
            @endif

                @foreach($stations as $station)
                    <td>
                    @foreach($tvprogram[$station]->where('hour', $i) as $rec)
                            <div class="media">
                            @if($rec->node_id > 0)
                                    <div class="media-left">
                                        <span class="label label-default">{{date('H:i', strtotime($rec->start))}}</span>
                                        @if($rec->hd)
                                            <strong>HD</strong>
                                        @endif
                                    </div>
                                    <div class="media-body">
                                        <small>
                                            <a href="{{url('tvprogram/show',['id' => $rec->tv_program_id])}}">
                                                {{$rec->title}} <br>
                                                @if((
                                                $rec->imdb_votes > 80000 || ($rec->imdb_votes > 24000 && $rec->imdb_rating > 5.7)) && !empty($rec->amazon_image) &&
                                                is_object($prev[$station]) && $rec->amazon_image != $prev[$station]->amazon_image)
                                                    <img src="{{$rec->amazon_image}}" width="120">
                                                @endif
                                            </a>
                                        </small>
                                    </div>
                            @else
                                    <div class="media-left">
                                        <span class="label label-disabled">{{date('H:i', strtotime($rec->start))}}</span>
                                    </div>
                                    <div class="media-body">
                                        <small><span style="color: #999;">{{$rec->title}}</span></small>
                                    </div>
                            @endif
                                <?php $prev[$station] = $rec ?>
                            </div>
                    @endforeach
                    </td>
                @endforeach
            </tr>
        @endfor
        </tbody>
    </table>
    </div>
@stop

@section('scripts')
    @parent

    <!-- script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/locales/bootstrap-datepicker.de.min.js"></script -->
    <script src="{{ asset('/js/jquery.floatThead.js') }}"></script>
    <!-- script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script -->


    <script>
        $(function() {

            $('#config-date').datepicker({
                dateFormat: "yy-mm-dd",
                firstDay: 1,
                gotoCurrent: true,
                maxDate: "+0d",
                minDate: "-1m"
                /*todayBtn: "linked",
                language: "de",
                autoclose: true,
                todayHighlight: true,
                zIndexOffset: 10001,
                startDate: '{{ date('Y-m-d', time() - (30 * 24 * 60 * 60)) }}',
                endDate: '{{ date('Y-m-d') }}'*/
            });

            $('.input-group.date .input-group-addon').on('click', function() {
                $('#config-date').datepicker('show');
            });

            var $table = $('table.table-fixed-header');
            $table.floatThead({
                zIndex: 2,
                responsiveContainer: function($table){
                    return $table.closest('.table-responsive');
                }
            });


            $( ".draggable" ).draggable({ revert: true });
            $( ".droppable" ).droppable({
                classes: {
                    "ui-droppable-active": "ui-state-active",
                    "ui-droppable-hover": "ui-state-hover"
                },
                drop: function( event, ui ) {
                    var station = ui.draggable.data('station');
                    var position = $(this).data('position');
                    var date = '{{$date->format('Y-m-d')}}';

                    renderStation(position, station, date);
                },
                stop: function( event, ui ) {

                }
            });
        });


        function renderStation(position, station, date)
        {
            $.getJSON('{{url('tvprogram/table-data')}}/'+station+'/'+date, function(data) {
                clearStation(position, station);
                $.each(data, function(key, rec) {
                    var html = renderStationHtml(rec);
                    var element = $('table.table-fixed-header tr:eq('+(rec.hour+2)+')').find('td:eq('+position+')');
                    element.html(element.html() + html);
                });
            });

        }

        function clearStation(position, station)
        {
            $('table.table-fixed-header tr').find('td:eq('+position+')').each(function(i) {
                $( this ).html('');
            });

            $('table.floatThead-table tr').find('th:eq('+position+')').text(station);
        }

        function renderStationHtml(rec) {
            var html = '';

            rec.start = new Date(rec.start);

            html = '<div class="media">';

            if(rec.available) {
                html += '<div class="media-left">'+
                    '<span class="label label-default">'+rec.hourFormated+'</span>';
                if(rec.hd) {
                    html += ' <strong>HD</strong>';
                }
                html += '</div>';

                html += '<div class="media-body">'+
                    '<small><a href="'+rec.link+'">'+rec.title+'<br>';
                if(rec.amazon_image) {
                    html += '<img src="'+rec.amazon_image+'" width="120">';
                }
                html += '</a></small></div>';
            } else {
                html += '<div class="media-left"><span class="label label-disabled">'+rec.hourFormated+'</span></div>';
                html += '<div class="media-body"><small style="color: #999;">'+rec.title+'</small></div>';
            }

            return html;
        }

    </script>
@stop