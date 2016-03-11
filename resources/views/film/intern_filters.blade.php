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
        'title' => 'Missing Data',
        'name' => 'missing',
        'query' => $query,
        'filters' => $filterService->getMissingDataFilter(),
        'text' => $filterService->getMissingDataText(),
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
        'title' => 'Jahr',
        'name' => 'year',
        'query' => $query,
        'filters' => $filterService->getYearFilter(),
        'text' => $filterService->getYearText(),
        'action' => $action,
    ])

</ul>
