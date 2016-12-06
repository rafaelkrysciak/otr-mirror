<?php

namespace App\Http\Controllers;

use App\FilmFilter\FilmFilter;
use App\FilmView;
use App\Http\Requests;
use App\Services\FilmSearchService;
use App\User;
use Illuminate\Http\Request;

abstract class CatalogueController extends Controller
{

	protected $request;
	protected $action;
	protected $series;


	function __construct(Request $request)
	{
		$this->request = $request;
		list($class, $action) = explode('@', \Route::getCurrentRoute()->getActionName());
		$this->action = $action;

		$this->middleware('auth', ['only' => 'my']);
	}


	public function my()
	{
		$user = \Auth::user();

		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->whereIn('film_view.film_id', function ($query) use ($user) {
				$query->select('film_id')->from('film_user')->where('user_id', '=', $user->id);
			});

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'genre', 'quality'],
			$filmQuery,
			'Meine'
		);
	}


	protected function generate($filters, $filmQuery, $title)
	{
		$user = \Auth::user();

		$filter = FilmFilter::factory($filters)
			->useSession('user')
			->setAction((new \ReflectionClass($this))->getShortName() . '@' . $this->action)
			->readRequest($this->request);

		if($this->request->has('reset') || is_null($user) || !$user->isPremium()) {
			$filter->reset();
		}

		$filter->apply($filmQuery);


		if(is_null($user) || !$user->isPremium()) {
			$tvPrograms = $filmQuery->limit(4)->offset(8)->get();
		} else {
			$tvPrograms = $filmQuery->paginate(32);
		}

		$type = $this->series == 1 ? 'serien' : 'filme';

		return view('film.view_catalogue', compact('tvPrograms', 'title', 'filter', 'type'));
	}


	public function all(FilmSearchService $filmSearchService, Request $request)
	{
		$user = \Auth::user();

		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series);

		$filter = FilmFilter::factory(['order', 'language', 'rating', 'fsk', 'year', 'genre', 'quality'])
			->useSession('user')
			->setAction((new \ReflectionClass($this))->getShortName() . '@' . __FUNCTION__)
			->setFulltextSearch(true)
			->setAdditionalQueryParameter(['q' => $request->get('q')])
			->readRequest($request);

		if($this->request->has('reset') || is_null($user) || !$user->isPremium()) {
			$filter->reset();
		}

		$filter->apply($filmQuery);

		if($request->has('q') && !$this->request->has('reset')) {
			$filmSearchService->search($request->get('q'), $filmQuery);
		}

		if($this->request->has('reset')) {
			$filter->reset();
		}


		if(is_null($user) || !$user->isPremium()) {
			$tvPrograms = $filmQuery->limit(4)->offset(8)->get();
		} else {
			$tvPrograms = $filmQuery->paginate(32);
		}


		$title = 'Alle '.($this->series == 0 ? 'Filme' : 'Serien');
		$type = $this->series == 1 ? 'serien' : 'filme';

		return view('film.view_catalogue', compact('tvPrograms', 'title', 'filter', 'type'));
	}


	public function blockbuster()
	{
		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->where(function ($query) {
				$query->orWhere(function ($query) {
					$query->where('imdb_rating', '>', 6)
						->where('imdb_votes', '>', 100000);
				})
					->orWhere(function ($query) {
						$query->where('imdb_rating', '>', 7)
							->where('imdb_votes', '>', 50000);
					});
			});

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'genre', 'quality'],
			$filmQuery,
			'Blockbuster'
		);
	}


	public function dokus()
	{
		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->where(function ($query) {
				$query->orWhere('genre', 'like', '%News%')
					->orWhere('genre', 'like', '%Documentary%');
			});

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'quality'],
			$filmQuery,
			'Dokumentationen'
		);
	}


	public function comedy()
	{
		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->where('imdb_votes', '>', 5000)
			->where('genre', 'like', '%comedy%');

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'quality'],
			$filmQuery,
			'Komödien'
		);
	}


	public function animation()
	{
		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->where('imdb_votes', '>', 5000)
			->Where('genre', 'like', '%animation%');

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'quality'],
			$filmQuery,
			'Animation'
		);
	}


	public function family()
	{
		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->where('imdb_votes', '>', 5000)
			->where('genre', 'like', '%family%');

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'quality'],
			$filmQuery,
			'Familie'
		);
	}


	public function animation_family()
	{
		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->where('imdb_votes', '>', 5000)
			->where(function ($query) {
				$query->orWhere('genre', 'like', '%family%')
					->orWhere('genre', 'like', '%animation%');
			});

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'quality'],
			$filmQuery,
			'Animation und Familie'
		);
	}


	public function drama()
	{
		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->where('imdb_votes', '>', 5000)
			->where('genre', 'like', '%drama%');

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'quality'],
			$filmQuery,
			'Dramen'
		);
	}


	public function horror_mystery()
	{
		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->where('imdb_votes', '>', 5000)
			->where(function ($query) {
				$query->orWhere('genre', 'like', '%horror%')
					->orWhere('genre', 'like', '%mystery%');
			});

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'quality'],
			$filmQuery,
			'Horror und Mystery'
		);
	}


	public function action()
	{
		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->where('imdb_votes', '>', 5000)
			->where('genre', 'like', '%action%');

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'quality'],
			$filmQuery,
			'Action'
		);
	}


	public function thriller()
	{
		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->where('imdb_votes', '>', 5000)
			->where('genre', 'like', '%thriller%');

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'quality'],
			$filmQuery,
			'Thriller'
		);
	}


	public function action_thriller_crime()
	{
		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->where('imdb_votes', '>', 5000)
			->where(function ($query) {
				$query->orWhere('genre', 'like', '%action%')
					->orWhere('genre', 'like', '%thriller%')
					->orWhere('genre', 'like', '%crime%');
			});

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'quality'],
			$filmQuery,
			'Thriller, Krimis und Action'
		);
	}


	public function scifi()
	{
		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->where('imdb_votes', '>', 5000)
			->where('genre', 'like', '%Sci-Fi%');

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'quality'],
			$filmQuery,
			'Since-Fiction'
		);
	}


	public function fantasy()
	{
		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->where('imdb_votes', '>', 5000)
			->where('genre', 'like', '%fantasy%');

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'quality'],
			$filmQuery,
			'Fantasy'
		);
	}


	public function scifi_fantasy()
	{
		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->where('imdb_votes', '>', 5000)
			->where(function ($query) {
				$query->orWhere('genre', 'like', '%Sci-Fi%')
					->orWhere('genre', 'like', '%fantasy%');
			});

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'quality'],
			$filmQuery,
			'Since-Fiction und Fantasy'
		);
	}


	public function genre($genre, Request $request)
	{
		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->where('imdb_votes', '>', 5000)
			->where('genre', 'like', "%$genre%");

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'quality'],
			$filmQuery,
			'Genry: ' . $genre
		);
	}


	public function insiderstip()
	{
		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->where('imdb_rating', '>=', 7)
			->where('imdb_votes', '>', 4000)
			->where('imdb_votes', '<', 50000);

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'genre', 'quality'],
			$filmQuery,
			'Geheimtipps'
		);
	}

}
