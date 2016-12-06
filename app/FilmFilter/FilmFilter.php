<?php namespace App\FilmFilter;


use App\FilmFilter\Attribute\AttributeAbstract;
use App\FilmFilter\Attribute\Fsk;
use App\FilmFilter\Attribute\Genre;
use App\FilmFilter\Attribute\Language;
use App\FilmFilter\Attribute\Missing;
use App\FilmFilter\Attribute\Order;
use App\FilmFilter\Attribute\Quality;
use App\FilmFilter\Attribute\Rating;
use App\FilmFilter\Attribute\Type;
use App\FilmFilter\Attribute\Year;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Class FilmFilter
 * @package App\FilmFilter
 */
class FilmFilter
{

	/**
	 * @var bool
	 */
	protected $useSession = false;
	/**
	 * @var
	 */
	protected $action;
	/**
	 * @var bool
	 */
	protected $fulltextSearch = false;
	/**
	 * @var array
	 */
	protected $additionalQueryParameter = [];
	/**
	 * @var AttributeAbstract[]
	 */
	protected $attributes = [];


	/**
	 * @param $attributes
	 *
	 * @return FilmFilter
	 */
	public static function factory($attributes)
	{
		$filter = new FilmFilter();

		foreach($attributes as $attribute) {
			switch($attribute) {
				case 'fsk':
					$filter->addAttribute(new Fsk());
					break;
				case 'genre':
					$filter->addAttribute(new Genre());
					break;
				case 'language':
					$filter->addAttribute(new Language());
					break;
				case 'missing':
					$filter->addAttribute(new Missing());
					break;
				case 'order':
					$filter->addAttribute(new Order());
					break;
				case 'quality':
					$filter->addAttribute(new Quality());
					break;
				case 'rating':
					$filter->addAttribute(new Rating());
					break;
				case 'type':
					$filter->addAttribute(new Type());
					break;
				case 'year':
					$filter->addAttribute(new Year());
					break;
			}
		}

		return $filter;
	}


	/**
	 * @param AttributeAbstract $attribute
	 *
	 * @return $this
	 */
	public function addAttribute(AttributeAbstract $attribute)
	{
		$this->attributes[$attribute->getName()] = $attribute;

		return $this;
	}


	/**
	 * @return mixed
	 */
	public function getFulltextSearch()
	{
		return $this->fulltextSearch;
	}


	/**
	 * @param mixed $fulltextSearch
	 *
	 * @return $this
	 */
	public function setFulltextSearch($fulltextSearch)
	{
		$this->fulltextSearch = $fulltextSearch;

		return $this;
	}


	/**
	 * @param string $name
	 *
	 * @return mixed
	 */
	public function getAdditionalQueryParameter($name = null)
	{
		if(!is_null($name) && array_key_exists($name, $this->additionalQueryParameter)) {
			return $this->additionalQueryParameter[$name];
		} elseif(!is_null($name)) {
			return null;
		}

		return $this->additionalQueryParameter;
	}


	/**
	 * @param mixed $additionalQueryParameter
	 *
	 * @return $this
	 */
	public function setAdditionalQueryParameter($additionalQueryParameter)
	{
		$this->additionalQueryParameter = (array) $additionalQueryParameter;

		return $this;
	}


	/**
	 * @param string $part
	 *
	 * @return mixed
	 */
	public function getAction($part = 'full')
	{
		list($controller, $method) = explode('@', $this->action);
		if($part == 'controller') {
			return $controller;
		}

		if($part == 'method') {
			return $method;
		}

		return $this->action;
	}


	/**
	 * @param mixed $action
	 *
	 * @return $this
	 */
	public function setAction($action)
	{
		$this->action = $action;

		return $this;
	}


	/**
	 * @param $filmid
	 *
	 * @return string
	 */
	public function getFilmUrl($filmid)
	{
		$url = 'tvprogram/film/' . $filmid;

		$language = 'any';
		$quality = 'any';

		foreach($this->attributes as $attribute) {
			if(get_class($attribute) == 'App\FilmFilter\Attribute\Quality' && !$attribute->isDefaultSelected()) {
				$quality = $attribute->getValue();
			}

			if(get_class($attribute) == 'App\FilmFilter\Attribute\Language' && !$attribute->isDefaultSelected()) {
				$language = $attribute->getValue();
			}
		}

		return $url . '/' . $language . '/' . $quality;
	}


	/**
	 * @return $this
	 */
	public function reset()
	{
		foreach($this->getAttributes() as $attribute) {
			$attribute->reset();
		}
		$this->storeInSession();

		return $this;
	}


	/**
	 * @param null $name
	 *
	 * @return AttributeAbstract|Attribute\AttributeAbstract[]
	 */
	public function getAttributes($name = null)
	{
		if(!is_null($name) && array_key_exists($name, $this->attributes)) {
			return $this->attributes[$name];
		}

		return $this->attributes;
	}


	/**
	 * @return $this
	 */
	public function storeInSession()
	{
		if($this->useSession === false) {
			return $this;
		}

		foreach($this->attributes as $attribute) {
			$key = 'film_filter.' . $this->useSession . '.' . $attribute->getName();
			\Session::set($key, $attribute->getValue());
		}

		return $this;
	}


	/**
	 * @param Builder $builder
	 *
	 * @return $this
	 */
	public function apply(Builder $builder)
	{
		foreach($this->attributes as $attribute) {
			$attribute->apply($builder);
		}

		$this->storeInSession();

		return $this;
	}


	/**
	 * @param Request $request
	 *
	 * @return $this
	 */
	public function readRequest(Request $request)
	{
		foreach($this->attributes as $attribute) {
			if($request->has($attribute->getName())) {
				$attribute->setFilter($request->get($attribute->getName()));
			}
		}

		return $this;
	}


	/**
	 * @param string $name
	 *
	 * @return $this
	 */
	public function useSession($name = 'default')
	{
		$this->useSession = $name;
		$this->restoreFromSession();

		return $this;
	}


	/**
	 * @return $this
	 */
	public function restoreFromSession()
	{
		if($this->useSession === false) {
			return $this;
		}

		foreach($this->attributes as $attribute) {
			$key = 'film_filter.' . $this->useSession . '.' . $attribute->getName();
			if(\Session::has($key)) {
				$attribute->setFilter(\Session::get($key));
			}
		}

		return $this;
	}


	/**
	 * @param $name
	 *
	 * @return AttributeAbstract
	 */
	public function getAttribute($name)
	{
		return $this->attributes[$name];
	}


	/**
	 * @param array $additionalParameters
	 *
	 * @return array
	 */
	public function getQueryStringArray($additionalParameters = [])
	{
		$queryStringArray = $additionalParameters;
		foreach($this->attributes as $attribute) {
			$queryStringArray = $attribute->getQueryStringArray($queryStringArray);
		}

		return $queryStringArray;
	}


	/**
	 * @param $name
	 * @param $value
	 *
	 * @return $this
	 */
	public function setAttributeValue($name, $value)
	{
		if(array_key_exists($name, $this->attributes)) {
			$this->attributes[$name]->setFilter($value);
		}

		return $this;
	}


	/**
	 * @param $name
	 *
	 * @return string
	 */
	public function getText($name)
	{
		if(array_key_exists($name, $this->attributes)) {
			return $this->attributes[$name]->getText();
		}

		return '';
	}

}