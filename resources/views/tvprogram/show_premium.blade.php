@extends('app')

@section('title', $tvProgram->title.' '.$tvProgram->station.' '.$tvProgram->start->format('Y-m-d H:i'))

@section('content')
    <h1>
        <span id="film-title">{{$tvProgram->title}}</span>
        <small>
            {{$tvProgram->film->country()}} ({{$tvProgram->film->year}})
        </small>
    </h1>
    @if($tvProgram->film->original_title)
        <h5>{{$tvProgram->film->original_title}}</h5>
    @endif
    <br>
    <div class="row film">
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-5" style="overflow: hidden;">
                    @if($tvProgram->film && $tvProgram->film->amazon_image)
                        <a id="cover" href="{{$tvProgram->film->imageResize(650)}}"
                           class="fancybox">
                            <img class="img-r_esponsive center-block cover"
                                 alt="{{$tvProgram->film->title}}"
                                 src="{{$tvProgram->film->imageResize(437)}}">
                        </a>
                    @else
                        <img class="img-r_esponsive center-block cover" src="{{asset('img/default_cover.jpg')}}">
                    @endif
                </div>
                <div class="col-md-7">
                    <strong>Sender:</strong>
                    <span class="label label-default">{{$tvProgram->station}}</span>
                    ({{$tvProgram->tvstation->language}})<br>
                    <strong>Begin:</strong>
                    <i class="glyphicon glyphicon-calendar"></i> {{$tvProgram->start->format('Y-m-d')}}
                    <i class="glyphicon glyphicon-time"></i> {{$tvProgram->start->format('H:i')}}
                    <i class="glyphicon glyphicon-asterisk"></i> {{$tvProgram->length}} Minuten<br>
                    @if($tvProgram->season && $tvProgram->episode)
                        <strong>Staffel:</strong>
                        {{$tvProgram->season}}<br>
                        <strong>Folge:</strong>
                        {{$tvProgram->episode}}<br>
                    @endif

                    <strong>
                        <a href="http://www.imdb.com/title/tt{{$tvProgram->film->imdb_id}}" target="_blank">
                            <i class="glyphicon glyphicon-new-window"></i> IMDb:
                        </a>
                    </strong>
                    {{$tvProgram->film->imdb_rating}} ({{ceil($tvProgram->film->imdb_votes/1000)}}K)<br>

                    <strong>Genre:</strong>
                    @foreach($tvProgram->film->genres() as $genre)
                        @if($tvProgram->film->tvseries)
                            <a href="{{route('seriesview', ['genres[]' => strtolower($genre)])}}">{{$genre}}</a>,
                        @else
                            <a href="{{route('filmview', ['genres[]' => strtolower($genre)])}}">{{$genre}}</a>,
                        @endif
                    @endforeach

                    <h3>Stab</h3>
                    <strong>Regie:</strong>
                    @foreach($tvProgram->film->directors() as $director)
                        @if($tvProgram->film->tvseries)
                            <a href="{{route('seriesview', ['q' => $director])}}">{{$director}}</a>,
                        @else
                            <a href="{{route('filmview', ['q' => $director])}}">{{$director}}</a>,
                        @endif
                    @endforeach
                    <br><br>

                    <div class="stab">
                        <table class="table-striped" style="width: 100%;">
                            @foreach($tvProgram->film->filmStars as $filmStars)
                            <tr>
                                @if($tvProgram->film->tvseries)
                                    <td><a href="{{route('seriesview', ['q' => $filmStars->star])}}">{{$filmStars->star}}</a></td>
                                @else
                                    <td><a href="{{route('filmview', ['q' => $filmStars->star])}}">{{$filmStars->star}}</a></td>
                                @endif
                                <td class="text-right">{{$filmStars->role}}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>


                    <br><br>
                    @if($tvProgram->film && $tvProgram->film->amazon_link)
                        <a id="amazon-link" href="{{$tvProgram->film->amazon_link}}" target="_blank"
                           class="btn btn-warning">
                            <i class="zocial amazon"></i> DVD/BluRay kaufen
                        </a>
                    @else
                        <a id="amazon-link" href="http://www.amazon.de/gp/search?ie=UTF8&camp=1638&creative=6742&index=dvd&linkCode=ur2&tag=hqmi-21&keywords={!! urlencode($tvProgram->title) !!}"
                           target="_blank" class="btn btn-warning">
                            <i class="zocial amazon"></i> DVD/BluRay kaufen
                        </a>
                    @endif
                    @if($tvProgram->film && $tvProgram->film->trailer)
                        <a id="trailer" href="{{$tvProgram->film->trailerUrl()}}"
                           class="btn btn-default fancybox fancybox.iframe">
                            Trailer
                        </a>
                    @endif
                    @if($tvProgram->film && $tvProgram->film->dvdkritik)
                        <a id="review" href="{{$tvProgram->film->reviewUrl()}}"
                           class="btn btn-default fancybox fancybox.iframe">
                            Kritik
                        </a>
                    @endif
                </div>
            </div>
            @if($tvProgram->film->description)
                <h3>DVD Beschreibung:</h3>
                <div class="text-justify description">
                    {!! $tvProgram->film->description !!}
                </div>
            @endif
            @if($tvProgram->description)
                <h3>EPG Beschreibung:</h3>
                <div class="text-justify description nocontent">
                    {!! $tvProgram->description !!}
                </div>
            @endif			
			
            <br>
            @if($tvProgram->otrkeyFiles->isEmpty())
                <div class="alert alert-warning" role="alert">
                    <strong>Zu dieser Sendung sind zur Zeit keine Dateien vorhanden</strong>
                </div>
            @else
                <table class="table table-files">
                    <tr>
                        <th>Dateiname</th>
                        <th>Größe</th>
                        <th>Qualität</th>
                        <th></th>
                    </tr>
                    @foreach($tvProgram->otrkeyFiles as $file)
                        @if($file->isAvailable())
                            <tr>
                                <td class="vert-align">
                                    <table class="fixed-table">
                                        <tr>
                                            <td>{{$file->name}}</td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="vert-align nowrap">@byteToSize($file->size)</td>
                                <td class="vert-align nowrap">
                                    @if($file->quality == 'mpg.avi')
                                        <i class="glyphicon glyphicon-sd-video"></i> <strong>SD</strong>
                                    @elseif($file->quality == 'mpg.HQ.avi')
                                        <i class="glyphicon glyphicon-sd-video"></i> <strong>HQ</strong>
                                    @elseif($file->quality == 'mpg.HD.avi')
                                        <i class="glyphicon glyphicon-hd-video"></i> <strong>HD</strong>
                                    @elseif($file->quality == 'mpg.mp4')
                                        <i class="glyphicon glyphicon-phone"></i> <strong>mp4</strong>
                                    @elseif($file->quality == 'mpg.HD.ac3')
                                        <i class="glyphicon glyphicon-sound-dolby"></i> <strong>AC3</strong>
                                    @endif
                                </td>
                                <td class="vert-align nowrap">
                                    <a href="{{url('download', ['user' => Auth::user() ? Auth::user()->id:'guest', 'token' => $token[$file->id], 'filename' => $file->name])}}" class="btn btn-primary download">
                                        <i class="glyphicon glyphicon-download-alt"></i> Download
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            @endif
            <div class="row nocontent">
                @if(count($episodes) > 0)
                    <hr>
                    @include('tvprogram.tvseries_episodes', ['episodes' => $episodes, 'seriesLists' => $seriesLists, 'activeStation' => $tvProgram->station])
                @elseif(count($relatedItems) > 0)
                    <hr>
                    @include('partials.tv_programs_list', ['caption' => 'Ähnliche Sendungen','items' => $relatedItems])
                @endif
            </div>
        </div>
        <div class="col-md-3">
            @if(!$tvProgram->otrkeyFiles->isEmpty())
                @include('partials.preview', ['otrkeyFile' => $tvProgram->otrkeyFiles[0]])
            @endif
            {{-- Favorite / Seen buttons --}}
            <br><br>
            <div class="btn-group-vertical btn-group-lg center-block" role="group">
                <button type="button" class="btn btn-default add-to-list {{$tvProgram->film->belongsToUser(Auth::user()) ? 'list-active':''}}" data-list="films" data-id="{{$tvProgram->film->id}}">
                    <strong><i class="glyphicon glyphicon-heart"></i>
                        @if($tvProgram->film->tvseries)
                            Meine Serie
                        @else
                            Mein Film
                        @endif
                    </strong>
                </button>
                <button type="button" class="btn btn-default add-to-list {{$lists[\App\User::FAVORITE]}}" data-list="{{\App\User::FAVORITE}}" data-id="{{$tvProgram->id}}">
                    <strong><i class="glyphicon glyphicon-star"></i> Sendung Merken</strong>
                </button>
                <button type="button" class="btn btn-default add-to-list {{$lists[\App\User::WATCHED]}}" data-list="{{\App\User::WATCHED}}" data-id="{{$tvProgram->id}}">
                    <strong><i class="glyphicon glyphicon-ok-circle"></i> Sendung Gesehen</strong>
                </button>
            </div>
            <br>
                @include('tvprogram.internet_search', ['tvProgram' => $tvProgram])
                <br>
            {{-- Admin Actions --}}
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
                       data-method="delete" data-confirm="Are you sure?">
                        <i class="glyphicon glyphicon-remove"></i> Delete
                    </a>

                    <button type="button" class="btn @if($tvProgram->film_mapper_id) btn-primary @else btn-default @endif"
                            data-toggle="modal" data-target="#iframeModal" data-remote=""
                            @if($tvProgram->film_mapper_id)
                                data-src="{{action('FilmMapperController@edit', ['film_mapper' => $tvProgram->film_mapper_id])}}">
                            @else
                                data-src="{{action('FilmMapperController@fromTvProgram', ['tv_program_id' => $tvProgram->id])}}">
                            @endif
                        <i class="glyphicon glyphicon-link"></i> Mapper
                    </button>
                </div>
            @endif
            <br><br>
        </div>
    </div>

    @include('film-mapper.modal')
