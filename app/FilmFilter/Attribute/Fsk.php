<?php namespace App\FilmFilter\Attribute;

use Illuminate\Database\Eloquent\Builder;

class Fsk extends AttributeAbstract
{

	protected $name = 'fsk';
	protected $title = 'FSK';

	protected $options = [
		'all'     => 'Alle',
		'o.Al.'   => 'o.Al.',
		'6'       => '6',
		'12'      => '12',
		'16'      => '16',
		'18'      => '18',
		'unknown' => 'Unbekannt',
	];


	public function apply(Builder $builder)
	{
		if ($this->isSelected('unknown')) {
			$builder->whereNotIn('film_view.fsk', ['o.Al.', '6', '12', '16', '18']);
		} elseif (!$this->isSelected($this->default)) {
			$builder->where('film_view.fsk', '=', $this->value);
		}

		return $this;
	}

}