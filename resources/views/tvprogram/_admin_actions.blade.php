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
    <table class="table">
        <caption>Stats</caption>
        <tr>
            <th>Quality</th>
            <th>Downloads</th>
        </tr>
        @foreach($stats as $quality => $downloads)
            <tr>
                <td>{{$quality}}</td>
                <td>{{$downloads}}</td>
            </tr>
        @endforeach
        <tr>
            <th>Total</th>
            <th>{{array_sum($stats)}}</th>
        </tr>
    </table>
@endif
