<?php namespace App\Http\Controllers;

use App\Film;
use App\FilmMapper;
use App\Filmstar;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Services\FilmMapperService;
use App\Services\ImdbService;
use App\Station;
use App\TvProgram;
use Illuminate\Http\Request;

class FilmMapperController extends Controller
{


    function __construct()
    {
        $this->middleware('admin');
    }


    public function verify($id, FilmMapperService $filmMapperService)
    {
        $filmMapper = FilmMapper::findOrFail($id);
        $filmMapper->verified = true;
        $filmMapper->save();

        $filmMapperService->map($filmMapper);

        return ['status' => 'OK'];
    }


    public function skip($id, FilmMapperService $filmMapperService)
    {
        $filmMapper = FilmMapper::findOrFail($id);
        $filmMapper->verified = true;
        $filmMapper->film_id = 0;
        $filmMapper->save();

        $filmMapperService->map($filmMapper);

        return ['status' => 'OK'];
    }


    public function fromTvProgram($tv_program_id)
    {
        $tvProgram = TvProgram::findOrFail($tv_program_id);

        return $tvProgram;
    }


    public function applyMapRules(FilmMapperService $filmMapperService)
    {
        $numRows = $filmMapperService->mapAll(true);
        flash($numRows . ' Tv-Programs mapped');

        return redirect()->back();
    }


    public function verifierIndex($language = 'de')
    {
        $mappers = FilmMapper::with('film')
            ->where('verified', '=', false)
            ->where('language', '=', $language)
            ->limit(100)
            ->get();

        $languages = Station::groupBy('language_short')
            ->orderBy('language')
            ->lists('language', 'language_short');

        return view('film-mapper.verifier_index', compact('mappers', 'languages', 'language'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($tv_program_id)
    {
        $tvProgram = TvProgram::findOrFail($tv_program_id);

        $filmMapper = new FilmMapper();
        $filmMapper->org_title = $tvProgram->org_title;
        $filmMapper->new_title = $tvProgram->org_title;
        $filmMapper->language = $tvProgram->tvstation->language_short;

        if ($tvProgram->year > 1900) {
            $filmMapper->year = $tvProgram->year;
        }

        if ($tvProgram->director) {
            $filmMapper->director = $tvProgram->director;
        }

        $languages = Station::groupBy('language_short')
            ->orderBy('language')
            ->lists('language', 'language_short');

        $films = [];

        return view('film-mapper.create', compact('filmMapper', 'films', 'languages'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param FilmMapperService $filmMapperService
     *
     * @return Response
     */
    public function store(Request $request, FilmMapperService $filmMapperService, ImdbService $imdbService)
    {

        $film_id = $request->get('film_id');
        if (preg_match('/^tt[0-9]{7}$/', $film_id)) {
            $imdbId = trim($film_id, 't');
            $film = Film::create($imdbService->getImdbData($imdbId));
            Filmstar::createCast($film, $imdbService->cast($imdbId, 20));
            $film_id = $film->id;
        }

        $mapper = FilmMapper::create([
            'org_title'  => $request->get('org_title'),
            'new_title'  => $request->get('new_title'),
            'min_length' => $request->get('min_length'),
            'max_length' => $request->get('max_length'),
            'film_id'    => $film_id,
            'language'   => $request->get('language'),
            'year'       => $request->get('year'),
            'channel'    => $request->get('channel'),
            'director'   => $request->get('director'),
            'verified'   => $request->get('verified', 0),
        ]);

        $filmMapperService->map($mapper, $request->get('overwrite') == 1);

        flash('Mapper Saved');

        return redirect('film-mapper/'.$mapper->id.'/edit');
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
        $filmMapper = FilmMapper::with('film')->findOrFail($id);

        return $filmMapper;
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
        $filmMapper = FilmMapper::findOrFail($id);
        $film = $filmMapper->film;
        $films = [
            $film->id => $film->title . ' (' . $film->year . ')' . ($film->tvseries ? ' Series' : ''),
        ];

        $languages = Station::groupBy('language_short')
            ->orderBy('language')
            ->lists('language', 'language_short');

        return view('film-mapper.edit', compact('filmMapper', 'films', 'languages'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param Request $request
     * @param FilmMapperService $filmMapperService
     *
     * @return Response
     */
    public function update($id, Request $request, FilmMapperService $filmMapperService, ImdbService $imdbService)
    {
        $mapper = FilmMapper::findOrFail($id);

        $film_id = $request->get('film_id');
        if (preg_match('/^tt[0-9]{7}$/', $film_id)) {
            $imdbId = trim($film_id, 't');
            $film = Film::create($imdbService->getImdbData($imdbId));
            Filmstar::createCast($film, $imdbService->cast($imdbId, 20));
            $film_id = $film->id;
        }

        $mapper->update([
            'new_title'  => $request->get('new_title'),
            'min_length' => $request->get('min_length'),
            'max_length' => $request->get('max_length'),
            'film_id'    => $film_id,
            'language'   => $request->get('language'),
            'year'       => $request->get('year'),
            'channel'    => $request->get('channel'),
            'director'   => $request->get('director'),
            'verified'   => $request->get('verified', 0),
        ]);
        $filmMapperService->map($mapper, $request->get('overwrite') == 1);

        flash('Mapper Saved');

        return redirect()->back();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id, FilmMapperService $filmMapperService, Request $request)
    {
        $mapper = FilmMapper::findOrFail($id);

        $filmMapperService->unmap($mapper, $mapper->verified);

        $mapper->delete();

        if ($request->ajax()) {
            return ['status' => 'OK'];
        } else {
            return redirect()->back();
        }
    }

}
