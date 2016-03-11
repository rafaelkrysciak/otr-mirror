@extends('app')

@section('content')
    {!! Form::open(['url'=>'film']) !!}
    <!-- Boddy Form Input -->
    <div class="form-group">
        {!! Form::label('imdb_id', 'IMDb ID') !!}
        {!! Form::text('imdb_id', null, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::submit('Create', ['class' => 'btn btn-primary form-control']) !!}
    </div>
    {!! Form::close() !!}
@stop