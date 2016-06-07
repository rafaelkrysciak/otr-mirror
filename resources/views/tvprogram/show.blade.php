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
            @include('partials.ad_728x90')
            <div class="row nocontent">
            @if(count($relatedItems) > 0)
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
            @if(Auth::user())
                <br><br>
                <div class="btn-group-vertical btn-group-lg center-block" role="group">
                    <button type="button" class="btn btn-default add-to-list {{$lists[\App\User::FAVORITE]}}" data-list="{{\App\User::FAVORITE}}" data-id="{{$tvProgram->id}}">
                        <strong><i class="glyphicon glyphicon-star"></i> Merken</strong>
                    </button>
                    <button type="button" class="btn btn-default add-to-list {{$lists[\App\User::WATCHED]}}" data-list="{{\App\User::WATCHED}}" data-id="{{$tvProgram->id}}">
                        <strong><i class="glyphicon glyphicon-ok-circle"></i> Gesehen</strong>
                    </button>
                </div>
            @endif
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
                    <a href="{{url('film/'.$tvProgram->film_id.'/edit')}}" class="btn btn-default"
                       data-toggle="modal" data-target="#iframeModal" data-remote="">
                        <i class="glyphicon glyphicon-edit"></i> Film Edit
                    </a>
                    <a href="{{url('tvprogram', ['tv_program_id' => $tvProgram->id])}}" class="btn btn-danger"
                       data-method="delete" data-confirm="Are you sure?">
                        <i class="glyphicon glyphicon-remove"></i> Delete
                    </a>
                    <button type="button" class="btn @if($tvProgram->film_mapper_id) btn-primary @else btn-default @endif"
                            data-toggle="modal" data-target="#iframeModal" data-remote=""
                        @if($tvProgram->film_mapper_id)
                            data-src="{{action('FilmMapperController@edit', ['film_mapper' => $tvProgram->film_mapper_id])}}">
                        @else
                            data-src="{{url('film-mapper/create/'.$tvProgram->id)}}">
                        @endif
                        <i class="glyphicon glyphicon-link"></i> Mapper
                    </button>
                </div>
            @endif
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
    </script>
@stop