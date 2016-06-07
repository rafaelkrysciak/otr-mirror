@extends('app')

@section('title', 'Suche - '.$q)

@section('content')
    <style type="text/css">
        .tooltip-inner {
            max-width: 500px;
        }
    </style>

    <h1 class="text-center">Suche</h1>

    <br><br>
    {!! Form::open(['url'=>'tvprogram/search', 'method'=>'get', 'class' => '']) !!}
    {!! Form::hidden('page', '1') !!}
    <div class="row">
        <div class="col-md-offset-1 col-md-6 col-xs-6">
            {!! Form::text('q', $q, ['class'=>'form-control', 'placeholder'=>'Suchen']) !!}
        </div>
        <div class="col-md-2 col-xs-4">
            {!! Form::select('language', $languages, $lang, ['class'=>'form-control']) !!}
        </div>
        <div class="col-md-2 col-xs-2">
            {!! Form::submit('Suchen', ['class'=>'form-control btn btn-primary']) !!}
        </div>
    </div>
    {!! Form::close() !!}
    <br><br>
    @if($q && $paginator->count() == 0)
        Keine Eintäge gefunden
    @else
        <table class="table">
            @foreach($paginator as $item)
                @if($item->start->format('Y-m-d') != $date && $date = $item->start->format('Y-m-d'))
                    <tr>
                        <td colspan="5"><h3><i class="glyphicon glyphicon-calendar"></i> {{$date}}</h3></td>
                    </tr>
                    <tr>
                        <th>Listen</th>
                        <th>Sender</th>
                        <th>Uhrzeit</th>
                        <th>Titel</th>
                        <th class="visible-md-block visible-lg-block">Beschreibung</th>
                    </tr>
                @endif
                @include('tvprogram.index_row')
            @endforeach
        </table>
    @endif
    {!! $paginator->render() !!}
@stop

@section('scripts')
    @parent
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip({
                delay: {"show": 1500, "hide": 100}
            })
        });
    </script>

    @include('partials.js-add-to-list')
@stop