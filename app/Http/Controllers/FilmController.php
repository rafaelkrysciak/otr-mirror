<?php namespace App\Http\Controllers;

use ApaiIO\ApaiIO;
use ApaiIO\Operations\Lookup;
use App\Film;
use App\FilmFilter\FilmFilter;
use App\Filmstar;
use App\FilmView;
use App\Http\Requests;
use App\Services\FilmFilterService;
use App\Services\FilmSearchService;
use App\Services\ImdbService;
use App\TvProgramsView;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Input;
use Session;

class FilmController extends Controller
{

	function __construct()
	{
		$this->middleware('admin', ['except' => ['show']]);
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
	public function index(Request $request)
	{
		$filmQuery = FilmView::whereNotNull('film_id');

		$filter = FilmFilter::factory(['order', 'type', 'missing', 'language', 'rating', 'year', 'genre', 'quality'])
			->useSession('admin')
			->setAction((new \ReflectionClass($this))->getShortName() . '@' . __FUNCTION__)
			->readRequest($request)
			->apply($filmQuery);


		$films = $filmQuery->paginate(32);

		return view('film.index', compact('films', 'query', 'filter'));
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
		$tvProgram = TvProgramsView::where('otr_epg_id', '=', $id)->first();
		if ($tvProgram) {
			return redirect('tvprogram/show/' . $tvProgram->tv_program_id, 301);
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

				if (empty($film->imdb_image)) {
					$updateData['imdb_image'] = $imdbData['imdb_image'];
				}

				if (empty($film->year)) {
					$updateData['year'] = $imdbData['year'];
				}

				if (empty($film->country)) {
					$updateData['country'] = $imdbData['country'];
				}

				if (empty($film->director)) {
					$updateData['director'] = $imdbData['director'];
				}

				if (empty($film->genre)) {
					$updateData['genre'] = $imdbData['genre'];
				}

				if (empty($film->fsk)) {
					$updateData['fsk'] = $imdbData['fsk'];
				}
				if (empty($film->original_title)) {
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
