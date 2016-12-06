<?php namespace App\FilmFilter\Attribute;

use Illuminate\Database\Eloquent\Builder;

class Rating extends AttributeAbstract
{

	protected $name = 'rating';
	protected $title = 'Bewertung';

	protected $options = [
		'all' => 'Alle',
		'9'   => 'mindestens 9',
		'8'   => 'mindestens 8',
		'7'   => 'mindestens 7',
		'6'   => 'mindestens 6',
		'5'   => 'mindestens 5',
		'4'   => 'unter 5',
	];


	public function apply(Builder $builder)
	{
		if ($this->value == '4') {
			$builder->where('film_view.imdb_rating', '<', 5);
		} elseif (!$this->isSelected($this->default)) {
			$builder->where('film_view.imdb_rating', '>=', $this->value);
		}

		return $this;
	}

}