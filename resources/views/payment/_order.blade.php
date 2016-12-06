<h2>Bestellen</h2>
<hr>
<a name="bestellen"></a>
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="text-center"><i class="glyphicon glyphicon-pawn"></i> Gast</h3>
            </div>
            <div class="panel-body text-center">
                <p class="lead" style="font-size:28px"><strong>Kostenlos</strong></p>
            </div>
            <ul class="list-group list-group-flush text-center">
                <li class="list-group-item">
                    <span class="glyphicon glyphicon-hourglass"></span> Langsame Geschwindigkeit
                </li>
                <li class="list-group-item">
                    <span class="glyphicon glyphicon-arrow-right"></span>&nbsp;&nbsp;Nur <strong>1</strong> Parallel Downloads pro Server
                </li>
                <li class="list-group-item">
                    <span class="glyphicon glyphicon-remove-circle"></span> Download nur bei gringer Auslastung
                </li>
                <li class="list-group-item">
                    <span class="glyphicon glyphicon-bullhorn"></span> Banner Werbung
                </li>
                <li class="list-group-item">
                    <span class="glyphicon glyphicon-hd-video"></span> <span class="glyphicon glyphicon-sd-video"></span><span class="glyphicon glyphicon-phone"></span> Zugang zu allen Formaten
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
                    <span class="glyphicon glyphicon-star"></span></span> Standard Geschwindigkeit
                </li>
                <li class="list-group-item">
                    <span class="glyphicon glyphicon-arrow-right"></span>&nbsp;&nbsp;Bis <strong>3</strong> Parallel Downloads pro Server
                </li>
                <li class="list-group-item">
                    <span class="glyphicon glyphicon-ok-circle"></span> Download nur bis standard Auslastung
                </li>
                <li class="list-group-item">
                    <span class="glyphicon glyphicon-bullhorn"></span> Banner Werbung
                </li>
                <li class="list-group-item">
                    <span class="glyphicon glyphicon-hd-video"></span> <span class="glyphicon glyphicon-sd-video"></span><span class="glyphicon glyphicon-phone"></span> Zugang zu allen Formaten
                </li>
                <li class="list-group-item">
                    <span class="glyphicon glyphicon-th-list"></span> Favoriten/Gesehen Listen
                </li>
                <li class="list-group-item">
                    <span class="glyphicon glyphicon-gift"></span> 3 Tage Premium-Zugang zum testen
                </li>
            </ul>
            @if(Auth::guest())
                <div class="panel-footer"> <a class="btn btn-lg btn-block btn-danger" href="{{url('auth/register')}}">REGISTRIEREN</a> </div>
            @endif
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="text-center"><i class="glyphicon glyphicon-king"></i> Premium User</h3>
            </div>
            <div class="panel-body text-center">
                <p class="lead" style="font-size:28px">ab <strong>1,25 €</strong><sup>*</sup> / Monat</p>
            </div>
            <ul class="list-group list-group-flush text-center">
                <li class="list-group-item">
                    <span class="glyphicon glyphicon-star"></span><span class="glyphicon glyphicon-star"></span><span class="glyphicon glyphicon-star"></span> Premium Geschwindigkeit
                </li>
                <li class="list-group-item">
                    <span class="glyphicon glyphicon-random"></span>&nbsp;&nbsp;<strong>Uneingeschränkte</strong> Parallel Downloads
                </li>
                <li class="list-group-item">
                    <span class="glyphicon glyphicon-fire"></span> Zugang zu Premium Server
                </li>
                <li class="list-group-item">
                    <span class="glyphicon glyphicon-ban-circle"></span> Bannerfrei
                </li>
                <li class="list-group-item">
                    <span class="glyphicon glyphicon-hd-video"></span> <span class="glyphicon glyphicon-sd-video"></span><span class="glyphicon glyphicon-phone"></span> Zugang zu allen Formaten
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
                    <span class="zocial youtube"></span> Zugang zu Trailer und Kritiken
                </li>
                <li class="list-group-item">
                    <span class="glyphicon glyphicon-heart-empty"></span> Warmers Zeichen der Wertschätzung
                </li>
            </ul>
            <div class="panel-footer">

                <a href="{{url('payment/purchase/1')}}" role="button" class="btn btn-success btn-lg btn-block">
                    <i class="fa fa-cc-paypal"></i> <strong>&nbsp;&nbsp;2,50 €</strong><sup>*</sup>&nbsp;&nbsp;&nbsp;&nbsp;1 Monat&nbsp;&nbsp;&nbsp;&nbsp;<small>(2,50 €/Monat)</small>
                </a>

                <a href="{{url('payment/purchase/2')}}" role="button" class="btn btn-success btn-lg btn-block">
                    <i class="fa fa-cc-paypal"></i> <strong>10,50 €</strong><sup>*</sup>&nbsp;&nbsp;&nbsp;&nbsp;6 Monate&nbsp;&nbsp;<small>(1,75 €/Monat)</small>
                </a>

                <a href="{{url('payment/purchase/3')}}" role="button" class="btn btn-success btn-lg btn-block">
                    <i class="fa fa-cc-paypal"></i> <strong>15,00 €</strong><sup>*</sup>&nbsp;&nbsp;12 Monate&nbsp;&nbsp;<small>(1,25 €/Monat)</small>
                </a>
                <button type="button" class="btn btn-info btn-lg btn-block" data-toggle="modal" data-target="#bank-transfer">
                    <i class="glyphicon glyphicon-credit-cardglyphicon glyphicon-credit-card"></i> Überweisung</small>
                </button>
            </div>
        </div>
        <p style="color:#aaa;"><sup>*</sup> Endpreis, keine Ausweisung der MwSt. nach § 19 UStG</p>
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
                    <dd>DE60 1203 0000 1039 1326 99</dd>
                    <dt>BIC</dt>
                    <dd>BYLADEM1001</dd>
                    <dt>Bank</dt>
                    <dd>DKB Berlin</dd>
                </dl>
                <div class="alert alert-danger" role="alert">
                    <strong><i class="glyphicon glyphicon-warning-sign"></i> Wichtig:</strong><br>
                    Bitte benutze als Verwendungszweck deine Email-Adresse (z.B. musterman(at)example.com), mit der du dich bei HQ-Mirror registriert hast.
                    Ansonsten kann die Zahlung nicht zugeordnet werden.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>
