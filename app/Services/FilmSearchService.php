<?php namespace App\Services;


use App\FilmView;

class FilmSearchService
{

    public function search($term, $query = null)
    {
        if(is_null($query)) {
            $query = FilmView::query();
        }

        $tokens = $this->tokenize($term);
        foreach($tokens as $token) {
            $alt = $this->alt($token);
            $query->where(function($query) use ($alt, $token) {
                $query
                    ->orWhere('title', 'like', "%$token%")
                    ->orWhere('original_title', 'like', "%$token%")
                    ->orWhere('actor', 'like', "%$token%")
                    ->orWhere('director', 'like', "%$token%");
                if($alt) {
                    $query
                        ->orWhere('title', 'like', "%$alt%")
                        ->orWhere('original_title', 'like', "%$alt%")
                        ->orWhere('actor', 'like', "%$alt%")
                        ->orWhere('director', 'like', "%$alt%");
                }
            });
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
        $alt = str_ireplace(['ä', 'ö', 'ü', 'ß'], ['ae', 'oe', 'ue', 'ss'], $word);

        return $alt == $word ? null : $alt;
    }
}