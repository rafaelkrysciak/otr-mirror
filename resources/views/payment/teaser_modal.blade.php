<div class="modal fade" id="premiumTeaserModal" tabindex="-1" role="dialog" aria-labelledby="premiumTeaser">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Download startet sofort ...</h4>
            </div>
            <div class="modal-body">
                <h1>Schau dir doch mal die Vorteile eines Premium-Accounts <small><a href="{{url('payment/prepare')}}#bestellen">&raquo; Bestellen</a></small></h1>
                <hr>
                @include('payment._benefits')
            </div>
            <div class="modal-footer">
                <a class="btn btn-primary" href="{{url('payment/prepare')}}#bestellen" role="button">Premium Bestellen</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>