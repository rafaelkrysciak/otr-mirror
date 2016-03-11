@extends('app')

@section('title', 'Email Schreiben')

@section('content')
    {!! Form::open(['method' => 'POST', 'url' => 'mail/send']) !!}

    <div class="form-group">
        {!! Form::label('recipients_type', 'Recipient Type') !!}
        {!! Form::select('recipients_type', ['all' => 'All', 'individual' => 'Individual', 'both' => 'Both'], 'individual', ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('recipients', 'Recipients:') !!}
        {!! Form::text('recipients', null, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('subject', 'Subject:') !!}
        {!! Form::text('subject', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('body', 'Body:') !!}
        {!! Form::textarea('body', null, ['class' => 'form-control']) !!}
    </div>

    {!! Form::submit('Send Mail', ['class' => 'btn btn-primary']) !!}

    {!! Form::close() !!}
@stop

