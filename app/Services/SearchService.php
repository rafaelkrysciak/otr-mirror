<?php
/**
 * Created by PhpStorm.
 * User: rafael
 * Date: 19.04.2015
 * Time: 22:49
 */

namespace App\Services;


use App\TvProgram;
use App\TvProgramsView;

class SearchService {


    public function getRelated(TvProgram $tvProgram, $limit = 10)
    {
        $relatedItems = $this->query($tvProgram->title)
            ->where('tv_program_id','!=',$tvProgram->id)
            ->groupBy('tv_program_id')
            ->limit(10)
            ->get();

        return $relatedItems;
    }


    public function query($term, $score = 0)
    {
        $query = TvProgramsView::query();

        $alt = $this->alt($term);

        $query->where(function($query) use ($term, $score, $alt)
        {
            $query->orWhereRaw('MATCH (title,description,name) AGAINST  (?) > ?', [$term, $score]);
            $query->orWhereRaw('MATCH (title) AGAINST  (?) > ?', [$term, $score]);
            $query->orWhereRaw('MATCH (description) AGAINST  (?) > ?', [$term, $score]);
            $query->orWhereRaw('MATCH (name) AGAINST  (?) > ?', [$term, $score]);

            if($alt) {
                $query->orWhereRaw('MATCH (title,description,name) AGAINST  (?) > ?', [$alt, $score]);
                $query->orWhereRaw('MATCH (title) AGAINST  (?) > ?', [$alt, $score]);
                $query->orWhereRaw('MATCH (description) AGAINST  (?) > ?', [$alt, $score]);
                $query->orWhereRaw('MATCH (name) AGAINST  (?) > ?', [$alt, $score]);
            }
        });

        $query->orderByRaw('MATCH (title) AGAINST  (?) desc', [$term]);
        $query->orderByRaw('MATCH (name) AGAINST  (?) desc', [$term]);
        $query->orderByRaw('MATCH (description) AGAINST  (?) desc', [$term]);
        $query->orderBy('start', 'desc');

        if($alt) {
            $query->orderByRaw('MATCH (title) AGAINST  (?) desc', [$alt]);
            $query->orderByRaw('MATCH (name) AGAINST  (?) desc', [$alt]);
            $query->orderByRaw('MATCH (description) AGAINST  (?) desc', [$alt]);
        }


        return $query;
    }

	/**
	 * create a query for search
	 *
	 * @param $term
	 * @param string $lang
	 *
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function searchTnt($term, $lang = 'all')
	{

		$tnt = new \TeamTNT\TNTSearch\TNTSearch();

		$defaultConnection = config('database.default');
		$tnt->loadConfig([
			'driver'    => config("database.connections.$defaultConnection.driver"),
			'host'      => config("database.connections.$defaultConnection.host"),
			'database'  => config("database.connections.$defaultConnection.database"),
			'username'  => config("database.connections.$defaultConnection.username"),
			'password'  => config("database.connections.$defaultConnection.password"),
			'storage'   => storage_path()
		]);

		$tnt->selectIndex('index.tvprograms');
		$tnt->maxDocs = 500;
		//$tnt->fuzziness = true;

		$tntResult = $tnt->search($term, 5000);


		$query = \App\TvProgramsView::whereIn('tv_program_id', $tntResult['ids']);
		if($lang != 'all') {
			$query->where('language', $lang);
		}

		$items = $query->get()
			->keyBy('tv_program_id');

		$sorted = $items->sortBy(function($value, $key) use ($tntResult) {
			return array_search($key, $tntResult['ids']);
		});

		$tntResult['items'] = $sorted;

		return $sorted;
	}


    /**
     * create a query for search
     *
     * @param $term
     * @param string $lang
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function search($term, $lang = 'all')
    {
        $query = TvProgramsView::query();

        $tokens = $this->tokenize($term);
        foreach($tokens as $token) {
            $alt = $this->alt($token);
            $query->where(function($query) use ($alt, $token) {
                $query
                    ->orWhere('title', 'like', "%$token%")
                    ->orWhere('description', 'like', "%$token%")
                    ->orWhere('name', 'like', "%$token%");
                if($alt) {
                    $query
                        ->orWhere('title', 'like', "%$alt%")
                        ->orWhere('description', 'like', "%$alt%")
                        ->orWhere('name', 'like', "%$alt%");
                }
            });
        }
        $query->orderBy('start', 'desc');

        if($lang != 'all') {
            $query->where('language','=',$lang);
        }

        return $query;
    }

    protected function tokenize($term)
    {
        $words = [];
        $delim = " \n.,;-()";
        $tok = strtok($term, $delim);
        while ($tok !== false) {
            $words[] = $tok;
            $tok = strtok($delim);
        }
        return array_unique($words);
    }


    protected function alt($word)
    {
        $alt = str_ireplace(['ä','ö','ü','ß'], ['ae','oe', 'ue', 'ss'], $word);
        return $alt == $word ? null : $alt;
    }
}