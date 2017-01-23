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
                    @if($tvProgram->film->fsk)
                        <strong>FSK:</strong>
                        {{$tvProgram->film->fsk}}<br>
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
                            <a href="{{route('seriesview', ['genre' => strtolower($genre)])}}">{{$genre}}</a>,
                        @else
                            <a href="{{route('filmview', ['genre' => strtolower($genre)])}}">{{$genre}}</a>,
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
                        <a id="amazon-link" href="{{urldecode($tvProgram->film->amazon_link)}}" target="_blank"
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
                <div class="text-justify description clearfix">
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
                @include('tvprogram.download', ['otrkeyFiles' => $tvProgram->otrkeyFiles, 'token' => $token])
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
            <hr>
            @include('tvprogram.disqus', ['url' => url('tvprogram/show',['id' => $tvProgram->id]), 'identifier' => $tvProgram->id])
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
                <a class="btn btn-default" href="#disqus_thread">
                    <i class="glyphicon glyphicon-comment"></i> <span class="disqus-comment-count" data-disqus-identifier="{{$tvProgram->id}}">Kommentare</span>
                </a>
            </div>
            <br>
            @include('tvprogram.internet_search', ['tvProgram' => $tvProgram])
            <br>
            {{-- Admin Actions --}}
            @include('tvprogram._admin_actions', ['tvProgram' => $tvProgram, 'stats' => $stats])
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

        // jDownload
        $('.jdownload-link').click(function(e) {
            var url = $(this).data('url');
            $('#jdownload-urls').val(url);
            $('#jdownload-form').submit();
            return false;
        });

        var jdownloader=false;
    </script>
    <script language="javascript" src="http://127.0.0.1:9666/jdcheck.js"></script>
    <script id="dsq-count-scr" src="//hqmirror.disqus.com/count.js" async></script>
@stop

@section('head')
    <link href="{{ asset('/js/fancybox/jquery.fancybox.min.css') }}" rel="stylesheet">
    @if($tvProgram->film && $tvProgram->film->amazon_image)
        <meta property="og:image" content="{{$tvProgram->film->imageResize(580)}}" />
        <meta property="thumbnail" content="{{$tvProgram->film->imageResize(580)}}" />
    @endif
    @if($tvProgram->otrkeyFiles->isEmpty())
        <meta name="robots" content="noindex">
    @endif
    <meta property="og:description" content="{{$tvProgram->description}}" />
    <meta property="og:title" content="{{$tvProgram->title.' '.$tvProgram->station.' '.$tvProgram->start->format('Y-m-d H:i')}}" />
    <meta property="og:type" content="video.movie" />
    <meta property="og:site_name" content="HQ-Mirror" />
    <meta property="og:url" content="{{url('tvprogram/show',['id' => $tvProgram->id])}}" />
@stop
