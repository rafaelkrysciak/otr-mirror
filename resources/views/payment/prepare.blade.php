@extends('app')

@section('title', 'Premium')

@section('content')
    <div class="alert alert-danger" role="alert">
        <strong>Bitte Beachten:</strong>
        Die hier angebotene Dateien können nur zusammen mit einem Konto bei
        <a href="http://www.onlinetvrecorder.com/" target="_blank">OnlineTvRecorder</a> benutzt werden!
        Bitte beachte auch unsere <a href="{{url('faq')}}">FAQs</a>.
    </div>

    <div class="jumbotron">
        <div class="row">
            <div class="col-md-12">
                <h1><i class="glyphicon glyphicon-king"></i> Premium Angebot</h1>
                <p>
                    Mit einem Premium-Account erhältst du Zugang zu schnellen Downloads und umfassendem <a href="#filme">Film-</a> und <a href="#serien">Serienverzeichnis</a>.
                    Außerdem stehen dir noch viele <a href="#zusatzinformationen">Zusatzinformationen</a> zu ausgewählten Sendungen zur Verfügung.<br>
                </p>

                <p><a class="btn btn-danger btn-lg" href="#vorteile" role="button">Mehr erfahren</a></p>
            </div>
        </div>
    </div>
    @include('payment._order')
    <br>
    @include('payment._benefits')
@stop
