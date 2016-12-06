<ul id="film-series-navigation" class="nav nav-tabs">
    <li role="presentation" class="{{Request::is($type.'/alle') ? 'active':''}}">
        <a href="{{url('/'.$type.'/alle')}}">Alle {{ucfirst($type)}}</a>
    </li>
    <li role="presentation" class="{{Request::is($type.'/meine') ? 'active':''}}">
        <a href="{{url('/'.$type.'/meine')}}" class="text-uppercase"><strong><span class="glyphicon glyphicon-pushpin"></span> Meine {{ucfirst($type)}}</strong></a>
    </li>
    <li role="presentation" class="{{Request::is($type.'/blockbuster') ? 'active':''}}">
        <a href="{{url('/'.$type.'/blockbuster')}}">Blockbuster</a>
    </li>
    <li role="presentation" class="{{Request::is($type.'/geheimtipp') ? 'active':''}}">
        <a href="{{url('/'.$type.'/geheimtipp')}}">Geheimtipp</a>
    </li>
    <li role="presentation" class="{{Request::is($type.'/action-thriller-crime') ? 'active':''}}">
        <a href="{{url('/'.$type.'/action-thriller-crime')}}">Action/Thriller/Crime</a>
    </li>
    <li role="presentation" class="{{Request::is($type.'/drama') ? 'active':''}}">
        <a href="{{url('/'.$type.'/drama')}}">Drama</a>
    </li>
    <li role="presentation" class="{{Request::is($type.'/komoedie') ? 'active':''}}">
        <a href="{{url('/'.$type.'/komoedie')}}">Komödie</a>
    </li>
    <li role="presentation" class="{{Request::is($type.'/sci-fi-fantasy') ? 'active':''}}">
        <a href="{{url('/'.$type.'/sci-fi-fantasy')}}">Sci-Fi/Fantasy</a>
    </li>
    <li role="presentation" class="{{Request::is($type.'/animation-familie') ? 'active':''}}">
        <a href="{{url('/'.$type.'/animation-familie')}}">Animation/Familie</a>
    </li>
    <li role="presentation" class="dropdown {{Request::is($type.'/horror-mystery') || Request::is($type.'/dokus') ? 'active':''}}">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
           aria-expanded="false">
            Mehr <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <li role="presentation" class="{{Request::is($type.'/horror-mystery') ? 'active':''}}">
                <a href="{{url('/'.$type.'/horror-mystery')}}">Horror/Mystery</a>
            </li>
            <li role="presentation" class="{{Request::is($type.'/dokus') ? 'active':''}}">
                <a href="{{url('/'.$type.'/dokus')}}">Dokus</a>
            </li>
        </ul>
    </li>
</ul>