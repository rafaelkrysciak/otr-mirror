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
        'title' => 'Qualität',
        'name' => 'quality',
        'query' => $query,
        'filters' => $filterService->getQualityFilter(),
        'text' => $filterService->getQualityText(),
        'action' => $action,
    ])

</ul>
