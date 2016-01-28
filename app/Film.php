<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{

    protected $fillable = [
        'title',
        'original_title',
        'description',
        'year',
        'country',
        'genre',
        'director',
        'fsk',

        'amazon_asin',
        'amazon_link',
        'amazon_image',

        'imdb_last_update',
        'imdb_id',
        'imdb_rating',
        'imdb_votes',
        'imdb_runtime',
        'imdb_image',

        'trailer',
        'dvdkritik',

        'tvseries',
    ];

    protected $dates = ['imdb_last_update'];


    public function trailerUrl()
    {
        $id = $this->getYoutubeId($this->trailer);
        if ($id) {
            return "http://www.youtube.com/embed/" . $id . '?autoplay=1&amp;wmode=opaque&amp;vq=hd720';
        }

        return $this->trailer;
    }


    public function reviewUrl()
    {
        $id = $this->getYoutubeId($this->dvdkritik);
        if ($id) {
            return "http://www.youtube.com/embed/" . $id . '?autoplay=1&amp;wmode=opaque&amp;vq=hd720';
        }

        return $this->dvdkritik;
    }


    protected function getYoutubeId($url)
    {
        $matches = [];
        if (preg_match('/www\.youtube\.com\/watch\?v=([^&]+)/', $url, $matches)) {
            return $matches[1];
        }
    }


    public function imageResize($height)
    {
        //http://ecx.images-amazon.com/images/I/51brs1Ez1ML.jpg
        //http://ecx.images-amazon.com/images/I/51w07kGRNxL.jpg
        //http://ecx.images-amazon.com/images/I/517d4PZA1YL.jpg

        if (empty($this->amazon_image)) {
            return '';
        }
        $basename = basename($this->amazon_image);
        $imagename = substr($basename, 0, strpos($basename, '.'));
        $imagetype = substr($basename, strpos($basename, '.'));

        $suffix = '_UY' . $height . '_';

        return str_replace($basename, $imagename . '.' . $suffix . $imagetype, $this->amazon_image);
    }


    public function country()
    {
        return array_get(explode(',', $this->country), 0);
    }


    public function genres()
    {
        return explode(',', $this->genre);
    }

    public function directors()
    {
        return explode(',', $this->director);
    }

    public function series()
    {
        return $this->tvseries ? 'Series' : '';
    }


    public function tvPrograms()
    {
        return $this->hasMany('App\TvProgram')->orderBy('start');
    }


    public function proposedTvPrograms()
    {
        return $this->hasMany('App\TvProgram', 'proposed_film_id');
    }


    public function filmStars()
    {
        return $this->hasMany('App\Filmstar')->orderBy('position');
    }


    public function scopeByImdbId($query, $imdb_id)
    {
        $query->where('imdb_id', '=', $imdb_id);
    }


    /**
     * Get users related to the film
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User')
            ->withTimestamps();
    }


    public function belongsToUser(User $user)
    {
        return $this->users()->where('user_id','=',$user->id)->count() > 0;
    }
}
