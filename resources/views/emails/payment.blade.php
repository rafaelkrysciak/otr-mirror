@extends('emails.main')

@section('title', 'Zahlung')

@section('content')


    @include('emails.content_block_start')
    <h1>Hallo {{$user->name}},</h1>
    @include('emails.content_block_end')

    @include('emails.content_block_start')
    Wir haben eine Zahlung von Dir erfast und gebucht. <br> Dein Premium-Status ist nun bis {{date('d.m.Y', strtotime($user->premium_valid_until))}} gültig.
    @include('emails.content_block_end')

    @include('emails.content_block_start')
    Viel Spaß beim modernen Fernsehn schauen!<br><br>
    Viele Grüße<br>
    Rafael
    @include('emails.content_block_end')
@stop