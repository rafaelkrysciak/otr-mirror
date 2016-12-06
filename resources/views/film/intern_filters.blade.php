<ul class="nav nav-pills">
    @include('film.filter', [
        'title' => 'Sortierung',
        'name' => 'order',
        'filterService' => $filterService,
        'action' => $action,
    ])

    @include('film.filter', [
        'title' => 'Missing Data',
        'name' => 'missing',
        'filterService' => $filterService,
        'action' => $action,
    ])

    @include('film.filter', [
        'title' => 'Typ',
        'name' => 'type',
        'filterService' => $filterService,
        'action' => $action,
    ])

    @include('film.filter', [
        'title' => 'Sprache',
        'name' => 'language',
        'filterService' => $filterService,
        'action' => $action,
    ])

    @include('film.filter', [
        'title' => 'Bewertung',
        'name' => 'rating',
        'filterService' => $filterService,
        'action' => $action,
    ])

    @include('film.filter', [
        'title' => 'Jahr',
        'name' => 'year',
        'filterService' => $filterService,
        'action' => $action,
    ])

</ul>
