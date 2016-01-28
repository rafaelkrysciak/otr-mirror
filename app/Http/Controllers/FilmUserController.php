<?php namespace App\Http\Controllers;

use App\Film;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class FilmUserController extends Controller
{


    function __construct()
    {
        $this->middleware('premium');
    }


    public function add($film_id)
    {
        try {
            $user = \Auth::user();
            $film = Film::find($film_id);
            if ($user && $film) {
                $user->films()->attach($film);
            }
        } catch (\Exception $e) {
            return ['status' => 'NOK', 'message' => $e->getMessage()];
        }

        return ['status' => 'OK'];
    }


    public function remove($film_id)
    {
        try {
            $user = \Auth::user();
            $film = Film::find($film_id);
            if ($user && $film) {
                $user->films()->detach($film);
            }
        } catch (\Exception $e) {
            return ['status' => 'NOK', 'message' => $e->getMessage()];
        }

        return ['status' => 'OK'];
    }
}
