<?php namespace App\Services;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Class FilmFilterService
 * @package App\Services
 */
class FilmFilterService
{


	/**
	 * @var Builder
	 */
	private $queryBuilder;

	/**
	 * @var array
	 */
	private $filter = [];

	/**
	 * @var array
	 */
	private $yearFilter = [
		'all'  => 'Alle',
		'2010' => '2010 - ',
		'2000' => '2000 - 2009',
		'1990' => '1990 - 1999',
		'1980' => '1980 - 1989',
		'1970' => '1970 - 1979',
		'1960' => ' - 1969',
	];

	/**
	 * @var array
	 */
	private $fskFilter = [
		'all'     => 'Alle',
		'o.Al.'   => 'o.Al.',
		'6'       => '6',
		'12'      => '12',
		'16'      => '16',
		'18'      => '18',
		'unknown' => 'Unbekannt',
	];

	/**
	 * @var array
	 */
	private $userFilmsFilter = [
		'all' => 'Alle',
		'yes' => 'Meine',
	];

	/**
	 * @var array
	 */
	private $ratingFilter = [
		'all' => 'Alle',
		'9'   => 'mindestens 9',
		'8'   => 'mindestens 8',
		'7'   => 'mindestens 7',
		'6'   => 'mindestens 6',
		'5'   => 'mindestens 5',
		'4'   => 'unter 5',
	];

	/**
	 * @var array
	 */
	private $genreFilter = [
		'all'         => 'Alle',
		'drama'       => 'Drama',
		'comedy'      => 'Comedy',
		'thriller'    => 'Thriller',
		'crime'       => 'Crime',
		'romance'     => 'Romance',
		'action'      => 'Action',
		'documentary' => 'Documentary',
		'adventure'   => 'Adventure',
		'family'      => 'Family',
		'music'       => 'Music',
		'mystery'     => 'Mystery',
		'fantasy'     => 'Fantasy',
		'sci-fi'      => 'Sci-Fi',
		'horror'      => 'Horror',
		'animation'   => 'Animation',
		'biography'   => 'Biography',
		'history'     => 'History',
		'war'         => 'War',
		'sport'       => 'Sport',
		'musical'     => 'Musical',
		'reality-tv'  => 'Reality-TV',
		'western'     => 'Western',
		'short'       => 'Short',
		'talk-show'   => 'Talk-Show',
		'game-show'   => 'Game-Show',
		'news'        => 'News',
		'adult'       => 'Adult',
	];

	/**
	 * @var array
	 */
	private $qualityFilter = [
		'all'        => 'Alle',
		'mpg.HD.avi' => 'HD',
		'mpg.HD.ac3' => 'AC3',
		'mpg.HQ.fra' => 'FRA mp3',
		'mpg.HQ.avi' => 'HQ',
		'mpg.avi'    => 'SD',
		'mpg.mp4'    => 'mp4',
	];

	/**
	 * @var array
	 */
	private $languageFilter = [
		'all'      => 'Alle',
		'deutsch'  => 'Deutsch',
		'englisch' => 'Englisch',
	];


	/**
	 * @var array
	 */
	private $orderFields = [
		'start'     => 'Kürzlich gelaufen',
		'downloads' => 'Top Downloads',
		'year'      => 'Erscheinungsjahr',
		'rating'    => 'Bewertung',
		'votes'     => 'Anazahl Bewertungen',
	];

	/**
	 * @var array
	 */
	private $missingDataFields = [
		'all'          => 'Alle',
		'amazon_asin'  => 'Amazon ASIN',
		'amazon_image' => 'Amazon Image',
		'trailer'      => 'Trailer',
		'dvdkritik'    => 'Review',
		'description'  => 'Description',
	];

	/**
	 * @var array
	 */
	private $typeFilter = [
		'all'    => 'Alle',
		'film'   => 'Filme',
		'series' => 'Serien'
	];

	/**
	 * @var array
	 */
	private $definition = [];

	/**
	 * @var array
	 */
	private $multiselect = ['genre'];


	/**
	 *
	 */
	public function __construct()
	{
		$this->definition = [
			'order'    => $this->orderFields,
			'language' => $this->languageFilter,
			'quality'  => $this->qualityFilter,
			'genre'    => $this->genreFilter,
			'rating'   => $this->ratingFilter,
			'fsk'      => $this->fskFilter,
			'year'     => $this->yearFilter,
			'missing'  => $this->missingDataFields,
			'my'       => $this->userFilmsFilter,
			'type'     => $this->typeFilter,
		];
	}


	public function requireFilters($filters)
	{
		foreach($this->definition as $name => $value)
		{
			if(!in_array($name, $filters)) {
				unset($this->definition[$name]);
			}
		}

		return $this;
	}


	/**
	 * @return array
	 */
	public function getDefinitions()
	{
		return array_keys($this->definition);
	}


