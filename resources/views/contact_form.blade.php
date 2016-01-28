@extends('app')

@section('title', 'Kontakt')

@section('content')
    <h1><i class="glyphicon glyphicon-envelope"></i> Kontakt</h1>
    <br>
    {!! Form::open(['method' => 'POST', 'url' => 'contact/send']) !!}
    <!-- Boddy Form Input -->
    <div class="form-group">
        {!! Form::label('name', 'Name') !!}
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('email', 'Email') !!}
        {!! Form::email('email', null, ['class' => 'form-control']) !!}
    </div>
    <!-- Boddy Form Input -->
    <div class="form-group">
        {!! Form::label('message', 'Nachricht') !!}
        {!! Form::textarea('message', null, ['class' => 'form-control']) !!}
    </div>

    {!! Form::submit('Senden', ['class' => 'btn btn-primary']) !!}

    {!! Form::close() !!}
@stop

