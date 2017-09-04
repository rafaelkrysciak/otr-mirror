@extends('app')

@section('content')
    <h1>Create Payment</h1>
    {!! Form::open(['url'=>'payment/store', 'enctype' => 'multipart/form-data']) !!}

    <div class="form-group">
        {!! Form::label('user_id', 'User:') !!}
        {!! Form::select('user_id', [], null, ['class' => 'form-control user-id-select']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('product_id', 'Product:') !!}
        {!! Form::select('product_id', [1 => '1 Monat Premium',2 => '6 Monate Premium',3 => '12 Monate Premium'], null, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('transactionid', 'Transactionid:') !!}
        {!! Form::text('transactionid', null, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::submit('Create Payment', ['class' => 'btn btn-primary form-control']) !!}
    </div>

    {!! Form::close() !!}
@stop

@section('scripts')
    @parent

    <script>
        $(".user-id-select").select2({
            ajax: {
                url: '{{ url('user/select') }}',
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