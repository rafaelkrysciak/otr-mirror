<?php namespace App\Imdb;


use Imdb\Config;

/**
 * Class Imdb
 * @package App\Imdb
 */
class Imdb extends \Imdb\Title {

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        parent::__construct('0', $config);
    }

    /**
     * get directors as array
     * @return array
     */
    public function directors()
    {
        return (array) parent::director();
    }

    /**
     * get directors as concatenated string
     * @return string
     */
    public function director()
    {
        $directors = $this->directors();
        $directorStr = "";
        if(is_array($directors)) {
            foreach($directors as $director) {
                $directorStr .= $director['name'].',';
            }
            $directorStr = trim($directorStr, ',');
        }
        return $directorStr;
    }

    /**
     * get the mpaa for a selected country
     *
     * @param string $country country name i.e. Germany
     * @return string
     */
    public function mpaaForCountry($country = 'Germany')
    {
        $mpaas = (array) $this->mpaa();
        $mpaa = null;
        if(array_key_exists($country, $mpaas)) {
            $mpaa = $mpaas[$country];
        }
        return $mpaa;
    }

    /**
     * get countries as array
     *
     * @return array
     */
    public function countries()
    {
        return (array) parent::country();
    }

    /**
     * get countries as concatenated string
     *
     * @return string
     */
    public function country()
    {
        return implode(',', $this->countries());
    }

    /**
     * get metadata as array fit for Metadata Model
     *
     * @return array
     */
    public function toMetadataArray()
    {
        $photo = $this->photo(true);
        $photo = $photo === false ? null : $photo;

        return [
            'title' => html_entity_decode($this->title()),
            'original_title' => html_entity_decode($this->orig_title()),
            'year' => $this->year(),
            'country' => $this->country(),
            'genre' => $this->genre(),
            'director' => $this->director(),
            'fsk' => $this->mpaaForCountry('Germany'),
            'imdb_id' => $this->imdbid(),
            'imdb_rating' => $this->rating(),
            'imdb_votes' => $this->votes(),
            'imdb_runtime' => $this->runtime(),
            'imdb_image' => $photo,
            'tvseries' => $this->is_serial(),
        ];
    }

    protected function rate_vote()
    {
        parent::rate_vote();
        if (preg_match('!<span[^>]*itemprop="ratingCount">([\d\.,]+)</span!i',$this->page["Title"],$match)){
            $votes = str_replace(array('.', ','), '', $match[1]);
            $this->main_votes = (int)$votes;
        }else{
            $this->main_votes = 0;
        }
    }


    public function cast()
    {
        return parent::cast($clean = true);
    }
}