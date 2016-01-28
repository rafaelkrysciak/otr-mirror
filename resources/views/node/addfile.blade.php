@extends('app')

@section('content')
    {!! Form::open(['method' => 'POST', 'url' => 'node/add-file']) !!}
    <!-- Boddy Form Input -->
    <div class="form-group">
        {!! Form::label('url', 'URL') !!}
        {!! Form::text('url', null, ['class' => 'form-control']) !!}
    </div>
    <!-- Boddy Form Input -->
    <div class="form-group">
        {!! Form::label('node_id', 'Node') !!}
        {!! Form::select('node_id', $nodes, null, ['class' => 'form-control']) !!}
    </div>

    {!! Form::submit('Add File', ['class' => 'btn btn-primary']) !!}

    {!! Form::close() !!}
@stop