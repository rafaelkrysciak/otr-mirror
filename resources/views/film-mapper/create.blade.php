@extends('app')

@section('content')
    <h1>Create Film Mapper</h1>
    {!! Form::model($filmMapper, ['method' => 'POST', 'url' => 'film-mapper']) !!}
    @include('film-mapper.form', ['submitBurronText'=>'Create'])
    {!! Form::close() !!}
@stop

@section('scripts')
    @include('film-mapper.javascript');
@stop