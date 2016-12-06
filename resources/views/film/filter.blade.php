<li role="presentation" class="dropdown">
    <a id="drop-{{$attribute->getName()}}" href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
        {{$attribute->getTitle()}}: {{$attribute->getText()}}
        <span class="caret"></span>
    </a>
    <ul id="menu-{{$attribute->getName()}}" class="dropdown-menu" role="menu" aria-labelledby="drop-{{$attribute->getName()}}">
        @foreach($attribute->getOptions() as $option)
            <li role="presentation">
                <a role="menuitem" tabindex="-1" href="{{ action($action, $attribute->getOptionQueryStringArray($option['key'], $query)) }}">
                    @if($option['selected'])
                        <b>{{$option['text']}}</b>
                    @else
                        {{$option['text']}}
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</li>
