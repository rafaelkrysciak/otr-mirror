<?php namespace App\Http\Controllers;

use App\Exceptions\LimitExceededDownloadException;
use App\Exceptions\NoCapacityDownloadException;
use App\Exceptions\QualityViolationDownloadException;
use App\Film;
use App\Filmstar;
use App\Http\Requests;
use App\OtrkeyFile;
use App\Services\DownloadService;
use App\Services\ImdbService;
use App\Services\NodeService;
use App\Services\SearchService;
use App\Services\StatService;
use App\TvProgram;
use App\TvProgramsView;
use App\User;
use Illuminate\Pagination\BootstrapThreePresenter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use \Auth;
use \Cache;
use \Input;
use \Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

class TvProgramController extends Controller
{

    /**
     *
     */
    function __construct()
    {
        //$this->middleware('auth', ['only' => 'download']);
    }


    /**
     * Forward to a TvProgram based on film and quality
     *
     * @param $film_id
     * @param null|string $language
     * @param string $quality
     *
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function film($film_id, $language = 'any', $quality = 'any')
    {
        $tvProgramQuery = TvProgramsView::where('film_id', '=', $film_id)
            ->orderBy('start', 'desc');
        if ($language != 'any') {
            $tvProgramQuery->where('language', '=', $language);
        }
        if ($quality != 'any') {
            $tvProgramQuery->where('quality', '=', $quality);
        }
        $tvProgram = $tvProgramQuery->first();
        if ($tvProgram) {
            return redirect('tvprogram/show/' . $tvProgram->tv_program_id);
        }

        return redirect()->back()->withErrors(['Sendung nicht gefunden']);
    }


    /**
     * Display a listing of the resource.
     *
     * @param string $lang
     *
     * @return Response
     */
    public function index($lang = null)
    {
        $user = Auth::user();

        $page = Input::get('page', 1);
        $perPage = 100;

        $cacheKey = "TvProgramController::index::{$lang}::$page";

        list($items, $totalCount) = Cache::remember($cacheKey, 10, function () use ($page, $perPage, $lang) {

            $totalCount = TvProgramsView::language($lang)
                ->distinct()
                ->count('tv_program_id');
            $items = TvProgramsView::language($lang)
                ->groupBy('tv_program_id')
                ->orderBy('start', 'desc')
                ->forPage($page, $perPage)
                ->get();

            // Check if the whole Cache is invalid
            // If the sum of all item change the whole cache is invalid
            $checkSum = TvProgramsView::language($lang)->sum('tv_program_id');
            $checkKey = 'TvProgramController::index::check';
            $checkValue = Cache::get($checkKey);
            if ($checkValue != $checkSum) {
                for ($i = 1; $i < ceil($totalCount / $perPage); $i++) {
                    Cache::forget("TvProgramController::index::{$lang}::$i");

                }
                Cache::forever($checkKey, $checkSum);
            }

            return [$items, $totalCount];
        });

        $paginator = new LengthAwarePaginator($items, $totalCount, $perPage, $page, ['path' => '/tvprogram/' . $lang]);
        Paginator::presenter(function () use ($paginator) {
            return new BootstrapThreePresenter($paginator);
        });

        $lists = [];
        if ($user) {
            // User list (favorites and watched)
            $lists = $user->getListsForTvPrograms($items->lists('tv_program_id')->toArray());
        }

        // $date: user as day header in the view
        $date = '';

        return view('tvprogram.index', compact('paginator', 'date', 'lists', 'lang'));
    }


