<?php namespace App\Services;

use \App\Imdb\Imdb;

/**
 * Class ImdbService
 * @package App\Services
 */
class ImdbService
{

	/**
     * @param $imdbId
     *
     * @return mixed
     */
    public function getSeriesIdIfEpisode($imdbId)
    {
        $imdb = $this->getImdbObject($imdbId);
        $episodeDetails = $imdb->get_episode_details();
        if (array_key_exists('imdbid', $episodeDetails)) {
            return $episodeDetails['imdbid'];
        } else {
            return $imdbId;
        }
    }


	/**
     * @param $imdbId
     * @param array $fields
     *
     * @return array
     * @throws \Exception
     */
    public function getImdbData($imdbId, $fields = [])
    {
        $imdb = $this->getImdbObject($imdbId);

        if (!$imdb->title()) {
            throw new \Exception("ImdbId $imdb not found");
        }

        $imdbData = $imdb->toMetadataArray();

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
        $imdb = $this->getImdbObject($imdbId);
        $imdb->photo_localurl();
    }


	/**
     * @param $imdbId
     * @param int $count
     *
     * @return array
     */
    public function cast($imdbId, $count = 30)
    {
        $imdb = $this->getImdbObject($imdbId);

        $castData = $imdb->cast($clean = true);

        $cast = [];

        foreach ($castData as $row) {
            $cast[] = ['star' => $row['name'], 'role' => $row['role']];
        }

        return array_slice($cast, 0, $count);;
    }


	/**
     * @param $q
     * @param string $language
     *
     * @return mixed
     */
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


	/**
     * @param $imdbId
     *
     * @return Imdb
     */
    protected function getImdbObject($imdbId)
    {
        if(is_a($imdbId, 'App\Imdb\Imdb')) {
            return $imdbId;
        }

        return Imdb::factory($imdbId);
    }

}