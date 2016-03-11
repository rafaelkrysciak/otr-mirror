<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use \Cache;

class FilmView extends Film
{
    protected $fillable = [];
    protected $table = 'film_view';
    protected $dates = ['start'];


    public function country()
    {
        return array_get(explode(',', $this->country), 0);
    }

    public function scopeLanguage($query, $lang)
    {
        $query->where('languages','like', "%$lang%");
    }

    public function tvPrograms()
    {
        return $this->hasMany('App\TvProgram', 'film_id', 'id');
    }

    public function filmStars()
    {
        return $this->hasMany('App\Filmstar', 'film_id', 'id');
    }

}
