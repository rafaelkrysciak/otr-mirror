<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Filmstar extends Model {

    protected $fillable = [
        'film_id',
        'star',
        'role',
        'position'
    ];

    public $timestamps = false;


    public function film()
    {
        return $this->belongsTo('App\Film');
    }


    public static function createCast(Film $film, array $cast)
    {
        $filmstars = [];
        foreach ($cast as $key => $role) {
            $data = [
                'star' => $role['star'],
                'role' => $role['role'],
                'position' => array_key_exists('position', $role) ? $role['position'] : $key,
            ];
            $filmstars[] = new Filmstar($data);
        }
        $film->filmStars()->saveMany($filmstars);
    }
}
