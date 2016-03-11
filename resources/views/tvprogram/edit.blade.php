@extends('app')

@section('content')
    <h1>Edit TV-Program</h1>

    {!! Form::model($tv_program, ['method' => 'PATCH', 'url' => ['tvprogram/'.$tv_program->id], 'id' => 'film_form']) !!}

    <div class="form-group">
        {!! Form::label('title', 'Title') !!}
        {!! Form::text('title', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Boddy Form Input -->
    <div class="form-group">
        {!! Form::label('film_id', 'Film') !!}
        {!! Form::select('film_id', $films ?: [], null, ['class' => 'form-control film-select']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('station', 'Station') !!}
        {!! Form::text('station', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Boddy Form Input -->
    <div class="form-group">
        {!! Form::label('start', 'Start') !!}
        {!! Form::text('start', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Boddy Form Input -->
    <div class="form-group">
        {!! Form::label('end', 'End') !!}
        {!! Form::text('end', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Boddy Form Input -->
    <div class="form-group">
        {!! Form::label('description', 'Description') !!}
        {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Boddy Form Input -->
    <div class="form-group">
        {!! Form::label('season', 'Season') !!}
        {!! Form::text('season', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Boddy Form Input -->
    <div class="form-group">
        {!! Form::label('episode', 'Episode') !!}
        {!! Form::text('episode', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::submit('Update', ['class' => 'btn btn-primary form-control']) !!}
    </div>

    {!! Form::close() !!}
@stop


@section('scripts')

    <script>
        $(".film-select").select2({
            ajax: {
                url: '{{ url('film/search-for-select') }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2.
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 3,
            _templateResult: function(repo) {
                if (repo.loading) return repo.text;
                return repo.text;
            },
            _templateSelection: function(repo) {
                return repo.text || repo.text;
            }
        });
    </script>

@stop