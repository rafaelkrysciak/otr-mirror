<div class="row" style="background-color: lightgray;margin-bottom: 40px;">
    <div class="col-md-12 text-center">
        <br><br><br><br>
        <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
            Zeige Dateien
        </button>
        <br><br><br><br><br>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Weiterleitung</h4>
            </div>
            <div class="modal-body">
                Du wirst an eine Seite weitergeleitet wo eine Berechnung gemacht wird.<br>
                <img src="{{asset('/img/download_redirect.png')}}"><br>
                Nachdem die berechnung ferig gestellt worden ist, wirst du wieder an HQ-Mirror zurück geleitet, wo der Download fortgesetzt werden kann.
                <br><br>
                <strong>Übrigens:</strong> Premium Benutzer können die Dateien direkt runter laden. <a href="{{url('payment/prepare')}}">Jetzt Premium Benutzer werden</a>
            </div>
            <div class="modal-footer">
                <a href="{{url('tvprogram/view-download/'.$tv_program_id)}}" class="btn btn-primary" rel="nofollow">Verstanden</a><br>
            </div>
        </div>
    </div>
</div>