<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class TvProgram extends Model
{

    protected $fillable = [
        'otr_epg_id',
        'start',
        'end',
        'length',
        'station',
        'title',
        'type',
        'description',
        'genre_id',
        'fsk',
        'language',
        'season',
        'episode',
        'film_id',
        'film_mapper_id',
        'year',
        'director',
    ];

    protected $dates = ['start', 'end'];


    public function scopeLanguage($query, $lang)
    {
        if (empty($lang)) return;

        $stations = Cache::remember(__METHOD__ . '::' . $lang, 60, function () use ($lang) {
            return Station::where('language', '=', $lang)->pluck('tvprogram_name');
        });

        $query->whereIn('station', $stations);
    }


    public function otrkeyFiles()
    {
        return $this->hasMany('App\OtrkeyFile');
    }


    public function tvstation()
    {
        return $this->belongsTo('App\Station', 'station', 'tvprogram_name');
    }


    public function film()
    {
        return $this->belongsTo('App\Film');
    }


    public function proposedFilm()
    {
        return $this->belongsTo('App\Film', 'proposed_film_id');
    }


    public function mapper()
    {
        return $this->belongsTo('App\FilmMapper');
    }


    public function epgProgram()
    {
        return $this->hasOne('App\EpgProgram');
    }


    public function promotion()
    {
        return $this->hasMany('App\Promotion');
    }


    public function users()
    {
        return $this->belongsToMany('App\User', 'tv_program_user', 'tv_program_id', 'user_id')
            ->withPivot('type')
            ->withTimestamps();
    }

}
