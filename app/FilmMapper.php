<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class FilmMapper extends Model
{

    protected $fillable = [
        'org_title',
        'new_title',
        'min_length',
        'max_length',
        'film_id',
        'language',
        'year',
        'channel',
        'director',
        'verified',
    ];

    protected $casts = [
        'verified' => 'boolean',
    ];


    public function film()
    {
        return $this->belongsTo('App\Film');
    }

    public function tvPrograms()
    {
        return $this->hasMany('App\TvProgram')->orderBy('start', 'desc');
    }


    /**
     * @param TvProgram $tvProgram
     * @param (integer|Film) $film
     *
     * @return FilmMapper
     */
    public static function createFromTvProgram(TvProgram $tvProgram, $film)
    {
        if ($film instanceof Film) $film = $film->id;

        return self::create([
            'film_id'   => $film,
            'org_title' => $tvProgram->org_title,
            'new_title' => $tvProgram->org_title,
            'year'      => $tvProgram->year > 0 ? $tvProgram->year : 0,
            'director'  => is_null($tvProgram->director) ? '' : $tvProgram->director,
            'language'  => $tvProgram->tvstation->language_short,
            'verified'  => false,
        ]);
    }
}
