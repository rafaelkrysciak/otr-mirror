<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\TvProgram;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\BootstrapThreePresenter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class UserListController extends Controller {


    function __construct()
    {
        $this->middleware('auth');
    }


    public function add()
    {
        $user = Auth::user();

        $list = Input::get('list');
        $tv_program = TvProgram::findOrFail(Input::get('tv_program_id'));

        try {
            $user->addTvProgramToList($tv_program, $list);
        } catch(\PDOException $e) {
            // Items is already in the list
            if($e->getCode() != 23000) {
                Log::error($e);
                return ['status' => 'NOK'];
            }
        }

        return ['status' => 'OK'];
	}

    public function remove()
    {
        $user = Auth::user();

        $list = Input::get('list');
        $tv_program = TvProgram::findOrFail(Input::get('tv_program_id'));

        try {
            $user->removeTvProgramFromList($tv_program, $list);
        } catch(\Exception $e) {
            Log::error($e);
            return ['status' => 'NOK'];
        }

        return ['status' => 'OK'];
    }


    public function favorite()
    {
        $user = Auth::user();

        $perPage = 100;
        $page = Input::get('page', 1);

        $shows = $user->getFavorites();
        $items = $shows->slice(($page-1)*$perPage, $perPage);

        $paginator = $this->generatePaginator($items, $shows->count(), $perPage, $page, '/user-list/favorite');
        $lists = $this->generateLists($user, $items);

        $date = '';

        $title = 'Gemerkt';

        return view('user-list.list', compact('paginator', 'date', 'lists', 'title'));

    }


    public function watched()
    {
        $user = Auth::user();

        $perPage = 100;
        $page = Input::get('page', 1);

        $shows = $user->getWatched();
        $items = $shows->slice(($page-1)*$perPage, $perPage);

        $paginator = $this->generatePaginator($items, $shows->count(), $perPage, $page, '/user-list/watched');
        $lists = $this->generateLists($user, $items);

        $date = '';

        $title = 'Angeschaut';

        return view('user-list.list', compact('paginator', 'date', 'lists', 'title'));

    }

    protected function generateLists($user, $items)
    {
        $userLists = $user->getListsForTvPrograms($items);

        $lists = [];
        foreach($items as $item) {
            $lists[$item->id] = [
                User::FAVORITE => in_array($item->id, $userLists[User::FAVORITE]) ? 'list-active' : '',
                User::DOWNLOADED => in_array($item->id, $userLists[User::DOWNLOADED]) ? 'list-active' : '',
                User::WATCHED => in_array($item->id, $userLists[User::WATCHED]) ? 'list-active' : '',
            ];
        }

        return $lists;
    }

    protected function generatePaginator($items, $totalCount, $perPage = 100, $page = null, $path = null)
    {
        $page = is_null($page) ? Input::get('page', 1) : $page;

        $paginator = new LengthAwarePaginator($items, $totalCount, $perPage, $page, ['path' => $path]);
        Paginator::presenter(function() use ($paginator) {
            return new BootstrapThreePresenter($paginator);
        });

        return $paginator;
    }
}
