@extends('app')

@section('content')
    <h1>Create Promotion</h1>
    {!! Form::open(['url'=>'promotion', 'enctype' => 'multipart/form-data']) !!}
    @include('promotion.form', [
        'submitButtonText' => 'Create Promotion',
        'title' => '',
        'tvprograms' => [],
        'search' => '',
        'position' => '100',
        'active' => 1,
    ])

    {!! Form::close() !!}
@stop

@section('scripts')
    @parent
    @include('promotion.select_tvprogram_script')
@stop