@stop

@section('scripts')
    @parent
    <script src="{{ asset('/js/fancybox/jquery.fancybox.min.js') }}"></script>
    @include('film-mapper.javascript')
    @include('partials.js-add-to-list')
    <script>
        $('.description').readmore({
            collapsedHeight: 44,
            speed: 300,
            moreLink: '<a href="#">Mehr &raquo;</a>',
            lessLink: '<a href="#">&laquo; Weniger</a>'
        });
        $('.stab').readmore({
            collapsedHeight: 160,
            speed: 300,
            moreLink: '<a href="#">Mehr &raquo;</a>',
            lessLink: '<a href="#">&laquo; Weniger</a>'
        });
        $(document).ready(function() {
            $(".fancybox").fancybox({
                width		: '95%',
                height		: '90%',
                openEffect	: 'fade',
                closeEffect	: 'fade',
                padding     : 0
            });
        });
    </script>

@stop

@section('head')
    <link href="{{ asset('/js/fancybox/jquery.fancybox.min.css') }}" rel="stylesheet">
    @if($tvProgram->film && $tvProgram->film->amazon_image)
        <meta property="og:image" content="{{$tvProgram->film->imageResize(650)}}" />
    @endif
    <meta property="og:description" content="{{$tvProgram->description}}" />
    <meta property="og:title" content="{{$tvProgram->title.' '.$tvProgram->station.' '.$tvProgram->start->format('Y-m-d H:i')}}" />
    <meta property="og:type" content="video.movie" />
    <meta property="og:site_name" content="HQ-Mirror" />
    <meta property="og:url" content="{{url('tvprogram/show',['id' => $tvProgram->id])}}?utm_source=facebook.com&utm_medium=sideshare&utm_campaign=none" />
@stop