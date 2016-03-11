<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class EpgProgram extends Model {

    protected $fillable = [
        'tv_program_id',
        'channel',
        'start',
        'stop',
        'title_de',
        'title_xx',
        'sub_title',
        'desc',
        'category',
        'directors',
        'actors',
        'date',
        'episode',
        'season',
        'episode_total',
    ];

    protected $dates = ['start', 'stop'];


    public function getDirectorsAttribute($value)
    {
        return explode('#', $value);
    }

    public function setDirectorsAttribute($value)
    {
        $this->attributes['directors'] = implode('#', (array) $value);
    }

    public function getCategoryAttribute($value)
    {
        return explode('#', $value);
    }

    public function setCategoryAttribute($value)
    {
        $this->attributes['category'] = implode('#', (array) $value);
    }

    public function getActorsAttribute($value)
    {
        return explode('#', $value);
    }

    public function setActorsAttribute($value)
    {
        $this->attributes['actors'] = implode('#', (array) $value);
    }

}