	/**
	 * @param Builder $queryBuilder
	 *
	 * @return $this
	 */
	public function setQueryBuilder(Builder $queryBuilder)
	{
		$this->queryBuilder = $queryBuilder;

		return $this;
	}


	/**
	 * @param $filmId
	 *
	 * @return string
	 */
	public function getFilmUrl($filmId)
	{
		$url = 'tvprogram/film/' . $filmId;

		if (array_key_exists('language', $this->filter)) {
			$url .= '/' . $this->filter['language'];
		} else {
			$url .= '/any';
		}

		if (array_key_exists('quality', $this->filter)) {
			$url .= '/' . $this->filter['quality'];
		} else {
			$url .= '/any';
		}

		return $url;
	}


	public function saveInSession($type = 'default')
	{
		foreach($this->getDefinitions() as $name) {
			\Session::remove($type . '.' . $name);
		}

		$query = $this->getQueryParameter();

		foreach ($query as $key => $value) {
			\Session::put($type . '.' . $key, $value);
		}

		return $this;
	}


	public function restoreFromSession($type = 'default')
	{
		foreach($this->getDefinitions() as $name) {
			if(!\Session::has($type . '.' . $name)) {
				continue;
			}
			$value = \Session::get($type . '.' . $name);
			if(is_array($value)) {
				foreach ($value as $item) {
					$this->filter($name, $item, $item);
				}
			} else {
				$this->filter($name, \Session::get($type . '.' . $name), \Session::get($type . '.' . $name));
			}
		}
		return $this;
	}


	public function applyRequest(Request $request)
	{
		foreach($this->getDefinitions() as $name) {
			if($request->has($name) || $request->has('_'.$name)) {
				$this->filter($name, $request->get($name), $request->get('_'.$name));
			}
		}
		var_dump($request->all());exit;
		return $this;
	}


	/**
	 * @param array $additionParameters
	 *
	 * @return array
	 */
	public function getQueryStringArray($additionParameters = [])
	{

		return array_merge($this->filter, $additionParameters);
	}


	/**
	 * Catch getFilter and getText methods
	 * for example getYearFilter will call getFilter('year')
	 * getYearText will call getText('year')
	 *
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 */
	public function __call($name, $arguments)
	{
		$output_array = [];
		if (preg_match("/get(.*)Filter/", $name, $output_array)) {
			return call_user_func([$this, 'getFilter'], strtolower($output_array[1]));
		}

		if (preg_match("/get(.*)Text/", $name, $output_array)) {
			return call_user_func([$this, 'getText'], strtolower($output_array[1]));
		}

	}


	/**
	 * Return options of a specific filter
	 *
	 * @param $name
	 *
	 * @return array
	 */
	public function getFilter($name)
	{
		$items = [];
		foreach ($this->definition[$name] as $key => $text) {
			$item = [
				'key'      => $key,
				'text'     => $text,
				'selected' => array_key_exists($name, $this->filter) && $this->filter[$name] == $key
			];
			$items[] = $item;
		}

		return $items;
	}


	/**
	 * Return the text of a selected filter
	 * 'all' is per definition default
	 * if 'all' is not defined the text of the first option will returned
	 * in case of multi-select field the texts of all selected options will be concatenated by coma
	 *
	 * @param $name
	 *
	 * @return string
	 */
	public function getText($name)
	{
		if (!array_key_exists($name, $this->filter) || empty($this->filter[$name])) {
			if (array_key_exists('all', $this->definition[$name])) {
				return $this->definition[$name]['all'];
			} else {
				return reset($this->definition[$name]);
			}

		} elseif (in_array($name, $this->multiselect)) {
			$text = '';
			foreach ($this->filter[$name] as $key) {
				$text .= $this->definition[$name][$key] . ', ';
			}

			return trim($text, ', ');
		} else {
			return $this->definition[$name][$this->filter[$name]];
		}
	}


	/**
	 * @return array
	 */
	public function getQueryParameter()
	{
		return $this->filter;
	}


	/**
	 * Applies the filter
	 *
	 * @param $name - name of the filter
	 * @param $value - new value or current state of multiselect field
	 * @param $set - new value or value to toggle for multiselect field
	 *
	 * @return $this
	 */
	public function filter($name, $value, $set)
	{
		if (is_null($value) && is_null($set)) {
			return $this;
		}

		$method = 'filter' . ucfirst($name);

		if (method_exists($this, $method)) {
			if (in_array($name, $this->multiselect)) {
				call_user_func([$this, $method], $value, $set);
			} else {
				call_user_func([$this, $method], $set ? $set : $value);
			}
		}

		return $this;
	}


	/**
	 * @param $type
	 *
	 * @return $this
	 */
	public function filterType($type)
	{
		switch ($type) {
			case 'film':
				$this->queryBuilder->where('film_view.tvseries', '=', 0);
				break;
			case 'series':
				$this->queryBuilder->where('film_view.tvseries', '=', 1);
				break;
			default:
				return $this;
		}

		$this->filter['type'] = $type;

		return $this;
	}


