@extends('emails.main')

@section('title', 'User Contact')

@section('content')

    @include('emails.content_block_start')
    <strong>From:</strong> {{$name}} ({{$email}})
    @include('emails.content_block_end')

    @include('emails.content_block_start')
    <strong>Message:</strong><br>
    {!! nl2br(e($comment)) !!}
    @include('emails.content_block_end')
@stop