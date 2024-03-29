@extends('app')

@section('content')

    @include('partials.film_nav', ['active' => $navActive])

    <h1>Filme <small><a href="{{url('my/films')}}">&raquo;Meine Filme</a></small></h1>

    @include('film.filters', [
        'query' => $query,
        'filter' => $filter,
        'action' => 'FilmCatalogueController@all',
        'fulltextSearch' => true
    ])

    <hr/>
    @foreach($tvPrograms as $key => $tvProgram)
        @if($key%4 == 0)
            @if($key != 0)
                </div>
            @endif
            <div class="row">
        @endif
                <div class="col-xs-12 col-sm-6 col-md-3">
                    <div class="panel film">
                        <a href="{{url('tvprogram/film/'.$tvProgram->film_id.'/'.($lang ?: 'any').'/'.($quality ?: 'any'))}}">
                        @if($tvProgram->amazon_image)
                            <img class="img-r_esponsive center-block" style="w_idth: 100%" src="{{$tvProgram->imageResize(335)}}">
                        @else
                            <img class="img-r_esponsive center-block" style="w_idth: 100%" src="{{asset('img/default_cover.jpg')}}">
                        @endif
                        </a>
                        <a href="{{url('tvprogram/film/'.$tvProgram->film_id.'/'.$lang)}}">
                            <h3>{{$tvProgram->title}}</h3>
                        </a>
                        <span class="nowrap"><i class="glyphicon glyphicon-globe"></i> {{$tvProgram->country()}} ({{$tvProgram->year}})</span> <span class="nowrap"><i class="glyphicon glyphicon-star"></i> Rating: {{$tvProgram->imdb_rating}} ({{(int)($tvProgram->imdb_votes/1000)}}K)</span>

                    </div>
                </div>
    @endforeach
    <div class="clearfix"> </div>
    {!! $tvPrograms->appends($query)->render() !!}
@stop

@section('scripts')
    <script src="{{ asset('/js/grids.js') }}"></script>
    <script>
        jQuery(function($) {
            $('.film').responsiveEqualHeightGrid();
        });



    </script>
@stop