<?php namespace App\Services;


use Illuminate\Database\Eloquent\Builder;

/**
 * Class FilmFilterService
 * @package App\Services
 */
class FilmFilterService
{


    /**
     * @var
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

    private $userFilmsFilter = [
        'all' => 'Alle',
        'yes'  => 'Meine',
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
        'all'          => 'All',
        'amazon_asin'  => 'Amazon ASIN',
        'amazon_image' => 'Amazon Image',
        'trailer'      => 'Trailer',
        'dvdkritik'    => 'Review',
        'description'  => 'Description',
    ];

    /**
     * @var
     */
    private $order;


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
     * @return array
     */
    public function getGenreFilter()
    {
        $genres = [];
        foreach ($this->genreFilter as $genreKey => $genreText) {
            $item = [
                'key'      => $genreKey,
                'text'     => $genreText,
                'selected' => array_key_exists('genres', $this->filter) && in_array($genreKey, $this->filter['genres']) !== false
            ];
            $genres[] = $item;
        }

        return $genres;
    }


    /**
     * @return array
     */
    public function getFskFilter()
    {
        $fsks = [];
        foreach ($this->fskFilter as $fskKey => $fskText) {
            $item = [
                'key'      => $fskKey,
                'text'     => $fskText,
                'selected' => array_key_exists('fsk', $this->filter) && $this->filter['fsk'] == $fskKey
            ];
            $fsks[] = $item;
        }

        return $fsks;
    }

    /**
     * @return array
     */
    public function getUserFilmsFilter()
    {
        $userFilms = [];
        foreach ($this->userFilmsFilter as $key => $text) {
            $item = [
                'key'      => $key,
                'text'     => $text,
                'selected' => array_key_exists('my', $this->filter) && $this->filter['my'] == $key
            ];
            $userFilms[] = $item;
        }

        return $userFilms;
    }


    /**
     * @return array
     */
    public function getYearFilter()
    {
        $years = [];
        foreach ($this->yearFilter as $yearKey => $yearText) {
            $item = [
                'key'      => $yearKey,
                'text'     => $yearText,
                'selected' => array_key_exists('year', $this->filter) && $this->filter['year'] == $yearKey
            ];
            $years[] = $item;
        }

        return $years;
    }


    /**
     * @return array
     */
    public function getRatingFilter()
    {
        $ratings = [];
        foreach ($this->ratingFilter as $ratingKey => $ratingText) {
            $item = [
                'key'      => $ratingKey,
                'text'     => $ratingText,
                'selected' => array_key_exists('rating', $this->filter) && $this->filter['rating'] == $ratingKey
            ];
            $ratings[] = $item;
        }

        return $ratings;
    }


    /**
     * @return array
     */
    public function getQualityFilter()
    {
        $quality = [];
        foreach ($this->qualityFilter as $key => $text) {
            $item = [
                'key'      => $key,
                'text'     => $text,
                'selected' => array_key_exists('quality', $this->filter) && $this->filter['quality'] == $key
            ];
            $quality[] = $item;
        }

        return $quality;
    }


    /**
     * @return array
     */
    public function getLanguageFilter()
    {
        $language = [];
        foreach ($this->languageFilter as $key => $text) {
            $item = [
                'key'      => $key,
                'text'     => $text,
                'selected' => array_key_exists('language', $this->filter) && $this->filter['language'] == $key
            ];
            $language[] = $item;
        }

        return $language;
    }


    public function getMissingDataFilter()
    {
        $missing = [];
        foreach ($this->missingDataFields as $key => $text) {
            $item = [
                'key'      => $key,
                'text'     => $text,
                'selected' => array_key_exists('missing', $this->filter) && $this->filter['missing'] == $key
            ];
            $missing[] = $item;
        }

        return $missing;
    }


    /**
     * @return string
     */
    public function getGenreText()
    {
        if (!array_key_exists('genres', $this->filter) || empty($this->filter['genres'])) {
            return $this->genreFilter['all'];
        } else {
            $genreText = '';
            foreach ($this->filter['genres'] as $genre) {
                $genreText .= $this->genreFilter[$genre] . ', ';
            }

            return trim($genreText, ', ');
        }
    }


    /**
     * @return mixed
     */
    public function getFskText()
    {
        if (!array_key_exists('fsk', $this->filter)) {
            return $this->fskFilter['all'];
        } else {
            return $this->fskFilter[$this->filter['fsk']];
        }
    }

    /**
     * @return mixed
     */
    public function getUserFilmsText()
    {
        if (!array_key_exists('my', $this->filter)) {
            return $this->userFilmsFilter['all'];
        } else {
            return $this->userFilmsFilter[$this->filter['my']];
        }
    }

    /**
     * @return mixed
     */
    public function getYearText()
    {
        if (!array_key_exists('year', $this->filter)) {
            return $this->yearFilter['all'];
        } else {
            return $this->yearFilter[$this->filter['year']];
        }
    }


    /**
     * @return mixed
     */
    public function getRatingText()
    {
        if (!array_key_exists('rating', $this->filter)) {
            return $this->ratingFilter['all'];
        } else {
            return $this->ratingFilter[$this->filter['rating']];
        }
    }


    /**
     * @return mixed
     */
    public function getQualityText()
    {
        if (!array_key_exists('quality', $this->filter)) {
            return $this->qualityFilter['all'];
        } else {
            return $this->qualityFilter[$this->filter['quality']];
        }
    }


    /**
     * @return mixed
     */
    public function getLanguageText()
    {
        if (!array_key_exists('language', $this->filter)) {
            return $this->languageFilter['all'];
        } else {
            return $this->languageFilter[$this->filter['language']];
        }
    }


    public function getMissingDataText()
    {
        if (!array_key_exists('missing', $this->filter)) {
            return $this->missingDataFields['all'];
        } else {
            return $this->missingDataFields[$this->filter['missing']];
        }
    }


    /**
     * @return array
     */
    public function getQueryParameter()
    {
        $params = $this->filter;
        if (!empty($this->order)) {
            $params['orderby'] = $this->order;
        }

        return $params;
    }


    /**
     * @param $order
     */
    public function orderBy($order)
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
        $this->order = $order;
    }


    public function getOrderByFields()
    {
        $fields = [];
        foreach ($this->orderFields as $key => $text) {
            $item = [
                'key'      => $key,
                'text'     => $text,
                'selected' => $this->order == $key,
            ];
            $fields[] = $item;
        }

        return $fields;
    }


    public function getOrderByText()
    {
        if (empty($this->order)) {
            return '';
        }

        return $this->orderFields[$this->order];
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


    public function filterUserFilms($userFilms)
    {
        if ($userFilms == 'yes') {
            $this->queryBuilder->whereIn('film_view.film_id', function($query) {
                $user = \Auth::user();
                $query->select('film_id')->from('film_user')->where('user_id','=',$user->id);
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

        $genres = $this->toggleGenre($genres, $genreToggle);

        foreach ($genres as $genre) {
            $this->queryBuilder->where('film_view.genre', 'like', '%' . $genre . '%');
        }

        $this->filter['genres'] = $genres;

        return $this;
    }


    public function filterMissingData($missingField)
    {
        if($missingField == 'all') return $this;

        $this->queryBuilder->join('films', 'film_view.film_id','=','films.id');
        $this->queryBuilder->where(function($query) use ($missingField) {
            $query->orWhereNull('films.'.$missingField)
                ->orWhere('films.'.$missingField,'=','');
        });

        $this->filter['missing'] = $missingField;

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

}