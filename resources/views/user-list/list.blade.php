@extends('app')

@section('content')
    <h1>{{$title}}</h1>
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
            <tr>
                <td class="nowrap">
                    <a href="#" class="add-to-list {{$lists[$item->id][\App\User::FAVORITE]}}"
                       data-list="{{\App\User::FAVORITE}}" data-id="{{$item->id}}" title="Merken" data-toggle="tooltip">
                        <i class="glyphicon glyphicon-star"></i>
                    </a>
                    <a href="#" class="add-to-list {{$lists[$item->id][\App\User::WATCHED]}}"
                       data-list="{{\App\User::WATCHED}}" data-id="{{$item->id}}" title="Gesehen" data-toggle="tooltip">
                        <i class="glyphicon glyphicon-ok-circle"></i>
                    </a>
                </td>
                <td class="nowrap"><span class="label label-default">{{$item->station}}</span></td>
                <td class="nowrap">
                    <i class="glyphicon glyphicon-time"></i> {{$item->start->format('H:i')}}<span class="hidden-xs"> - {{$item->end->format('H:i')}}</span>
                </td>
                <td class="nowrap">
                    <strong>
                        <a href="{{url('tvprogram/show',['id' => $item->id])}}">{{$item->title}}</a>
                    </strong>
                </td>
                <td class="visible-md-block visible-lg-block" title="{{$item->description}}" data-toggle="tooltip" data-placement="left">
                    <table class="fixed-table">
                        <tr><td>{{$item->description}}</td></tr>
                    </table>
                </td>
            </tr>

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