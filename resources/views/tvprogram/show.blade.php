@extends('app')

@section('title', $tvProgram->title.' '.$tvProgram->station.' '.$tvProgram->start->format('Y-m-d H:i'))

@section('content')
    <h1>
        <span id="film-title">{{$tvProgram->title}}</span>
        @if($tvProgram->season && $tvProgram->episode)
            <small>(S{{str_pad($tvProgram->season, 2, '0', STR_PAD_LEFT)}}E{{str_pad($tvProgram->episode, 2, '0', STR_PAD_LEFT)}})</small>
        @endif
    </h1>
    <div class="row">
        <div class="col-md-9">
            <dl class="dl-horizontal">
                <dt>Sender:</dt>
                <dd><span class="label label-default">{{$tvProgram->station}}</span></dd>
                <dt>Sprache:</dt>
                <dd><i class="glyphicon glyphicon-flag"></i> {{$tvProgram->tvstation->language}} </dd>
                <dt>Begin:</dt>
                <dd><i class="glyphicon glyphicon-calendar"></i> {{$tvProgram->start->format('Y-m-d')}} <i class="glyphicon glyphicon-time"></i> {{$tvProgram->start->format('H:i')}}</dd>
                <dt>Dauer:</dt>
                <dd><i class="glyphicon glyphicon-asterisk"></i> {{$tvProgram->length}} Minuten</dd>
                <dt>Beschreibung:</dt>
                <dd class="text-justify"><span class="description nocontent">{{$tvProgram->description}}</span></dd>
            </dl>
            @include('partials.ad_728x90')
            <br>
            @if($tvProgram->otrkeyFiles->isEmpty())
                <div class="alert alert-warning" role="alert">
                    <strong>Zu dieser Sendung sind zur Zeit keine Dateien vorhanden</strong>
                </div>
            @else
                @if($showDownload)
                    @include('tvprogram.download', ['otrkeyFiles' => $tvProgram->otrkeyFiles, 'token' => $token])
                @else
                    @include('tvprogram.download_hidden', ['tv_program_id' => $tvProgram->id])
                @endif
            @endif
            @include('partials.ad_728x90')
            <div class="row nocontent">
            @if(count($relatedItems) > 0)
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
                @if(Auth::user())
                    <button type="button" class="btn btn-default add-to-list {{$lists[\App\User::FAVORITE]}}" data-list="{{\App\User::FAVORITE}}" data-id="{{$tvProgram->id}}">
                        <strong><i class="glyphicon glyphicon-star"></i> Merken</strong>
                    </button>
                    <button type="button" class="btn btn-default add-to-list {{$lists[\App\User::WATCHED]}}" data-list="{{\App\User::WATCHED}}" data-id="{{$tvProgram->id}}">
                        <strong><i class="glyphicon glyphicon-ok-circle"></i> Gesehen</strong>
                    </button>
                @endif
                <a class="btn btn-default" href="#disqus_thread">
                    <i class="glyphicon glyphicon-comment"></i> <span class="disqus-comment-count" data-disqus-identifier="{{$tvProgram->id}}">Kommentare</span>
                </a>
            </div>
            <br>
            @include('tvprogram.internet_search', ['tvProgram' => $tvProgram])
            <br>
            {{-- Admin Actions --}}
            @include('tvprogram._admin_actions', ['tvProgram' => $tvProgram, 'stats' => $stats])
            <br>
            <div class="row center-block">
                @include('partials.ad_160x600')
            </div>
            <br><br>
        </div>
    </div>
    @if(Auth::user() && Auth::user()->isAdmin())
        @include('film-mapper.modal')
    @endif

    @if(!Auth::user() || !Auth::user()->isPremium())
        @include('payment.teaser_modal')
    @endif
@stop

@section('head')
    <meta property="og:description" content="{{$tvProgram->description}}" />
    <meta property="description" content="{{$tvProgram->description}}" />
    <meta property="og:title" content="{{$tvProgram->title.' '.$tvProgram->station.' '.$tvProgram->start->format('Y-m-d H:i')}}" />
    <meta property="og:type" content="video.movie" />
    <meta property="og:site_name" content="HQ-Mirror" />
    <meta property="og:url" content="{{url('tvprogram/show',['id' => $tvProgram->id])}}" />
    @if($tvProgram->otrkeyFiles->isEmpty())
        <meta name="robots" content="noindex">
    @endif
@stop

@section('scripts')
    @parent
    @include('film-mapper.javascript')
    @include('partials.js-add-to-list')
    <script>
        $('.description').readmore({
            speed: 300,
            moreLink: '<a href="#">Mehr &rang;</a>',
            lessLink: '<a href="#">&lang; Weniger</a>'
        });
        $('a.download').click(function() {
            @if(!Auth::user() || !Auth::user()->isPremium())
            $('#premiumTeaserModal').modal();
            @endif
        });
        // jDownload
        $('.jdownload-link').click(function(e) {
            @if(!Auth::user() || !Auth::user()->isPremium())
                $('#premiumTeaserModal').modal();
            @endif
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