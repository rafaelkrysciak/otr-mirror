<a name="filme"></a>
<div class="row">
    <div class="col-md-6">
        <a href="{{asset('img/payment/hqm_filme_full.jpg')}}" class="fancybox">
            <img src="{{asset('img/payment/hqm_filme.jpg')}}" class="img-responsive img-thumbnail">
        </a>
    </div>
    <div class="col-md-6">
        <h2>Umfangreich Filmsuche</h2>
        Filme ganz einfach finden. Du kannst alle verfügbaren Filme nach belieben filtern.
        Es steht dir Sprache, Bewertung, FSK, das Produktionsjahr, eine lange Liste an Genres und die Qualität der Aufnahme als Filter zur Verfügung.
        Durch die Sortierung hast du die gewünschten Filme direkt auf der ersten Seite. Du kannst nach Zeitpunkt der Aufnahme sortieren, nach Top Downloads, Produktionsjahr, IMDb Bewertung oder Anzahl der Bewertungen sortieren.
        Mit der Volltextsuche findest du ganz schnell dein Lieblingstitel. Aber auch nach Schauspielern oder Regisseuren kann gesucht werden.
    </div>
</div>
<hr>
<a name="serien"></a>
<div class="row">
    <div class="col-md-6">
        <h2>Serien Archiv</h2>
        Alle Filter- und Sortiermöglichkeiten der Filmansicht können auf die Serien angewendet werden.
        Finde ganz schnell die angesagtesten Serien durch sortieren nach "Top Downloads" oder neusten Folgen durch sortieren nach "Kürzlich gelaufen".
        Über den Genre-Filter kannst du alle Serien zu dem Themen, die dich interessieren finden. z.B. Krimis, Fantasy oder Komödien.
    </div>
    <div class="col-md-6">
        <a href="{{asset('img/payment/hqm_serien_full.jpg')}}" class="fancybox">
            <img src="{{asset('img/payment/hqm_serien.jpg')}}" class="img-responsive img-thumbnail">
        </a>
    </div>
</div>
<hr>
<a name="zusatzinformationen"></a>
<div class="row">
    <div class="col-md-6">
        <a href="{{asset('img/payment/hqm_film_full.jpg')}}" class="fancybox">
            <img src="{{asset('img/payment/hqm_film.jpg')}}" class="img-responsive img-thumbnail">
        </a>
    </div>
    <div class="col-md-6 text-justify">
        <h2>Viele Zusatzinformationen</h2>
        Zu den Filmen und Serien stehen umfangreich Zusatzinformationen bereit.
        Bewertung und Anzahl der Stimmen geben den ersten Hinweis auf die Qualität des Films oder Serie.
        Genre, Name des Regisseur sowie der Schauspieler sind verlinkt um mit einem Klick weitere Filme mit gleichen Parametern zu finden.
        Zu vielen Sendungen sind Trailer und Kritik verlinkt, die Direkt auf der Seite angeschaut werden können.
        Damit kannst du ganz einfach einschätzen ob dir der Film gefällt oder nicht.
    </div>
</div>


@section('scripts')
    @parent
    <script src="{{ asset('/js/fancybox/jquery.fancybox.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".fancybox").fancybox({
                width		: '95%',
                height		: '90%',
                openEffect	: 'elastic',
                closeEffect	: 'elastic',
                padding     : 0
            });
        });
    </script>
@stop


@section('head')
    @parent
    <link href="{{ asset('/js/fancybox/jquery.fancybox.min.css') }}" rel="stylesheet">
@stop