<?php namespace App\FilmFilter\Attribute;


use Illuminate\Database\Eloquent\Builder;

class Quality extends AttributeAbstract
{

	protected $name = 'quality';
	protected $title = 'Qualität';

	protected $options = [
		'all'        => 'Alle',
		'mpg.HD.avi' => 'HD',
		'mpg.HD.ac3' => 'AC3',
		'mpg.HQ.fra' => 'FRA mp3',
		'mpg.HQ.avi' => 'HQ',
		'mpg.avi'    => 'SD',
		'mpg.mp4'    => 'mp4',
	];


	public function apply(Builder $builder)
	{
		if ($this->isSelected($this->default)) {
			return $this;
		}

		$builder->where('film_view.qualities', 'like', "%{$this->value}%");

		return $this;
	}

}