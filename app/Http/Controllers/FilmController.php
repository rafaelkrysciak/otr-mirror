<?php namespace App\Http\Controllers;

use ApaiIO\ApaiIO;
use ApaiIO\Operations\Lookup;
use App\Film;
use App\Filmstar;
use App\FilmView;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Services\FilmFilterService;
use App\Services\FilmSearchService;
use App\Services\ImdbService;
use App\TvProgramsView;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\BootstrapThreePresenter;
use \Input;
use \Session;

class FilmController extends Controller
{

    function __construct()
    {
        $this->middleware('admin', ['except' => ['viewSeries', 'viewFilms', 'mySeries', 'myFilms', 'show']]);
        $this->middleware('premium', ['only' => ['viewSeries', 'viewFilms', 'mySeries', 'myFilms']]);
    }


    public function mySeries(FilmFilterService $filterService, FilmSearchService $filmSearchService)
    {
        $user = \Auth::user();

        $filmQuery = FilmView::whereNotNull('film_id')
            ->where('tvseries', '=', '1')
            ->whereIn('film_view.film_id', function($query) use ($user) {
                $query->select('film_id')->from('film_user')->where('user_id','=',$user->id);
            });

        $query = $this->filterQuery($filterService, $filmQuery, 'myseries');
        $lang = array_key_exists('language', $query) ? $query['language'] : '';
        $quality = array_key_exists('quality', $query) ? $query['quality'] : '';

        $tvPrograms = $filmQuery->get();

        return view('film.my_series', compact('tvPrograms', 'query', 'filterService', 'lang', 'quality'));
    }


    public function viewSeries(FilmFilterService $filterService, FilmSearchService $filmSearchService)
    {

        $filmQuery = FilmView::whereNotNull('film_id')
            ->where('tvseries', '=', '1');

        $query = $this->filterQuery($filterService, $filmQuery, 'series');
        $lang = array_key_exists('language', $query) ? $query['language'] : '';
        $quality = array_key_exists('quality', $query) ? $query['quality'] : '';

        if(\Request::get('q')) {
            $filmSearchService->search(\Request::get('q'), $filmQuery);
            $query['q'] = \Request::get('q');
        }

        $tvPrograms = $filmQuery->paginate(16);

        return view('film.series', compact('tvPrograms', 'query', 'filterService', 'lang', 'quality'));
    }


    public function viewFilms(FilmFilterService $filterService, FilmSearchService $filmSearchService)
    {
        $filmQuery = FilmView::whereNotNull('film_id')
            ->where('tvseries', '=', '0');


        $query = $this->filterQuery($filterService, $filmQuery, 'film');
        $lang = array_key_exists('language', $query) ? $query['language'] : '';
        $quality = array_key_exists('quality', $query) ? $query['quality'] : '';

        if(\Request::get('q')) {
            $filmSearchService->search(\Request::get('q'), $filmQuery);
            $query['q'] = \Request::get('q');
        }

        $tvPrograms = $filmQuery->paginate(32);

        return view('film.view', compact('tvPrograms', 'query', 'filterService', 'lang', 'quality'));
    }


    public function myFilms(FilmFilterService $filterService, FilmSearchService $filmSearchService)
    {
        $user = \Auth::user();

        $filmQuery = FilmView::whereNotNull('film_id')
            ->where('tvseries', '=', '0')
            ->whereIn('film_view.film_id', function($query) use ($user) {
                $query->select('film_id')->from('film_user')->where('user_id','=',$user->id);
            });

        $query = $this->filterQuery($filterService, $filmQuery, 'myfilms');
        $lang = array_key_exists('language', $query) ? $query['language'] : '';
        $quality = array_key_exists('quality', $query) ? $query['quality'] : '';

        $tvPrograms = $filmQuery->get();

        return view('film.my_films', compact('tvPrograms', 'query', 'filterService', 'lang', 'quality'));
    }


