<?php namespace App\FilmFilter\Attribute;


use Illuminate\Database\Eloquent\Builder;

class Order extends AttributeAbstract
{

	protected $name = 'order';
	protected $title = 'Sortierung';
	protected $default = 'start';
	protected $value = 'start';

	protected $options = [
		'start'     => 'Kürzlich gelaufen',
		'downloads' => 'Top Downloads',
		'year'      => 'Erscheinungsjahr',
		'rating'    => 'Bewertung',
		'votes'     => 'Anazahl Bewertungen',
	];


	public function apply(Builder $builder)
	{
		switch ($this->value) {
			case 'year':
				$builder->orderBy('film_view.year', 'desc');
				break;
			case 'votes':
				$builder->orderBy('film_view.imdb_votes', 'desc');
				break;
			case 'rating':
				$builder->orderBy('film_view.imdb_rating', 'desc');
				break;
			case 'downloads':
				$builder->orderBy('film_view.downloads', 'desc');
				break;
			default:
			case 'start':
				$builder->orderBy('film_view.start', 'desc');
				break;
		}

		$builder->orderBy('film_view.start', 'desc');

		return $this;
	}

}