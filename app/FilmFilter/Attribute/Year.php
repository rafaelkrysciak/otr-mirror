<?php namespace App\FilmFilter\Attribute;

use Illuminate\Database\Eloquent\Builder;

class Year extends AttributeAbstract
{
	protected $name = 'year';
	protected $title = 'Jahr';

	protected $options = [
		'all'  => 'Alle',
		'2010' => '2010 - ',
		'2000' => '2000 - 2009',
		'1990' => '1990 - 1999',
		'1980' => '1980 - 1989',
		'1970' => '1970 - 1979',
		'1960' => ' - 1969',
	];

	public function apply(Builder $builder)
	{
		if ($this->value == '1960') {
			$builder->where('film_view.year', '<', 1970);
		} elseif (!$this->isSelected($this->default)) {
			$builder->where('film_view.year', '>=', $this->value);
			$builder->where('film_view.year', '<', (int) $this->value + 10);
		}

		return $this;
	}

}