	/**
	 * @param $order
	 *
	 * @return $this
	 */
	public function filterOrder($order)
	{
		switch ($order) {
			case 'year':
				$this->queryBuilder->orderBy('film_view.year', 'desc');
				break;
			case 'votes':
				$this->queryBuilder->orderBy('film_view.imdb_votes', 'desc');
				break;
			case 'rating':
				$this->queryBuilder->orderBy('film_view.imdb_rating', 'desc');
				break;
			case 'downloads':
				$this->queryBuilder->orderBy('film_view.downloads', 'desc');
				break;
			default:
			case 'start':
				$this->queryBuilder->orderBy('film_view.start', 'desc');
				break;
		}
		$this->filter['order'] = $order;

		return $this;
	}


	/**
	 * @param $rating
	 *
	 * @return $this
	 */
	public function filterRating($rating)
	{
		if ($rating == '4') {
			$this->queryBuilder->where('film_view.imdb_rating', '<', 5);
		} elseif ($rating != 'all') {
			$this->queryBuilder->where('film_view.imdb_rating', '>=', $rating);
		} else {
			return $this;
		}

		$this->filter['rating'] = $rating;

		return $this;
	}


	/**
	 * @param $quality
	 *
	 * @return $this
	 */
	public function filterQuality($quality)
	{
		if ($quality != 'all') {
			$this->queryBuilder->where('film_view.qualities', 'like', "%$quality%");
		} else {
			return $this;
		}

		$this->filter['quality'] = $quality;

		return $this;
	}


	/**
	 * @param $language
	 *
	 * @return $this
	 */
	public function filterLanguage($language)
	{
		if ($language != 'all') {
			$this->queryBuilder->where('film_view.languages', 'like', "%$language%");
		} else {
			return $this;
		}

		$this->filter['language'] = $language;

		return $this;
	}


	/**
	 * @param $year
	 *
	 * @return $this
	 */
	public function filterYear($year)
	{
		if ($year == '1960') {
			$this->queryBuilder->where('film_view.year', '<', 1970);
		} elseif ($year != 'all') {
			$this->queryBuilder->where('film_view.year', '>=', $year);
			$this->queryBuilder->where('film_view.year', '<', $year + 10);
		} else {
			return $this;
		}

		$this->filter['year'] = $year;

		return $this;
	}


	/**
	 * @param $fsk
	 *
	 * @return $this
	 */
	public function filterFsk($fsk)
	{
		if ($fsk == 'unknown') {
			$this->queryBuilder->whereNotIn('film_view.fsk', ['o.Al.', '6', '12', '16', '18']);
		} elseif ($fsk != 'all') {
			$this->queryBuilder->where('film_view.fsk', '=', $fsk);
		} else {
			return $this;
		}

		$this->filter['fsk'] = $fsk;

		return $this;
	}


	/**
	 * @param $userFilms
	 *
	 * @return $this
	 */
	public function filterMy($userFilms)
	{
		if ($userFilms == 'yes') {
			$this->queryBuilder->whereIn('film_view.film_id', function ($query) {
				$user = \Auth::user();
				$query->select('film_id')->from('film_user')->where('user_id', '=', $user->id);
			});
		} else {
			return $this;
		}

		$this->filter['my'] = $userFilms;

		return $this;
	}


	/**
	 * @param $genres
	 * @param null $genreToggle
	 *
	 * @return $this
	 */
	public function filterGenre($genres, $genreToggle = null)
	{
		$genres = (array)$genres;
		$genres = $this->toggleGenre($genres, $genreToggle);

		foreach ($genres as $genre) {
			$this->queryBuilder->where('film_view.genre', 'like', '%' . $genre . '%');
		}

		$this->filter['genre'] = $genres;

		return $this;
	}


	/**
	 * @param $genres
	 * @param $genreToggle
	 *
	 * @return array
	 */
	protected function toggleGenre($genres, $genreToggle)
	{
		if (is_null($genreToggle)) {
			return $genres;
		}

		if ($genreToggle == 'all') {
			return [];
		}

		$key = array_search($genreToggle, $genres);
		if ($key !== false) {
			unset($genres[$key]);
		} else {
			$genres[] = $genreToggle;
		}

		return $genres;
	}


	/**
	 * @param $missingField
	 *
	 * @return $this
	 */
	public function filterMissing($missingField)
	{
		if ($missingField == 'all') return $this;

		$this->queryBuilder->join('films', 'film_view.film_id', '=', 'films.id');
		$this->queryBuilder->where(function ($query) use ($missingField) {
			$query->orWhereNull('films.' . $missingField)
				->orWhere('films.' . $missingField, '=', '');
		});

		$this->filter['missing'] = $missingField;

		return $this;
	}

}