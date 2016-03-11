<?php namespace App\Services;

use \App\Imdb\Imdb;

class ImdbService
{

    /**
     * An imdb API implementation
     * @var Imdb $imdb
     */
    protected $imdb;


    /**
     * Create a new ImdbService instance
     *
     * @param Imdb $imdb
     */
    public function __construct(Imdb $imdb)
    {
        $this->imdb = $imdb;
    }


    public function getSeriesIdIfEpisode($imdbId)
    {
        $this->imdb->setid($imdbId);
        $episodeDetails = $this->imdb->get_episode_details();
        if (array_key_exists('imdbid', $episodeDetails)) {
            return $episodeDetails['imdbid'];
        } else {
            return $imdbId;
        }
    }


    public function getImdbObject($imdbId)
    {
        $this->imdb->setid($imdbId);

        return $this->imdb;
    }


    public function getImdbData($imdbId, $fields = [])
    {

        $this->imdb->setid($imdbId);

        if (!$this->imdb->title()) {
            throw new \Exception("ImdbId $imdbId not found");
        }

        $imdbData = $this->imdb->toMetadataArray();

        if (count($fields) > 0) {
            foreach ($imdbData as $field => $value) {
                if (!in_array($field, $fields)) {
                    unset($imdbData[$field]);
                }
            }
        }

        return $imdbData;
    }


    /**
     * Store Imdb Cover on the hard drive
     */
    public function savePhoto($imdbId)
    {
        $this->imdb->setid($imdbId);
        $this->imdb->photo_localurl();
    }


    public function cast($imdbId, $count = 30)
    {
        $this->imdb->setid($imdbId);
        $castData = $this->imdb->cast($clean = true);

        $cast = [];

        foreach ($castData as $row) {
            $cast[] = ['star' => $row['name'], 'role' => $row['role']];
        }

        return array_slice($cast, 0, $count);;
    }


    public function searchWithGoogle($q, $language = 'de')
    {
        $cacheKey = __METHOD__ . $q . $language;
        if (\Cache::has($cacheKey)) return \Cache::get($cacheKey);

        // ToDo: Dependency Ingection! ServiceProvider?
        $client = new \Google_Client();
        $client->setDeveloperKey(config('google.custom_search_api_key'));

        $service = new \Google_Service_Customsearch($client);

        $result = $service->cse->listCse($q, [
            'cx' => config('google.custom_search_imdb_cx'),
            'hl' => $language,
        ]);

        if ($result->getSpelling()) {
            $result = $service->cse->listCse($result->getSpelling()->getCorrectedQuery(), [
                'cx' => config('google.custom_search_imdb_cx'),
                'hl' => $language,
            ]);
        }

        /* @var $item \Google_Service_Customsearch_Result */
        foreach ($result->getItems() as $item) {
            $matches = [];
            if (preg_match('/tt([0-9]{7})/', $item->getLink(), $matches)) {
                \Cache::put($cacheKey, $matches[1], 60 * 24 * 2); // cache for 2 days
                return $matches[1];
            }
        };
    }

}