    protected function filterQuery(FilmFilterService $filterService, Builder $FilmsQuery, $type)
    {
        $filterService->setQueryBuilder($FilmsQuery)
            ->filterRating(Input::get('rating', Session::get($type . '.rating', 'all')))
            ->filterLanguage(Input::get('language', Session::get($type . '.language', 'all')))
            ->filterFsk(Input::get('fsk', Session::get($type . '.fsk', 'all')))
            ->filterYear(Input::get('year', Session::get($type . '.year', 'all')))
            ->filterGenre(Input::get('genres', Session::get($type . '.genres', [])), Input::get('genre'))
            ->filterQuality(Input::get('quality', Session::get($type . '.quality', 'all')))
            ->filterUserFilms(Input::get('my', Session::get($type . '.my', 'all')))
            ->filterMissingData(Input::get('missing', Session::get($type . '.missing', 'all')))
            ->orderBy(Input::get('orderby', Session::get($type . '.orderby', 'start')));

        $FilmsQuery->orderBy('start', 'desc');

        $query = $filterService->getQueryParameter();

        Session::remove($type . '.rating');
        Session::remove($type . '.language');
        Session::remove($type . '.fsk');
        Session::remove($type . '.year');
        Session::remove($type . '.genres');
        Session::remove($type . '.quality');
        Session::remove($type . '.my');
        Session::remove($type . '.orderby');
        Session::remove($type . '.missing');

        foreach ($query as $key => $value) {
            Session::put($type . '.' . $key, $value);
        }

        return $query;
    }


