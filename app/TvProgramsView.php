<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use \Cache;

class TvProgramsView extends Model
{

    protected $table = 'tv_programs_view';
    protected $dates = ['start'];


    public function country()
    {
        return array_get(explode(',', $this->country), 0);
    }

    public function scopeLanguage($query, $lang)
    {
        if(empty($lang)) return;

        $stations = Cache::remember(__METHOD__.'::'.$lang, 60, function () use ($lang) {
            return Station::where('language','=',$lang)->lists('tvprogram_name')->toArray();
        });

        $query->whereIn('station', $stations);
    }

    public function film()
    {
        return $this->belongsTo('App\Film');
    }

    public function node()
    {
        return $this->belongsTo('App\Node', 'node_id');
    }


    public function awsOtrkeyFile()
    {
        return $this->hasOne('App\AwsOtrkeyFile', 'otrkeyfile_id', 'otrkeyfile_id');
    }
}
