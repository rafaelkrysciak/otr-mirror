@extends('app')

@section('content')
    <h1>Film Mapper Verifier</h1>
    <ul class="nav nav-pills">
        @foreach($languages as $short => $long)
            @if($short == $language)
                <li role="presentation" class="active">
            @else
                <li role="presentation">
                    @endif
                    <a href="{{url('film-mapper/verifier-index/'.$short)}}">{{$long}}</a>
                </li>
                @endforeach
    </ul>
    @foreach($mappers as $key => $mapper)
        <hr>
        <a name="mapper{{$key}}"></a>
        <div class="row">
            <div class="col-md-2">
                @if(!empty($mapper->film->amazon_image))
                    <img style="margin: 0 10px 10px 0;" class="pull-left img-responsive" src="{{$mapper->film->amazon_image}}" width="150">
                @elseif(!empty($mapper->film->imdb_image))
                    <img style="margin: 0 10px 10px 0;" class="pull-left img-responsive" src="{{$mapper->film->imdb_image}}" width="150">
                @else
                    <img style="margin: 0 10px 10px 0;" class="pull-left img-responsive" src="{{asset('img/default_cover.jpg')}}" width="150">
                @endif
            </div>
            <div class="col-md-7">
                <h4>
                    <i class="glyphicon glyphicon-bookmark"></i> Mapper:
                    <a href="{{action('FilmMapperController@edit', ['film_mapper' => $mapper->id])}}"
                       data-toggle="modal" data-target="#iframeModal" data-remote="">
                        {{$mapper->org_title}} &raquo; {{$mapper->new_title}}
                    </a>
                    -
                    <small>
                        <a href="{{url('http://www.imdb.com/find?q='.urlencode($mapper->new_title))}}" target="_blank">
                            <i class="glyphicon glyphicon-new-window"></i> IMDb
                        </a>
                        <a href="http://www.amazon.de/gp/search?ie=UTF8&camp=1638&creative=6742&index=dvd&linkCode=ur2&tag=hqmi-21&keywords={!! urlencode($mapper->new_title) !!}" target="_blank">
                            <i class="glyphicon glyphicon-new-window"></i> Amazon 
                        </a>
                    </small>
                </h4>
                <dl class="dl-horizontal">
                    @if($mapper->year > 1900)
                        <dt>Year</dt>  <dd>{{$mapper->year}}</dd>
                    @endif
                    @if($mapper->min_length + $mapper->max_length > 0)
                        <dt>Length</dt> <dd>{{$mapper->min_length}} - {{$mapper->max_length}}</dd>
                    @endif
                    @if(!empty($mapper->channel))
                        <dt>Channel</dt> <dd>{{$mapper->channel}}</dd>
                    @endif
                    @if(!empty($mapper->director))
                        <dt>Director</dt>
                        <dd>
                            {{$mapper->director}}
                            <small>
                                <a href="{{url('http://www.imdb.com/find?q='.urlencode($mapper->director))}}" target="_blank">
                                    <i class="glyphicon glyphicon-new-window"></i> IMDb
                                </a>
                            </small>
                        </dd>
                    @endif
                </dl>
                <h4>
                    <i class="glyphicon glyphicon-film"></i> Film:
                    <a href="{{action('FilmController@edit', ['film_id' => $mapper->film->id])}}"
                            data-toggle="modal" data-target="#iframeModal" data-remote="">
                        {{$mapper->film->title}}
                    </a>
                    <small>
                        <b>{{$mapper->film->series()}}</b> {{$mapper->film->country()}} ({{$mapper->film->year}})
                        - <a href="http://www.imdb.com/title/tt{{$mapper->film->imdb_id}}" target="_blank">
                            <i class="glyphicon glyphicon-new-window"></i> IMDb
                        </a>
                        @if($mapper->film->amazon_link)
                            <a href="{{$mapper->film->amazon_link}}" target="_blank">
                                <i class="glyphicon glyphicon-new-window"></i> Amazon
                            </a>
                        @endif
                    </small>
                </h4>

                <dl class="dl-horizontal">
                    <dt>Runtime</dt>
                    <dd>{{$mapper->film->imdb_runtime}} Min.</dd>
                    <dt>Director</dt>
                    <dd>{{$mapper->film->director}}</dd>
                </dl>

                <h4><i class="sosaicon sosaicon-tv"></i> <a data-toggle="collapse" href="#collapseExample-{{$mapper->id}}">TV Programs</a></h4>
            </div>
            <div class="col-md-3">
                <div class="btn-group-vertical" role="group">
                    <button class="btn btn-default verify" data-mapper-id="{{$mapper->id}}"><i class="glyphicon glyphicon-ok"></i> Verify</button>
                    <button class="btn btn-default skip" data-mapper-id="{{$mapper->id}}"><i class="glyphicon glyphicon-share-alt"></i> Skip</button>
                    <button class="btn btn-default skip-plus-10-minutes" data-mapper-id="{{$mapper->id}}"><i class="glyphicon glyphicon-share-alt"></i> Clear & Skip +10m</button>
                    <a href="{{url('film-mapper/'.$mapper->id)}}" class="btn btn-default delete"
                       data-method="delete" data-confirm="Are you sure?" data-handler="ajax">
                        <i class="glyphicon glyphicon-trash"></i> Delete Mapper
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 collapse" id="collapseExample-{{$mapper->id}}">
                <table class="table">
                    <tr>
                        <th>Start</th>
                        <th>Title</th>
                        <th>Runtime</th>
                        <th>Year</th>
                        <th>Director</th>
                        <th>Channel</th>
                        <th>EPG</th>
                    </tr>
                    @foreach($mapper->tvPrograms->take(10) as $tvProgram)
                        <tr>
                            <td>{{$tvProgram->start->format('Y-m-d H:i')}}</td>
                            <td>
                                <a href="{{action('TvProgramController@edit', ['id'=>$tvProgram->id])}}"
                                   data-toggle="modal" data-target="#iframeModal" data-remote="">
                                    {{$tvProgram->title}}
                                </a>
                            </td>
                            <td>{{$tvProgram->length}}</td>
                            <td>{{$tvProgram->year}}</td>
                            <td>{{$tvProgram->director}}</td>
                            <td>{{$tvProgram->station}}</td>
                            <td>
                                @if($tvProgram->epgProgram)
                                    {{$tvProgram->epgProgram->title_de}}
                                    ({{$tvProgram->epgProgram->date}})
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-default btn-xs" role="button" target="_blank"
                                   href="http://www.google.com/search?q={{urlencode($tvProgram->title.' '.$tvProgram->station)}}">
                                    <i class="zocial google"></i>
                                </a>
                                <a class="btn btn-default btn-xs" role="button" target="_blank"
                                   href="https://www.fernsehserien.de/suche/{{urlencode($tvProgram->title)}}">
                                    <img src="https://www.fernsehserien.de/favicon.ico" width="21" height="21">
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    @endforeach

    <div style="height: 900px;">&nbsp;</div>
