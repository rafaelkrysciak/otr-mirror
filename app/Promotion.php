<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model {

    protected $fillable = [
        'title',
        'tv_program_id',
        'search',
        'position',
        'active',
    ];


    public function getLink()
    {
        if(empty($this->search)) {
            return 'tvprogram/show/'.$this->tv_program_id;
        } else {
            return 'tvprogram/search?q='.urlencode($this->search);
        }
    }


    public function getImageLink()
    {
        return 'img/promotions/'.$this->id.'.jpg';
    }


    public function scopeActive($query)
    {
        $query->where('active','=',true);
    }


    public function tvProgram()
    {
        return $this->belongsTo('App\TvProgram');
    }
}
