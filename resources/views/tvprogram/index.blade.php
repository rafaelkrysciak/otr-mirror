@extends('app')

@section('title')
    Sendungen
    @if($lang)
        auf {{$lang}}
    @endif

    - Seite {{$paginator->currentPage()}}
@stop

@section('content')
    <style type="text/css">
        .tooltip-inner {
            max-width: 500px;
        }
    </style>

    <h1>
        Sendungen
        @if($lang)
            auf {{$lang}}
        @endif
    </h1>
    <br>
    <table class="table">
    @foreach($paginator as $item)
        @if($item->start->format('Y-m-d') != $date && $date = $item->start->format('Y-m-d'))
            <tr>
                <td colspan="5">
                    @include('partials.ad_728x90')
                    <h3><i class="glyphicon glyphicon-calendar"></i> {{$date}}</h3>
                </td>
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
    <hr>
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