@extends('app')

@section('content')
    <h1>Edit Promotion</h1>
    {!! Form::open(['method' => 'PATCH', 'url' => ['promotion', $promotion->id], 'enctype' => 'multipart/form-data']) !!}
    @include('promotion.form', [
            'submitButtonText' => 'Update Promotion',
            'title' => $promotion->title,
            'tvprograms' => $tvprograms,
            'search' => $promotion->search,
            'position' => $promotion->position,
            'active' => $promotion->active,
        ])
    {!! Form::close() !!}
@stop

@section('scripts')
    @parent
    @include('promotion.select_tvprogram_script')
@stop