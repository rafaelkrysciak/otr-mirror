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
            <div class="col-md-8">
                <h1>Premium Angebot</h1>
                <p>
                    Mit einem Premium-Account erhältst du Zugang zu schnellen Downloads und umfassendem <a href="#filme">Film-</a> und <a href="#serien">Serienverzeichnis</a>.
                    Außerdem stehen dir noch viele <a href="#zusatzinformationen">Zusatzinformationen</a> zu ausgewählten Sendungen zur Verfügung.<br>
                </p>

                <p><a class="btn btn-danger btn-lg" href="#bestellen" role="button">Jetzt Bestellen</a></p>
            </div>
            <div class="col-md-4">
                <img src="{{asset('img/50prozent.png')}}" width="300">
            </div>
        </div>
    </div>
    <a name="vorteile"></a>
    <h2>Die Vorteile</h2>
    <hr>
    @include('payment._benefits')
    <br>
    <h2>Bestellen</h2>
    <hr>
    <a name="bestellen"></a>
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="text-center"><i class="glyphicon glyphicon-pawn"></i> Gast</h3>
                </div>
                <div class="panel-body text-center">
                    <p class="lead" style="font-size:28px"><strong>Kostenlos</strong></p>
                </div>
                <ul class="list-group list-group-flush text-center">
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-flash"></span> Langsame Geschwindigkeit
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-random"></span>&nbsp;&nbsp;Bis 2 Parallel Downloads
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-dashboard"></span> Download nur bei gringer Auslastung
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-bullhorn"></span> Banner Werbung
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-hd-video"></span> Zugang zu allen Formaten
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="text-center"><i class="glyphicon glyphicon-knight"></i> Registrierter User</h3>
                </div>
                <div class="panel-body text-center">
                    <p class="lead" style="font-size:28px"><strong>Kostenlos</strong></p>
                </div>
                <ul class="list-group list-group-flush text-center">
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-flash"></span> Standard Geschwindigkeit
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-random"></span>&nbsp;&nbsp;Bis 5 Parallel Downloads
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-dashboard"></span> Download nur bis standard Auslastung
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-bullhorn"></span> Banner Werbung
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-hd-video"></span> Zugang zu allen Formaten
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-th-list"></span> Favoriten/Gesehen Listen
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-king"></span> 3 Tage Premium-Zugang zum testen
                    </li>
                </ul>
                @if(Auth::guest())
                    <div class="panel-footer"> <a class="btn btn-lg btn-block btn-danger" href="{{url('auth/register')}}">REGISTRIEREN</a> </div>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="text-center"><i class="glyphicon glyphicon-king"></i> Premium User</h3>
                </div>
                <div class="panel-body text-center">
                    <p class="lead" style="font-size:28px">ab <strong><span style="text-decoration: line-through;">2,00 €</span></strong> <strong><span style="color: #ff0000;">1,00 €</span></strong> / Monat</p>
                </div>
                <ul class="list-group list-group-flush text-center">
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-flash"></span> Premium Geschwindigkeit
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-random"></span>&nbsp;&nbsp;Uneingeschränkte Parallel Downloads
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-cloud-download"></span> Zugang zu Premium Server
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-bullhorn"></span> Frei von Banner Werbung
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-hd-video"></span> Zugang zu allen Formaten
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-th-list"></span> Favoriten/Gesehen Listen
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-th"></span> Sortiertes Film und Serien verzeichnis
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-filter"></span> Viele Filtermöglichkeiten
                    </li>
                    <li class="list-group-item">
                        <span class="glyphicon glyphicon-plus"></span> Viele Zusatzinformationen zu Filmen und Serien<br>(Cover-Bild / Trailer / Besetzung)
                    </li>
                </ul>
                <div class="panel-footer">
                    <a href="{{url('payment/purchase/1')}}" role="button" class="btn btn-primary btn-lg btn-block disabled" style="text-decoration: line-through;">
                        <i class="fa fa-cc-paypal"></i> <strong>&nbsp;&nbsp;3,49 €</strong>&nbsp;&nbsp;&nbsp;&nbsp;1 Monat&nbsp;&nbsp;&nbsp;&nbsp;<small>(3,49 €/Monat)</small>
                    </a>

                    <a href="{{url('payment/purchase/1')}}" role="button" class="btn btn-danger btn-lg btn-block">
                        <i class="fa fa-cc-paypal"></i> <strong>&nbsp;&nbsp;1,75 €</strong>&nbsp;&nbsp;&nbsp;&nbsp;1 Monat&nbsp;&nbsp;&nbsp;&nbsp;<small>(1,75 €/Monat)</small>
                    </a>

                    <a href="{{url('payment/purchase/2')}}" role="button" class="btn btn-primary btn-lg btn-block disabled" style="text-decoration: line-through;">
                        <i class="fa fa-cc-paypal"></i> <strong>14,99 €</strong>&nbsp;&nbsp;&nbsp;&nbsp;6 Monate&nbsp;&nbsp;<small>(2,50 €/Monat)</small>
                    </a>
                    <a href="{{url('payment/purchase/2')}}" role="button" class="btn btn-danger btn-lg btn-block">
                        <i class="fa fa-cc-paypal"></i> <strong>7,50 €</strong>&nbsp;&nbsp;&nbsp;&nbsp;6 Monate&nbsp;&nbsp;<small>(1,25 €/Monat)</small>
                    </a>

                    <a href="{{url('payment/purchase/3')}}" role="button" class="btn btn-primary btn-lg btn-block disabled" style="text-decoration: line-through;">
                        <i class="fa fa-cc-paypal"></i> <strong>23,99 €</strong>&nbsp;&nbsp;12 Monate&nbsp;&nbsp;<small>(2,00 €/Monat)</small>
                    </a>
                    <a href="{{url('payment/purchase/3')}}" role="button" class="btn btn-danger btn-lg btn-block">
                        <i class="fa fa-cc-paypal"></i> <strong>12,00 €</strong>&nbsp;&nbsp;12 Monate&nbsp;&nbsp;<small>(1,00 €/Monat)</small>
                    </a>
                    <button type="button" class="btn btn-danger btn-lg btn-block" data-toggle="modal" data-target="#bank-transfer">
                        <i class="glyphicon glyphicon-credit-cardglyphicon glyphicon-credit-card"></i> Überweisung</small>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="bank-transfer" tabindex="-1" role="dialog" aria-labelledby="bank-transfer">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Bank Überweisung</h4>
                </div>
                <div class="modal-body">
                    Um per Bank-Überweisung zu bezahlen, bitte sende den Betrag, je nach gewünschten Paket, an die folgende Bankverbindung.
                    <br><br>
                    <dl class="dl-horizontal">
                        <dt>Inhaber</dt>
                        <dd>Edyta Krysciak</dd>
                        <dt>IBAN</dt>
                        <dd>DE46 1203 0000 1002 7083 84</dd>
                        <dt>BIC</dt>
                        <dd>BYLADEM1001</dd>
                        <dt>Bank</dt>
                        <dd>DKB Berlin</dd>
                    </dl>
                    <div class="alert alert-danger" role="alert">
                        <strong><i class="glyphicon glyphicon-warning-sign"></i> Wichtig:</strong><br>
                        Bitte benutze als Verwendungszweck deine Email-Adresse, mit der du dich bei HQ-Mirror registriert hast.
                        Ansonsten kann die Zahlung nicht zugeordnet werden.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Schließen</button>
                </div>
            </div>
        </div>
    </div>
@stop