@stop

@section('scripts')
    <script>

        // Skip the film (set as verified and film_id 0)
        $('.skip').on('click', function(e) {
            var button = $(this), id = $(this).data('mapper-id');
            e.stopPropagation();
            putRequest("{{url('/film-mapper/$id$/skip')}}".replace('$id$', id), button);
        });

        $('.skip-plus-10-minutes').on('click', function(e) {
            var button = $(this), id = $(this).data('mapper-id');
            e.stopPropagation();
            putRequest("{{url('/film-mapper/$id$/skip-plus-10-minutes')}}".replace('$id$', id), button);
        });

        // verify teh mapper
        $('.verify').on('click', function(e) {
            var button = $(this), id = $(this).data('mapper-id');
            e.stopPropagation();
            putRequest("{{url('/film-mapper/$id$/verify')}}".replace('$id$', id), button);
        });

        // make a put request
        function putRequest(url, button) {
            var request;

            button.attr('disabled', 'disabled');

            request = $.ajax({
                method: "PUT",
                url: url,
                data: {_token: window.csrfToken}
            });

            request.done(function(data, status) {
                var status = data.status || '';
                var message = data.message || 'Undefended Error';

                if(status != 'OK') {
                    alert('Request failed. ('+message+')');
                    button.removeAttr('disabled');
                }
            });

            request.fail(function(request, status, error) {
                alert('Request failed. ('+error+')');
                button.removeAttr('disabled');
            });
        }


        $(window).scroll(function(e) {
            $('.collapse').each(function(index, element) {
                var $element = $(element);
                var top = $element.parent().offset().top;

                if(top > window.scrollY+100 && top < window.scrollY+450) {
                    $element.collapse('show');
                } else {
                    $element.collapse('hide');
                }

            });
        });

        $('a.delete').on('done', function(e, data) {
            if(data.status == 'OK') {
                $(e.currentTarget).parent().find('.btn').attr('disabled', 'disabled');
            } else {
                alert( "Request failed: " + (data.message || 'Unknown Error'));
            }
        }).on('fail', function(e, jqXHR, textStatus) {
            alert( "Request failed: " + textStatus );
        });

    </script>
@stop