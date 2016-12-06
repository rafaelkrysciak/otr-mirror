<ul class="nav nav-pills">
    <li role="presentation"><a class="text-primary" href="{{action($filter->getAction(), ['reset' => 1])}}"><i class="glyphicon glyphicon-erase"></i> <b>Reset</b></a></li>
    @foreach($filter->getAttributes() as $attribute)
        @include('film.filter', [
            'attribute' => $attribute,
            'action' => $filter->getAction(),
            'query' => $filter->getAdditionalQueryParameter()
        ])
    @endforeach

    @if($filter->getFulltextSearch())
    <li class="">
        {!! Form::open(['method' => 'GET', 'action' => [$filter->getAction()]]) !!}
        @foreach($filter->getQueryStringArray() as $qname => $qvalue)
            @if($qname == 'q')
            @elseif(is_array($qvalue))
                @foreach($qvalue as $qvalue1)
                    <input type="hidden" name="{{$qname}}[]" value="{{$qvalue1}}">
                @endforeach
            @else
                <input type="hidden" name="{{$qname}}" value="{{$qvalue}}">
            @endif
        @endforeach
        <div class="input-group">
            {!! Form::text('q', $filter->getAdditionalQueryParameter('q'), ['class' => 'form-control', 'placeholder'=>'Suchen']) !!}
            <span class="input-group-btn">
                <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
            </span>
        </div>
        {!! Form::close() !!}
    </li>
    @endif
</ul>
