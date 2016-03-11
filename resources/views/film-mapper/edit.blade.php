@extends('app')

@section('content')
    <h1>Edit Film Mapper</h1>
    {!! Form::model($filmMapper, ['method' => 'PATCH', 'url' => ['film-mapper/'.$filmMapper->id], 'id' => 'film_form']) !!}
    @include('film-mapper.form', ['submitBurronText'=>'Update'])
    {!! Form::close() !!}
@stop

@section('scripts')
    @include('film-mapper.javascript');
@stop