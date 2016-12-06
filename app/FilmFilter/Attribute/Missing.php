<?php namespace App\FilmFilter\Attribute;


use Illuminate\Database\Eloquent\Builder;

class Missing extends AttributeAbstract
{

	protected $name = 'missing';
	protected $title = 'Missing';

	protected $options = [
		'all'          => 'Alle',
		'amazon_asin'  => 'Amazon ASIN',
		'amazon_image' => 'Amazon Image',
		'trailer'      => 'Trailer',
		'dvdkritik'    => 'Review',
		'description'  => 'Description',
	];


	public function apply(Builder $builder)
	{
		if ($this->isSelected($this->default)) {
			return $this;
		}

		$value = $this->value;

		$builder->join('films', 'film_view.film_id', '=', 'films.id');
		$builder->where(function ($query) use ($value) {
			$query->orWhereNull('films.' . $value)
				->orWhere('films.' . $value, '=', '');
		});

		return $this;
	}

}