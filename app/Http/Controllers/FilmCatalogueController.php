<?php

namespace App\Http\Controllers;

use App\FilmView;
use App\Http\Requests;
use Illuminate\Http\Request;

class FilmCatalogueController extends CatalogueController
{
	protected $series = 0;



	function __construct(Request $request)
	{
		parent::__construct($request);
	}


	public function my()
	{
		$user = \Auth::user();

		$filmQuery = FilmView::whereNotNull('film_id')
			->where('tvseries', '=', $this->series)
			->whereIn('film_view.film_id', function ($query) use ($user) {
				$query->select('film_id')->from('film_user')->where('user_id', '=', $user->id);
			});

		return $this->generate(
			['order', 'language', 'fsk', 'year', 'genre', 'quality'],
			$filmQuery,
			'Meine Filme'
		);
	}
}