    /**
     * @param SearchService $searchService
     *
     * @return \Illuminate\View\View
     */
    public function search(SearchService $searchService)
    {

        $q = trim(Input::get('q'));
        $page = Input::get('page', 1);
        $lang = Input::get('language', 'all');
        $perPage = 100;

        $availableLanguages = TvProgramsView::select(\DB::raw('language, count(distinct tv_program_id) as cnt'))
            ->groupBy('language')
            ->orderByRaw('count(*) DESC')
            ->get()
            ->toArray();

        $languages = ['all' => 'Alle Sprachen'];
        foreach ($availableLanguages as $language) {
            $languages[$language['language']] = $language['language'] . ' (' . $language['cnt'] . ')';
        }


        if (!empty($q)) {

            $items = $searchService->search($q, $lang)
                ->groupBy('tv_program_id')
                ->forPage($page, $perPage)
                ->get();

            $count = $searchService->search($q, $lang)
                ->distinct()
                ->count('tv_program_id');

            $paginator = new LengthAwarePaginator($items, $count, $perPage, $page);
            $paginator->setPath('/tvprogram/search')->appends(['q' => $q, 'language' => $lang]);
            Paginator::presenter(function () use ($paginator) {
                return new BootstrapThreePresenter($paginator);
            });

            $user = Auth::user();;
            if ($user) {
                $userLists = $user->getListsForTvPrograms($items->pluck('tv_program_id')->toArray());
            } else {
                $userLists = [
                    User::FAVORITE   => [],
                    User::DOWNLOADED => [],
                    User::WATCHED    => [],
                ];
            }

            foreach ($items as $item) {
                $lists[$item->tv_program_id] = [
                    User::FAVORITE   => in_array($item->tv_program_id, $userLists[User::FAVORITE]) ? 'list-active' : '',
                    User::DOWNLOADED => in_array($item->tv_program_id, $userLists[User::DOWNLOADED]) ? 'list-active' : '',
                    User::WATCHED    => in_array($item->tv_program_id, $userLists[User::WATCHED]) ? 'list-active' : '',
                ];
            }
        } else {
            $paginator = new LengthAwarePaginator([], 0, $perPage, $page, ['path' => '/tvprogram']);
            Paginator::presenter(function () use ($paginator) {
                return new BootstrapThreePresenter($paginator);
            });
        }

        $date = '';

        return view('tvprogram.search', compact('paginator', 'date', 'lists', 'q', 'languages', 'lang'));

    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id, NodeService $nodeService, StatService $statService, SearchService $searchService)
    {
        if(!$tvProgram = TvProgram::find($id)) {
            abort(404);
        }

        $episodes = [];
        if($tvProgram->film && $tvProgram->film->tvseries) {
            $episodes = TvProgramsView::where('film_id','=',$tvProgram->film_id)
                ->groupBy('tv_program_id')
                ->orderBy('station')
                ->orderBy('start','desc')
                ->get();

            if(Auth::user()) {
                $seriesLists = Auth::user()->getListsForTvPrograms($episodes->lists('tv_program_id')->toArray());
            }
        }

        $relatedItems = $searchService->getRelated($tvProgram);

        $downloadType = User::getDownloadType();
        $lists = User::getListsForTvProgram($tvProgram);

        $files = $tvProgram->otrkeyFiles->sortBy('quality');
        $token = [];
        foreach ($files as $file) {
            $token[$file->id] = $nodeService->generateDownloadToken($file->name, $downloadType);
        }

        $statService->trackView($tvProgram->id);

        if(Auth::user() && Auth::user()->isPremium() && $tvProgram->film && $tvProgram->film->id > 0) {
            return view('tvprogram.show_premium', compact('tvProgram', 'lists', 'token', 'relatedItems', 'episodes', 'seriesLists'));
        } elseif($tvProgram->film && $tvProgram->film->id > 0) {
            return view('tvprogram.show_film', compact('tvProgram', 'lists', 'token', 'relatedItems', 'episodes', 'seriesLists'));
        } else {
            return view('tvprogram.show', compact('tvProgram', 'lists', 'token', 'relatedItems', 'tvseries'));
        }

    }


    /**
     * @param $user
     * @param $token
     * @param $filename
     * @param DownloadService $downloadService
     *
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function download($user, $token, $filename, DownloadService $downloadService)
    {
        /* @var $user \App\User */
        $user = $user == 'guest' ? null : User::find($user);

        if ($user && $user->isPremium()) {
            $downloadType = DownloadService::PREMIUM;
        } elseif ($user) {
            $downloadType = DownloadService::REGISTERED;
        } else {
            $downloadType = DownloadService::GUEST;
        }

        if (!$downloadService->validateDownloadToken($token, $filename, $downloadType)) {
            return redirect()->back()->with('error', "Der Download Link ist ungültig. Möglicherweise ist er abgelaufen. Bitte versuche noch mal.");
        }

        $otrkeyFile = OtrkeyFile::where('name', '=', $filename)->first();
        if (!$otrkeyFile) {
            return redirect()->back()->with('error', "Datei $filename konnte leider nicht nicht gefunden werden.");
        }

