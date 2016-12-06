@extends('app')

@section('title', ucfirst($type).": ".$title)

@section('content')

    @include('partials.film_nav', ['type' => $type])
    <div id="catalogue-header" class="page-header {{$type}} {{$filter->getAction('method')}}">
        <h1>{{$title}}</h1>

        @include('film.filters', ['filter' => $filter])
    </div>

    @foreach($tvPrograms as $key => $tvProgram)
        @if($key%4 == 0)
            @if($key != 0)
                </div>
            @endif
            <div class="row">
        @endif
        <div class="col-xs-12 col-sm-6 col-md-3">
            <div class="panel film">
                <a href="{{url('tvprogram/film/'.$tvProgram->film_id.'/any/any')}}">
                @if($tvProgram->amazon_image)
                    <img class="img-r_esponsive center-block" src="{{$tvProgram->imageResize(335)}}">
                @else
                    <img class="img-r_esponsive center-block" src="{{asset('img/default_cover.jpg')}}">
                @endif
                </a>
                <a href="{{url('tvprogram/film/'.$tvProgram->film_id.'/any')}}">
                    <h3>{{$tvProgram->title}}</h3>
                </a>
                <span class="nowrap"><i class="glyphicon glyphicon-globe"></i> {{$tvProgram->country()}} ({{$tvProgram->year}})</span> <span class="nowrap"><i class="glyphicon glyphicon-star"></i> Rating: {{$tvProgram->imdb_rating}} ({{(int)($tvProgram->imdb_votes/1000)}}K)</span>
            </div>
        </div>
    @endforeach
    <div class="clearfix"> </div>
    @if(method_exists($tvPrograms, 'render'))
        {!! $tvPrograms->render() !!}
    @else
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-3">
                <div class="panel film">
                    <img style="max-width: 247px;" class="img-r_esponsive center-block" src="{{asset('img/preview_fadedout1.jpg')}}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-3 hidden-xs">
                <div class="panel film">
                    <img style="max-width: 247px;" class="img-r_esponsive center-block" src="{{asset('img/preview_fadedout2.jpg')}}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-3 hidden-sm hidden-xs">
                <div class="panel film">
                    <img style="max-width: 247px;" class="img-r_esponsive center-block" src="{{asset('img/preview_fadedout3.jpg')}}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-3 hidden-sm hidden-xs">
                <div class="panel film">
                    <img style="max-width: 247px;" class="img-r_esponsive center-block" src="{{asset('img/preview_fadedout4.jpg')}}">
                </div>
            </div>
        </div>
        <div class="jumbotron">
            <h1>Jetzt Premium-Mitglied werden <br><small>... und volle Funktionalität genießen</small></h1>
            <p><a class="btn btn-primary btn-lg" href="{{url('payment/prepare')}}" role="button">Mehr erfahren!</a></p>
        </div>

        @include('payment._benefits')
        <br>
        @include('payment._order')
    @endif
@stop

@section('scripts')
    <script src="{{ asset('/js/grids.js') }}"></script>
    <script>
        jQuery(function($) {
            $('.film').responsiveEqualHeightGrid();
        });



    </script>
@stop