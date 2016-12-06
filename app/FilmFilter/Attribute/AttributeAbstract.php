<?php
namespace App\FilmFilter\Attribute;


use Illuminate\Database\Eloquent\Builder;

abstract class AttributeAbstract
{

	protected $name;
	protected $title;
	protected $options = [];
	protected $default = 'all';
	protected $value = 'all';


	public function __construct($options = [])
	{
		foreach ($options as $option => $value) {
			$setter = 'set' . ucfirst($option);
			if (method_exists($this, $setter)) {
				call_user_func([$this, $setter], $value);
			}
		}
	}


	public function getTitle()
	{
		return $this->title;
	}


	public function setTitle($title)
	{
		$this->title = $title;
	}


	public function getValue()
	{
		return $this->value;
	}


	public function getOptions()
	{
		$options = [];
		foreach ($this->options as $key => $text) {
			$item = [
				'key'      => $key,
				'text'     => $text,
				'selected' => $this->isSelected($key)
			];
			$options[] = $item;
		}

		return $options;
	}


	public function isSelected($value)
	{
		return $this->value == $value;
	}


	public function isDefaultSelected()
	{
		return $this->value == $this->default;
	}


	public function getText()
	{
		return $this->options[$this->value];
	}


	public function getOptionQueryStringArray($option, $additionalParameter = [])
	{
		if(array_key_exists($option, $this->options)) {
			$additionalParameter[$this->getName()] = $option;
		}

		return $additionalParameter;
	}


	public function getQueryStringArray($additionalParameter = [])
	{
		if (!array_key_exists($this->getName(), $additionalParameter) && !$this->isSelected($this->default)) {
			$additionalParameter[$this->name] = $this->value;
		}

		return $additionalParameter;
	}


	public function getName()
	{
		return $this->name;
	}


	public function setName($name)
	{
		$this->name = $name;
	}


	public function setFilter($value)
	{
		if (array_key_exists($value, $this->options)) {
			$this->value = $value;
		}

		return $this;
	}


	public function reset()
	{
		$this->setFilter($this->default);
	}


	abstract public function apply(Builder $builder);
}