        try {
            $link = $downloadService->getDownloadLink($otrkeyFile, $downloadType);
            if ($user) {
                $user->addTvProgramToList($otrkeyFile->tvProgram, User::WATCHED);
                $user->addTvProgramToList($otrkeyFile->tvProgram, User::DOWNLOADED);
            }

            return redirect()->away($link);
        } catch (NoCapacityDownloadException $e) {
            flash()->warning('Im Moment sind alle Server ausgelastet. Bitte versuche später noch mal oder kaufe ein Premium-Account.');

            return redirect('payment/prepare');
        } catch (LimitExceededDownloadException $e) {
            // ToDo: Remove it
            flash('Der Limit ist für diesen Monat aufgebraucht. Bitte versuche später noch mal oder kaufe ein Premium-Account.');

            return redirect('payment/prepare');
        } catch (QualityViolationDownloadException $e) {
            // ToDo: Remove it
            flash('Download in der ausgewählten Qualität ist nur mit Premium-Account möglich. Bitte kaufe ein Premium-Account.');

            return redirect('payment/prepare');
        } catch (\Exception $e) {
            Log::error($e);

            return redirect()->back()->withErrors(['Es konnte kein Link generiert werden. Versuche bitte später noch mal.']);
        }
    }


    /**
     * @return array
     */
    public function select()
    {
        $q = Input::get('q');
        $page = Input::get('page', 1);

        $rows = TvProgramsView::where('title', 'like', "%$q%")
            ->forPage($page, 30)
            ->groupBy('tv_program_id')
            ->orderBy('start')
            ->get();

        $tvPrograms = [];
        foreach ($rows as $row) {
            $tvPrograms[] = [
                'text' => $row->title . ' (' . $row->station . ' ' . $row->start->format('Y-m-d H:i') . ')',
                'id'   => $row->tv_program_id,
            ];
        }

        $count = TvProgramsView::where('title', 'like', "%$q%")->groupBy('tv_program_id')->count();

        return [
            'incomplete_resulte' => $count > count($tvPrograms),
            'total_count'        => $count,
            'items'              => $tvPrograms
        ];
    }


    /**
     * Delete TVProgram from the view and all related files from the nodes
     *
     * @param $tv_program_id
     * @param NodeService $nodeService
     *
     * @return $this
     */
    public function destroy($tv_program_id, NodeService $nodeService)
    {
        $errors = [];

        $tvProgram = TvProgram::findOrFail($tv_program_id);

        $files = $tvProgram->otrkeyFiles;
        foreach ($files as $file) {
            if ($file->isAvailable()) {
                try {
                    $nodeService->deleteOtrkeyFile($file);
                } catch (\Exception $e) {
                    Log::error($e);
                    $errors[] = "Can't delete {$file->name}. " . $e->getMessage();
                }
            }
        }

        TvProgramsView::where('tv_program_id', '=', $tv_program_id)->delete();

        if (empty($errors)) flash('All files deleted.');

        return redirect()->back()->withErrors($errors);
    }


    /**
     * @param $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $tv_program = TvProgram::findOrFail($id);

        $film = $tv_program->film;
        if ($film) {
            $films = [
                $film->id => $film->title . ' (' . $film->year . ') ' . ($film->tvseries ? 'Series' : '')
            ];
        } else {
            $films = [];
        }

        return view('tvprogram.edit', compact('tv_program', 'films'));
    }


    /**
     * @param $id
     * @param Request|\Request $request
     *
     * @param ImdbService $imdbService
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update($id, Request $request, ImdbService $imdbService)
    {
        $all = $request->all();

        $film_id = $request->get('film_id');
        if (preg_match('/^tt[0-9]{7}$/', $film_id)) {
            $imdbId = trim($film_id, 't');
            $film = Film::create($imdbService->getImdbData($imdbId));
            Filmstar::createCast($film, $imdbService->cast($imdbId, 20));
            $film_id = $film->id;
        }
        $all['film_id'] = $film_id;

        $tv_program = TvProgram::findOrFail($id);

        $tv_program->update($all);

        flash('TV-Program updated');

        return redirect()->back();
    }


    /**
     * @param $otrid
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function byOtrId($otrid)
    {
        $tvProgram = TvProgram::where('otr_epg_id', '=', $otrid)->first();
        if ($tvProgram) {
            return redirect('tvprogram/show/' . $tvProgram->id);
        } else {
            flash()->error('Leider konnte die Sendung nicht gefunden werden');

            return redirect('/');
        }
    }


    /**
     * @param StatService $statService
     *
     * @return \Illuminate\View\View
     */
    public function top100(StatService $statService)
    {
        $page = Input::get('page', 1);

        $user = Auth::user();

        $downloads = Cache::remember(__METHOD__, 60 * 12, function () use ($statService) {
            return $statService->topDownloads(100, 7);
        });

        $downloadsCurrentPage = $downloads->forPage($page, 20);


        $paginator = new LengthAwarePaginator($downloadsCurrentPage, 100, 20, Input::get('page', 1), ['path' => '/tvprogram/top100']);

        $lists = [];
        if ($user) {
            // User list (favorites and watched)
            $lists = $user->getListsForTvPrograms($downloadsCurrentPage->lists('tv_program_id')->toArray());
        }

        return view('tvprogram/top100', compact('paginator', 'lists'));
    }

}
