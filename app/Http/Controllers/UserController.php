<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;

class UserController extends Controller
{
	function __construct()
	{
		$this->middleware('admin');
	}


	public function select()
	{
		$q = \Input::get('q');
		$page = \Input::get('page', 1);

		$rows = User::where('email', 'like', "%$q%")
			->forPage($page, 30)
			->orderBy('email')
			->get();

		$user = [];
		foreach($rows as $row) {
			$user[] = [
				'text' => $row->email,
				'id'   => $row->id,
			];
		}

		$count = User::where('email', 'like', "%$q%")->count();

		return [
			'incomplete_resulte' => $count > count($user),
			'total_count'        => $count,
			'items'              => $user
		];
    }
}
