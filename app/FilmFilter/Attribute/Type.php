<?php namespace App\FilmFilter\Attribute;


use Illuminate\Database\Eloquent\Builder;

class Type extends AttributeAbstract
{

	protected $name = 'type';
	protected $title = 'Typ';

	protected $options = [
		'all'    => 'Alle',
		'film'   => 'Filme',
		'series' => 'Serien'
	];


	public function apply(Builder $builder)
	{
		if ($this->isSelected($this->default)) {
			return $this;
		}

		switch ($this->value) {
			case 'film':
				$builder->where('film_view.tvseries', '=', 0);
				break;
			case 'series':
				$builder->where('film_view.tvseries', '=', 1);
				break;
		}

		return $this;
	}

}