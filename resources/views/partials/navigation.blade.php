<ul class="nav navbar-nav">
    <li><a href="{{ url('/') }}"><i class="glyphicon glyphicon-home"></i> Home</a></li>
    <li>
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
            <i class="sosaicon sosaicon-tv"></i>  TV Program <span class="caret"></span>
        </a>
        <ul class="dropdown-menu" role="menu">
            <li><a href="{{ url('tvprogram') }}"> Alle Sprachen </a></li>
            <li><a href="{{ url('tvprogram/Deutsch') }}"> Deutsch </a></li>
            <li><a href="{{ url('tvprogram/Englisch') }}"> Englisch </a></li>
            <li><a href="{{ url('tvprogram/Spanisch') }}"> Spanisch </a></li>
        </ul>
    </li>
    @if(Auth::user() && Auth::user()->isPremium())
        <li><a href="{{ url('film/view') }}"><i class="glyphicon glyphicon-film"></i> Filme</a></li>
        <li><a href="{{ url('series/view') }}"><i class="glyphicon glyphicon-fire"></i> Serien</a></li>
    @endif
    <li><a href="{{ url('tvprogram/top100') }}"><i class="glyphicon glyphicon-sort-by-attributes-alt"></i> Top100</a></li>
    <li><a href="{{ url('tvprogram/search') }}"><i class="glyphicon glyphicon-search"></i> Suchen</a></li>
    @if(Auth::user())
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="color: #ff0000;font-weight: bold;">
                <i class="fa fa-diamond"></i> Premium -50% <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
                @if(Auth::user()->isPremium())
                    <li><a href="{{ url('payment/prepare') }}"> Verlängern </a></li>
                @else
                    <li><a href="{{ url('payment/prepare') }}"> Jetzt zugreifen </a></li>
                @endif
                <li><a href="{{ url('payment/transactions') }}"> Transaktionen </a></li>
                @if(Auth::user() && Auth::user()->isAdmin())
                    <li><a href="{{ url('payment/all-transactions') }}"> Alle Transaktionen </a></li>
                    <li><a href="{{ url('payment/verify') }}"> Verify </a></li>
                @endif
            </ul>
        </li>
    @else
        <li><a href="{{ url('payment/prepare') }}"  style="color: #ff0000;font-weight: bold;"><i class="fa fa-diamond"></i> Premium -50% </a></li>
    @endif

    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
            Service <span class="caret"></span>
        </a>
        <ul class="dropdown-menu" role="menu">
            <li><a href="{{ url('contact') }}"><i class="glyphicon glyphicon-envelope"></i> Kontakt</a></li>
            <li><a href="{{ url('faq') }}"><i class="glyphicon glyphicon-info-sign"></i> F.A.Q.</a></li>
            <li><a href="{{ url('news') }}"><i class="glyphicon glyphicon-fire"></i> News <span class="badge">1</span></a></li>
        </ul>
    </li>

    @if(Auth::user() && Auth::user()->isAdmin())
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                Node <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
                <li><a href="{{ url('node/status') }}"> Status</a></li>
                <li role="presentation" class="divider"></li>
                <li><a href="{{ url('node/add-file') }}"> Add File</a></li>
                <li><a href="{{ url('node/copy-file') }}"> Copy File</a></li>
                <li role="presentation" class="divider"></li>
                <li><a href="{{ url('node/list-downloads') }}"> List Downloads</a></li>
                <li><a href="{{ url('node/planned-downloads') }}"> Planned Downloads </a></li>
                <li role="presentation" class="divider"></li>
                <li><a href="{{ url('node/delete-plan') }}"> Delete Plan </a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                System <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
                <li><a href="{{ url('stats') }}"> Statistic </a></li>
                <li role="presentation" class="divider"></li>
                <li><a href="{{ url('film-mapper/map-all') }}"> Map TvPrograms to Films </a></li>
                <li><a href="{{ url('system/refresh-tvprogram-view') }}"> Refresh Views </a></li>
                <li role="presentation" class="divider"></li>
                <li><a href="{{ url('mail/compose') }}"> Mail compose </a></li>
                <li role="presentation" class="divider"></li>
                <li><a href="{{ url('promotion') }}"> Promotions </a></li>
                <li><a href="{{ url('promotion/create') }}"> Promotion Create </a></li>
                <li role="presentation" class="divider"></li>
                <li><a href="{{ url('film') }}"> Film Index </a></li>
                <li><a href="{{ url('film/create') }}"> Film Create </a></li>
                <li role="presentation" class="divider"></li>
                <li><a href="{{ url('film-mapper/verifier-index/de') }}"> Film Mapper Verifier </a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                <i class="glyphicon glyphicon-time"></i> Cron <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
                <li><a href="{{ url('cron/node-status') }}"> Node Status </a></li>
                <li><a href="{{ url('cron/node-start-downloads') }}"> Node Start Downloads </a></li>
                <li><a href="{{ url('cron/node-sync-files') }}"> Node Sync Files </a></li>
                <li><a href="{{ url('cron/node-delete-old-files') }}"> Node Delete Old Files </a></li>
                <li><a href="{{ url('cron/node-rebalance') }}"> Node Rebalance </a></li>
                <li><a href="{{ url('cron/read-epg-data') }}"> Read EPG Data </a></li>
                <li><a href="{{ url('cron/distro-sync-files') }}"> Distro Sync Files </a></li>
                <li><a href="{{ url('cron/aws-sync-files') }}"> AWS Sync Files </a></li>
                <li><a href="{{ url('cron/aws-delete-old-files') }}"> AWS Delete Old Files </a></li>
                <li><a href="{{ url('cron/clean-database') }}"> Clean Database </a></li>
                <li><a href="{{ url('cron/refresh-imdb-data') }}"> Refresh IMDb Data </a></li>
                <li><a href="{{ url('cron/find-mapper-rules') }}"> Find Mapper Rules </a></li>
            </ul>
        </li>
    @endif
