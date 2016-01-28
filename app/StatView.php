<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class StatView extends Model {

    protected $fillable = ['event_date', 'tv_program_id', 'views', 'total_views'];
    protected $dates = ['event_date'];
    public $timestamps = false;


    public function tvProgram()
    {
        return $this->belongsTo('App\TvProgram');
    }

}