    public function searchForSelect()
    {
        $q = Input::get('q');
        $page = Input::get('page', 1);
        $perPage = 20;

        $count = Film::orWhere('title', 'like', "%$q%")
            ->orWhere('imdb_id', '=', trim($q, 't'))
            ->count();

        $data = Film::orWhere('title', 'like', "%$q%")
            ->orWhere('imdb_id', '=', trim($q, 't'))
            ->forPage($page, $perPage)
            ->get(['id', 'title', 'year', 'tvseries'])
            ->toArray();

        $films = [];
        foreach ($data as $row) {
            $text = $row['title'] . ' (' . $row['year'] . ')';
            $text .= $row['tvseries'] ? ' Series' : '';
            $films[] = [
                'id'   => $row['id'],
                'text' => $text,
            ];
        }

        if ($count == 0 and preg_match('/^tt[0-9]{7}$/', $q)) {
            $films[] = [
                'id'   => $q,
                'text' => $q . ' new entry',
            ];
            $count = 1;
        }

        return [
            'items'       => $films,
            'total_count' => $count,
        ];
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(FilmFilterService $filterService)
    {
        $filmQuery = FilmView::whereNotNull('film_id');

        $query = $this->filterQuery($filterService, $filmQuery, 'intern');

        $films = $filmQuery->paginate(32);

        return view('film.index', compact('films', 'query', 'filterService'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('film.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(ImdbService $imdbService)
    {
        $imdbId = \Input::get('imdb_id');
        $imdbId = trim($imdbId, 't');

        $film = Film::byImdbId($imdbId)->first();
        if ($film) {
            return redirect('film/' . $film->id . '/edit');
        }

        $imdbData = $imdbService->getImdbData($imdbId);
        $imdbData['imdb_last_update'] = Carbon::now();

        $film = Film::create($imdbData);

        $cast = $imdbService->cast($imdbId, 20);

        Filmstar::createCast($film, $cast);

        return redirect('film/' . $film->id . '/edit');
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $tvProgram = TvProgramsView::where('otr_epg_id','=',$id)->first();
        if($tvProgram) {
            return redirect('tvprogram/show/'.$tvProgram->tv_program_id, 301);
        } else {
            return redirect('/', 301)->withErrors(['Sendung nicht gefunden']);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $film = Film::findOrFail($id);

        $filmstars = $film->filmStars;

        while (count($filmstars) < 20) {
            $filmstars[] = [
                'star'     => '',
                'role'     => '',
                'position' => count($filmstars) - 1,
            ];
        }

        return view('film.edit', compact('film', 'filmstars'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function update($id)
    {
        $all = \Input::all();
        $all['tvseries'] = array_key_exists('tvseries', $all) ? $all['tvseries'] : 0;

        $film = Film::findOrFail($id);
        $film->update($all);

        $film->filmStars()->delete();

        $filmstars = [];
        foreach ($all['star'] as $key => $star) {
            if (empty($star)) continue;
            $filmstars[] = new Filmstar([
                'star'     => $star,
                'role'     => $all['role'][$key],
                'position' => (int)$all['position'][$key],
            ]);
        }

        $film->filmStars()->saveMany($filmstars);

        flash('Film Saved');

        return redirect('film');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $film = Film::findOrFail($id);
        $film->delete();

        flash('Film deleted');

        return redirect('film');
    }


    public function imdbData($imdbId, ImdbService $imdbService)
    {
        $imdbId = trim($imdbId, 't');

        $imdbData = $imdbService->getImdbData($imdbId);
        $imdbData['cast'] = $imdbService->cast($imdbId);

        return $imdbData;
    }


    public function refreshImdbData(ImdbService $imdbService)
    {
        set_time_limit(1200);
        $films = Film::select('films.*')
            ->join('tv_programs', 'films.id', '=', 'tv_programs.film_id')
            ->join('otrkey_files', 'otrkey_files.tv_program_id', '=', 'tv_programs.id')
            ->join('node_otrkeyfile', 'otrkey_files.id', '=', 'node_otrkeyfile.otrkeyfile_id')
            ->where('node_otrkeyfile.status', '=', 'downloaded')
            ->where('films.imdb_last_update', '<', Carbon::now()->subDays(14))
            ->groupBy('films.id')
            ->orderBy('imdb_last_update')
            ->limit(200)
            ->get();

        foreach ($films as $film) {
            try {
                $imdbData = $imdbService->getImdbData($film->imdb_id);
                $updateData = [
                    'imdb_rating'      => $imdbData['imdb_rating'],
                    'imdb_votes'       => $imdbData['imdb_votes'],
                    'imdb_last_update' => Carbon::now(),
                ];

                if(empty($film->imdb_image)) {
                    $updateData['imdb_image'] = $imdbData['imdb_image'];
                }

                if(empty($film->year)) {
                    $updateData['year'] = $imdbData['year'];
                }

                if(empty($film->country)) {
                    $updateData['country'] = $imdbData['country'];
                }

                if(empty($film->director)) {
                    $updateData['director'] = $imdbData['director'];
                }

                if(empty($film->genre)) {
                    $updateData['genre'] = $imdbData['genre'];
                }

                if(empty($film->fsk)) {
                    $updateData['fsk'] = $imdbData['fsk'];
                }
                if(empty($film->original_title)) {
                    $updateData['original_title'] = $imdbData['original_title'];
                }

                $film->update($updateData);
            } catch (\Exception $e) {
                \Log::error($e);
            }
        }

    }


    public function amazonDescription($asin, ApaiIO $apaiIO)
    {
        $lookup = new Lookup();
        $lookup->setItemId($asin);
        $lookup->setResponseGroup(['Small', 'Large']);
        $formattedResponse = $apaiIO->runOperation($lookup);

        $title = "";
        if (isset($formattedResponse['Items']['Item']['ItemAttributes']['Title'])) {
            $title = $formattedResponse['Items']['Item']['ItemAttributes']['Title'];
        }

        $reviews = [];
        if (isset($formattedResponse['Items']['Item']['EditorialReviews']['EditorialReview'])) {
            $reviews = $formattedResponse['Items']['Item']['EditorialReviews']['EditorialReview'];
        }
        if (array_key_exists('Content', $reviews)) {
            $reviews = [$reviews];
        }

        return view('film.amazon_description', compact('reviews', 'title'));
    }


    public function amazonData($asin, ApaiIO $apaiIO)
    {
        $lookup = new Lookup();
        $lookup->setItemId($asin);
        $lookup->setResponseGroup(['Small', 'Large']);
        $formattedResponse = $apaiIO->runOperation($lookup);

        $amazon_image = "";
        if (isset($formattedResponse['Items']['Item']['LargeImage'])) {
            $amazon_image = $formattedResponse['Items']['Item']['LargeImage']['URL'];
        } elseif ($formattedResponse['Items']['Item']['ImageSets']['ImageSet']['LargeImage']) {
            $amazon_image = $formattedResponse['Items']['Item']['ImageSets']['ImageSet']['LargeImage'];
        } elseif ($formattedResponse['Items']['Item']['ImageSets']['ImageSet'][0]['LargeImage']) {
            $amazon_image = $formattedResponse['Items']['Item']['ImageSets']['ImageSet'][0]['LargeImage'];
        }

        $amazon_link = "";
        if (isset($formattedResponse['Items']['Item']['DetailPageURL'])) {
            $amazon_link = $formattedResponse['Items']['Item']['DetailPageURL'];
        }

        return compact('amazon_link', 'amazon_image');
    }
}
