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
                <dd class="text-justify"><span class="description">{{$tvProgram->description}}</span></dd>
            </dl>

            <br>
            @if($tvProgram->otrkeyFiles->isEmpty())
                <div class="alert alert-warning" role="alert">
                    <strong>Zu dieser Sendung sind zur Zeit keine Dateien vorhanden</strong>
                </div>
            @else
                <table class="table">
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
                                    <a href="{{url('download', ['user' => Auth::user() ? Auth::user()->id:'guest', 'token' => $token[$file->id], 'filename' => $file->name])}}" class="btn btn-primary">
                                        <i class="glyphicon glyphicon-download-alt"></i> Download
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            @endif
            <div class="row">
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
            <br><br>
            <div class="btn-group-vertical btn-group-lg center-block" role="group">
                <button type="button" class="btn btn-default add-to-list {{$lists[\App\User::FAVORITE]}}" data-list="{{\App\User::FAVORITE}}" data-id="{{$tvProgram->id}}">
                    <strong><i class="glyphicon glyphicon-star"></i> Merken</strong>
                </button>
                <button type="button" class="btn btn-default add-to-list {{$lists[\App\User::WATCHED]}}" data-list="{{\App\User::WATCHED}}" data-id="{{$tvProgram->id}}">
                    <strong><i class="glyphicon glyphicon-ok-circle"></i> Gesehen</strong>
                </button>
            </div>
            <br>
            {{-- online search --}}
            <div class="btn-group-vertical btn-group-lg center-block" role="group">
                <a href="http://www.amazon.de/gp/search?ie=UTF8&camp=1638&creative=6742&index=dvd&linkCode=ur2&tag=hqmi-21&keywords={!! urlencode($tvProgram->title) !!}"
                   target="_blank" class="btn btn-default"><i class="zocial amazon"></i> DVD/BluRay</a>
                <a href="https://www.youtube.com/results?search_query={!! urlencode($tvProgram->title.' trailer') !!}"
                   target="_blank" class="btn btn-default"><i class="zocial youtube"></i> Trailer</a>
                <a href="http://www.imdb.com/find?s=all&q={!! urlencode($tvProgram->title) !!}"
                   target="_blank" class="btn btn-default"><i class="glyphicon glyphicon-facetime-video"></i> IMDb</a>
                <a href="http://www.google.com/images?q={!! urlencode($tvProgram->title) !!}"
                   target="_blank" class="btn btn-default"><i class="zocial google"></i> Cover</a>
                @if(!$tvProgram->otrkeyFiles->isEmpty() && ($tvProgram->otrkeyFiles->first()->season || $tvProgram->length < 75))
                    <a href="http://www.fernsehserien.de/suche/{!! urlencode($tvProgram->title) !!}"
                       target="_blank" class="btn btn-default"><i class=""></i> fernsehserien.de</a>
                @endif
                @if(!$tvProgram->otrkeyFiles->isEmpty())
                    <a href="http://cutlist.at/?find_sort=urating&find_ade=desc&find_what=name&find={!! urlencode($tvProgram->otrkeyFiles->first()->getBaseName()) !!}"
                       target="_blank" class="btn btn-default"><i class="glyphicon glyphicon-scissors"></i> cutlist.at</a>
                @endif
                @if($tvProgram->otr_epg_id > 0)
                    <a href="http://www.onlinetvrecorder.com/v2/?go=download&epg_id={!! urlencode($tvProgram->otr_epg_id) !!}"
                       target="_blank" class="btn btn-default"><i class="sosaicon sosaicon-tv"></i> OnlineTvRecorder</a>
                @else
                    <a href="http://www.onlinetvrecorder.com/v2/?go=list&tab=search&title={!! urlencode($tvProgram->title) !!}"
                       target="_blank" class="btn btn-default"><i class="sosaicon sosaicon-tv"></i> OnlineTvRecorder</a>
                @endif
            </div>
            {{-- Admin Actions --}}
            @if(Auth::user() && Auth::user()->isAdmin())
                <br>
                <div class="btn-group-vertical btn-group-lg center-block" role="group">
                    <a href="{{url('tvprogram/'.$tvProgram->id.'/edit')}}" class="btn btn-default">
                        <i class="glyphicon glyphicon-edit"></i> Edit
                    </a>
                    <a href="{{url('film/'.$tvProgram->film_id.'/edit')}}" class="btn btn-default">
                        <i class="glyphicon glyphicon-edit"></i> Film Edit
                    </a>
                    <a href="{{url('tvprogram', ['tv_program_id' => $tvProgram->id])}}" class="btn btn-danger"
                       data-method="delete" data-confirm="Are you sure?">
                        <i class="glyphicon glyphicon-remove"></i> Delete
                    </a>
                    <button type="button" class="btn @if($tvProgram->film_mapper_id) btn-primary @else btn-default @endif"
                            data-toggle="modal"
                            data-target="#film-mapper-modal"
                            data-mapper-id="{{$tvProgram->film_mapper_id}}"
                            data-tv-program-id="{{$tvProgram->id}}">
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
    @include('film-mapper.javascript')
    @include('partials.js-add-to-list')
    <script>
        $('.description').readmore({
            speed: 300,
            moreLink: '<a href="#">Mehr &rang;</a>',
            lessLink: '<a href="#">&lang; Weniger</a>'
        });
    </script>
@stop