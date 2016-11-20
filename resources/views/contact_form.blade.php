@extends('app')

@section('title', 'Kontakt')

@section('content')
    <h1><i class="glyphicon glyphicon-envelope"></i> Kontakt</h1>
    <br>
    @include('partials.contact_form')
@stop

@section('scripts')
    @parent
    @if(!Auth::user())
        <script src='https://www.google.com/recaptcha/api.js'></script>
    @endif
@stop