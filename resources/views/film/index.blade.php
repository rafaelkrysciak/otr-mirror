@extends('app')

@section('content')
    <div>
        <h1>Films</h1>
        @include('film.filters', ['filter' => $filter])
    </div>

    <table class="table">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Year</th>
            <th>Rating</th>
            <th>Type</th>
            <th>Cover</th>
            <th></th>
        </tr>
    @foreach($films as $film)
        <tr>
            <td>{{$film->id}}</td>
            <td>{{$film->title}}</td>
            <td>{{$film->year}}</td>
            <td>{{$film->imdb_rating}}/{{$film->imdb_votes > 1000 ? ceil($film->imdb_votes/1000).'K' : $film->imdb_votes}}</td>
            <td>
                @if($film->tvseries == 1)
                    Series
                @else
                    Movie
                @endif
            </td>
            <td>
                @if($film->amazon_image)
                    <img width="40" src="{{$film->amazon_image}}">
                @else
                    <img width="40" src="{{asset('img/default_cover.jpg')}}">
                @endif
                @if($film->imdb_image)
                    <img width="40" src="{{$film->imdb_image}}">
                @endif
            </td>
            <td>
                <a class="btn btn-default" href="{{'film/'.$film->id.'/edit'}}"><i class="glyphicon glyphicon-pencil"></i></a>
                <a class="btn btn-default" href="{{url('film', ['film' => $film->id])}}" data-method="delete" data-confirm="Are you sure?" data-handler="form">
                    <i class="glyphicon glyphicon-trash"></i>
                </a>
                @if(in_array($filter->getAttribute('missing')->getValue(), ['trailer','dvdkritik']))
                    <a href="https://www.youtube.com/results?search_query={!! urlencode($film->title.' trailer') !!}"
                       target="_blank" class="btn btn-default"><i class="zocial youtube"></i></a>
                @endif
                @if(in_array($filter->getAttribute('missing')->getValue(), ['amazon_asin','amazon_image','description']))
                    <a href="http://www.amazon.de/gp/search?ie=UTF8&camp=1638&creative=6742&index=dvd&linkCode=ur2&tag=hqmi-21&keywords={!! urlencode($film->title) !!}"
                       target="_blank" class="btn btn-default"><i class="zocial amazon"></i></a>
                @endif
            </td>
        </tr>
    @endforeach
    </table>

    <div class="clearfix"> </div>
    {!! $films->render() !!}

@stop