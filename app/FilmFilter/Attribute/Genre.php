<?php namespace App\FilmFilter\Attribute;

use Illuminate\Database\Eloquent\Builder;

class Genre extends AttributeAbstract
{

	protected $name = 'genre';
	protected $title = 'Genre';

	protected $options = [
		'all'         => 'Alle',
		'drama'       => 'Drama',
		'comedy'      => 'Comedy',
		'thriller'    => 'Thriller',
		'crime'       => 'Crime',
		'romance'     => 'Romance',
		'action'      => 'Action',
		'documentary' => 'Documentary',
		'adventure'   => 'Adventure',
		'family'      => 'Family',
		'music'       => 'Music',
		'mystery'     => 'Mystery',
		'fantasy'     => 'Fantasy',
		'sci-fi'      => 'Sci-Fi',
		'horror'      => 'Horror',
		'animation'   => 'Animation',
		'biography'   => 'Biography',
		'history'     => 'History',
		'war'         => 'War',
		'sport'       => 'Sport',
		'musical'     => 'Musical',
		'reality-tv'  => 'Reality-TV',
		'western'     => 'Western',
		'short'       => 'Short',
		'talk-show'   => 'Talk-Show',
		'game-show'   => 'Game-Show',
		'news'        => 'News',
		'adult'       => 'Adult',
	];


	public function apply(Builder $builder)
	{
		if ($this->isSelected($this->default)) {
			return $this;
		}

		$builder->where('film_view.genre', 'like', '%' . $this->value . '%');

		return $this;
	}

}