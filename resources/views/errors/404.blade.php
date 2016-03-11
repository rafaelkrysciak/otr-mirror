@extends('app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="error-template">
                <img src="{{asset('img/404.jpg')}}">
                <h1>Oops!</h1>
                <h2>404 Not Found</h2>
                <div class="error-details">
                    Entschuldigung, ein Fehler ist aufgetreten. Die angeforderte Seite konnte nicht gefunden werden!
                </div>
                <div class="error-actions">
                    <a href="{{url('/')}}" class="btn btn-primary btn-lg">
                        <span class="glyphicon glyphicon-home"></span> Nach Hause
                    </a>
                    <a href="{{url('contact')}}" class="btn btn-default btn-lg">
                        <span class="glyphicon glyphicon-envelope"></span> Support kontaktieren
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('head')
<style>
    .error-template {padding: 40px 15px;text-align: center;}
    .error-actions {margin-top:15px;margin-bottom:15px;}
    .error-actions .btn { margin-right:10px; }
</style>
@stop