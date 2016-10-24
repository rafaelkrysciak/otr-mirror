@extends('app')

@section('title')
    Top 100 Sendungen - Seite {{$paginator->currentPage()}}
@stop


@section('content')
    <style type="text/css">
        .tooltip-inner {
            max-width: 500px;
        }
    </style>

    <h1>
        Top 100
    </h1>
    <br>
    @include('partials.ad_728x90')
    <table class="table">
        <tr>
            <th class="text-right">#</th>
            <th class="hidden-xs">Listen</th>
            <th>Sender</th>
            <th class="hidden-xs">Datum</th>
            <th>Titel</th>
            <th class="visible-md-block visible-lg-block">Beschreibung</th>
        </tr>
        @foreach($paginator as $key => $item)
            @include('tvprogram.index_row_top100', ['pos' => ($key+1)+(($paginator->currentPage()-1)*$paginator->perPage())])
        @endforeach
    </table>
    @include('partials.ad_728x90')
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