</ul>

<ul class="nav navbar-nav navbar-right">
    @if (Auth::guest())
        <li><a href="{{ url('/auth/login') }}"><i class="glyphicon glyphicon-log-in"></i> Login</a></li>
        <li><a href="{{ url('/auth/register') }}"><i class="glyphicon glyphicon-collapse-up"></i> Registrieren</a></li>
    @else
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                @if(Auth::user()->isPremium())
                    <i class="glyphicon glyphicon-king"></i>
                @else
                    <i class="glyphicon glyphicon-knight"></i>
                @endif
                {{ Auth::user()->name }} <span class="caret"></span>
            </a>
            <ul class="dropdown-menu" role="menu">
                @if(Auth::user()->isPremium())
                    <li>
                        <a href="{{ url('/my/series') }}">
                            <i class="glyphicon glyphicon-heart"></i> Meine Serien
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/my/films') }}">
                            <i class="glyphicon glyphicon-film"></i> Meine Filme
                        </a>
                    </li>
                    <li role="presentation" class="divider"></li>
                @endif
                <li>
                    <a href="{{ url('/user-list/favorite') }}">
                        <i class="glyphicon glyphicon-star"></i> Gemerkte Sendungen
                    </a>
                </li>
                <li>
                    <a href="{{ url('/user-list/watched') }}">
                        <i class="glyphicon glyphicon-ok-circle"></i> Angeschaut
                    </a>
                </li>
                <li class="divider"></li>
                <li class="disabled">
                    <a href="#">
                        @if(Auth::user()->isPremium())
                            <i class="glyphicon glyphicon-king"></i>
                            Premium Status:<br>gültig bis {{Auth::user()->premium_valid_until->format('d.m.Y')}}
                        @else
                            <i class="glyphicon glyphicon-knight"></i>
                            Premium Status:<br>nicht aktiv
                        @endif


                    </a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="{{ url('/auth/logout') }}">
                        <i class="glyphicon glyphicon-log-out"></i> Logout
                    </a>
                </li>
            </ul>
        </li>
    @endif
</ul>