@extends('app')

@section('content')
    <h1>Liblings Filme und Serien an einem Platz <small>(05.07.2015)</small></h1>
    <br>
    <div class="row">
        <div class="col-md-6">
            <img src="{{asset('img/news/myseries_set.jpg')}}" class="img-thumbnail">
        </div>
        <div class="col-md-6 text-justify">
            <p>Ich freue mich euch ein schon öfters nachgefragtes Feature vorstellen zu können. Es ist die Möglichkeit eure Lieblings Filme und Serien zu markieren.</p>
            <p>Damit habt ihr eure Serien und Filme an einem Platz.</p>
            <p>Das Hinzufügen zu der Liste ist ganz einfach. Ein Klick auf "Mein Film" oder "Meine Serie" und schon ist es auf eure Liste.</p>
        </div>
    </div>
    <br><br>
    <div class="row">
        <div class="col-md-6 text-justify">
            <p>Alle eure Filme und Serien sind dann Zugänglich in eurem Menü.</p>
        </div>
        <div class="col-md-6">
            <img src="{{asset('img/news/myseries.jpg')}}" class="img-thumbnail">
        </div>
    </div>
    <p>Ich wünsche euch viel Spaß beim durchstöbern unseres Archivs.</p>
    <br>
    <p><strong>Rafael</strong></p>
@stop