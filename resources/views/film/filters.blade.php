<ul class="nav nav-pills">
    @include('film.filter', [
        'title' => 'Sortierung',
        'name' => 'orderby',
        'query' => $query,
        'filters' => $filterService->getOrderByFields(),
        'text' => $filterService->getOrderByText(),
        'action' => $action,
    ])

    @include('film.filter', [
        'title' => 'Sprache',
        'name' => 'language',
        'query' => $query,
        'filters' => $filterService->getLanguageFilter(),
        'text' => $filterService->getLanguageText(),
        'action' => $action,
    ])

    @include('film.filter', [
        'title' => 'Bewertung',
        'name' => 'rating',
        'query' => $query,
        'filters' => $filterService->getRatingFilter(),
        'text' => $filterService->getRatingText(),
        'action' => $action,
    ])

    @include('film.filter', [
        'title' => 'FSK',
        'name' => 'fsk',
        'query' => $query,
        'filters' => $filterService->getFskFilter(),
        'text' => $filterService->getFskText(),
        'action' => $action,
    ])

    @include('film.filter', [
        'title' => 'Jahr',
        'name' => 'year',
        'query' => $query,
        'filters' => $filterService->getYearFilter(),
        'text' => $filterService->getYearText(),
        'action' => $action,
    ])

    @include('film.filter', [
        'title' => 'Genre',
        'name' => 'genre',
        'query' => $query,
        'filters' => $filterService->getGenreFilter(),
        'text' => $filterService->getGenreText(),
        'action' => $action,
    ])

    @include('film.filter', [
        'title' => 'Qualität',
        'name' => 'quality',
        'query' => $query,
        'filters' => $filterService->getQualityFilter(),
        'text' => $filterService->getQualityText(),
        'action' => $action,
    ])

    <li class="">
        {!! Form::open(['method' => 'GET', 'action' => [$action]]) !!}
        @foreach($query as $qname => $qvalue)
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
            {!! Form::text('q', array_key_exists('q', $query) ? $query['q']:'', ['class' => 'form-control', 'placeholder'=>'Suchen']) !!}
            <span class="input-group-btn">
                <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
            </span>
        </div>
        {!! Form::close() !!}
    </li>
</ul>
