@extends('app')

@section('content')
    {!! Form::open(['method' => 'POST', 'url' => 'node/copy-file']) !!}

    <div class="form-group">
        {!! Form::label('file', 'File') !!}
        {!! Form::select('file', [], null, ['class' => 'form-control file-select']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('node_id', 'Node') !!}
        {!! Form::select('node_id', $nodes, null, ['class' => 'form-control']) !!}
    </div>

    {!! Form::submit('Add File', ['class' => 'btn btn-primary']) !!}

    {!! Form::close() !!}
@stop


@section('scripts')
    @parent
    <script>
        $(".file-select").select2({
            ajax: {
                url: '{{ url('node/get-files') }}',
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