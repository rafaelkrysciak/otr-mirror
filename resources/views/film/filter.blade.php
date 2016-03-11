<li role="presentation" class="dropdown">
    <a id="drop-{{$name}}" href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
        {{$title}}: {{$text}}
        <span class="caret"></span>
    </a>
    <ul id="menu-{{$name}}" class="dropdown-menu" role="menu" aria-labelledby="drop-{{$name}}">
        @foreach($filters as $filter)
            <li role="presentation">
                <a role="menuitem" tabindex="-1" href="{{ action($action, array_merge($query, [$name=>$filter['key']])) }}">
                    @if($filter['selected'])
                        <b>{{$filter['text']}}</b>
                    @else
                        {{$filter['text']}}
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</li>
