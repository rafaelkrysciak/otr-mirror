<?php namespace App\FilmFilter\Attribute;


use Illuminate\Database\Eloquent\Builder;

class Language extends AttributeAbstract
{

	protected $name = 'language';
	protected $title = 'Sprache';

	protected $options = [
		'all'      => 'Alle',
		'deutsch'  => 'Deutsch',
		'englisch' => 'Englisch',
	];


	public function apply(Builder $builder)
	{
		if ($this->isSelected($this->default)) {
			return $this;
		}

		$builder->where('film_view.languages', 'like', "%$this->value%");

		return $this;
	}

}