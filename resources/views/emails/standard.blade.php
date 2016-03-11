@extends('emails.main')

@section('title', $subject)

@section('content')

    @include('emails.content_block_start')
    {!! $body !!}
    @include('emails.content_block_end')
@stop