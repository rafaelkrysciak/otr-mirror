<?php namespace App\Http\Controllers;

use App\Exceptions\LimitExceededDownloadException;
use App\Exceptions\NoCapacityDownloadException;
use App\Exceptions\QualityViolationDownloadException;
use App\Film;
use App\Filmstar;
use App\Http\Requests;
use App\Node;
use App\OtrkeyFile;
use App\Services\DownloadService;
use App\Services\ImdbService;
use App\Services\NodeService;
use App\Services\SearchService;
use App\Services\StatService;
use App\TvProgram;
use App\TvProgramsView;
use App\User;
use Auth;
use Cache;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Pagination\BootstrapThreePresenter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Input;
use Log;

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
		if($language != 'any') {
			$tvProgramQuery->where('language', '=', $language);
		}
		if($quality != 'any') {
			$tvProgramQuery->where('quality', '=', $quality);
		}
		$tvProgram = $tvProgramQuery->first();
		if($tvProgram) {
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
			if($checkValue != $checkSum) {
				for($i = 1; $i < ceil($totalCount / $perPage); $i++) {
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
		if($user) {
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
		foreach($availableLanguages as $language) {
			$languages[$language['language']] = $language['language'] . ' (' . $language['cnt'] . ')';
		}


		if(!empty($q)) {

			$fullResult = $searchService->searchTnt($q, $lang);
			$items = $fullResult->forPage($page, $perPage);

			$count = count($fullResult);

			$paginator = new LengthAwarePaginator($items, $count, $perPage, $page);
			$paginator->setPath('/tvprogram/search')->appends(['q' => $q, 'language' => $lang]);
			Paginator::presenter(function () use ($paginator) {
				return new BootstrapThreePresenter($paginator);
			});

			$user = Auth::user();;
			if($user) {
				$userLists = $user->getListsForTvPrograms($items->pluck('tv_program_id')->toArray());
			} else {
				$userLists = [
					User::FAVORITE   => [],
					User::DOWNLOADED => [],
					User::WATCHED    => [],
				];
			}

			foreach($items as $item) {
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

		$showDownload = \Session::get('open_downloads.'.$id) === true || (Auth::user() && Auth::user()->isPremium());

		$episodes = [];
		if($tvProgram->film && $tvProgram->film->tvseries) {
			$episodes = TvProgramsView::where('film_id', '=', $tvProgram->film_id)
				->groupBy('tv_program_id')
				->orderBy('station')
				->orderBy('start', 'desc')
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
		foreach($files as $file) {
			$token[$file->id] = $nodeService->generateDownloadToken($file->name, $downloadType);
		}

		$statService->trackView($tvProgram->id);

		$stats = ['formats' => [], 'total' => 0, 'film' => 0];
		if(Auth::user() && Auth::user()->isAdmin()) {
			$stats['formats'] = $statService->getTvProgrammStats($id);
			$stats['total'] = array_sum($stats['formats']);
			$stats['film'] = $tvProgram->film_id > 0 ? $statService->getFilmStats($tvProgram->film_id) : 0;
		}

		if(Auth::user() && Auth::user()->isPremium() && $tvProgram->film && $tvProgram->film->id > 0) {
			return view('tvprogram.show_premium', compact('tvProgram', 'lists', 'token', 'relatedItems', 'episodes', 'seriesLists', 'stats', 'showDownload'));
		} elseif($tvProgram->film && $tvProgram->film->id > 0) {
			return view('tvprogram.show_film', compact('tvProgram', 'lists', 'token', 'relatedItems', 'episodes', 'seriesLists', 'stats', 'showDownload'));
		} else {
			return view('tvprogram.show', compact('tvProgram', 'lists', 'token', 'relatedItems', 'tvseries', 'stats', 'showDownload'));
		}

	}


	/**
	 * @param $user
	 * @param $token
	 * @param $filename
	 * @param DownloadService $downloadService
	 * @param StatService $statService
	 *
	 * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function download($user, $token, $filename, DownloadService $downloadService, StatService $statService)
	{
		/* @var $user \App\User */
		$user = $user == 'guest' ? null : User::find($user);

		if($user && $user->isPremium()) {
			$downloadType = DownloadService::PREMIUM;
			$username = $user->name;
		} elseif($user) {
			$downloadType = DownloadService::REGISTERED;
			$username = $user->name;
		} else {
			$downloadType = DownloadService::GUEST;
			$username = 'guest' . \Session::getId();
		}

		if(!$downloadService->validateDownloadToken($token, $filename, $downloadType)) {
			return redirect()->back()->with('error', "Der Download Link ist ungültig. Möglicherweise ist er abgelaufen. Bitte versuche noch mal.");
		}

		$otrkeyFile = OtrkeyFile::where('name', '=', $filename)->first();
		if(!$otrkeyFile) {
			return redirect()->back()->with('error', "Datei $filename konnte leider nicht nicht gefunden werden.");
		}

		if(!Cache::get($token)) {
			$statService->trackDownload($otrkeyFile->id);
			Cache::put($token, 1, Carbon::now()->addHours(26));
		}

		try {
			$link = $downloadService->getDownloadLink($otrkeyFile, $downloadType);
			if($user) {
				$user->addTvProgramToList($otrkeyFile->tvProgram, User::WATCHED);
				$user->addTvProgramToList($otrkeyFile->tvProgram, User::DOWNLOADED);
			}

			Log::info("[user download] [username:$username] [type:$downloadType] [filename:$filename] [link:$link]");

			return redirect()->away($link);
		} catch(NoCapacityDownloadException $e) {
			Log::info("[user download] [username:$username] [type:$downloadType] [filename:$filename] [NoCapacityDownloadException]");
			flash()->warning('Im Moment sind alle Server ausgelastet. Bitte versuche später noch mal oder kaufe ein Premium-Account.');

			return redirect('payment/prepare');
		} catch(LimitExceededDownloadException $e) {
			Log::info("[user download] [username:$username] [type:$downloadType] [filename:$filename] [LimitExceededDownloadException]");
			// ToDo: Remove it
			flash('Der Limit ist für diesen Monat aufgebraucht. Bitte versuche später noch mal oder kaufe ein Premium-Account.');

			return redirect('payment/prepare');
		} catch(QualityViolationDownloadException $e) {
			Log::info("[user download] [username:$username] [type:$downloadType] [filename:$filename] [QualityViolationDownloadException]");
			// ToDo: Remove it
			flash('Download in der ausgewählten Qualität ist nur mit Premium-Account möglich. Bitte kaufe ein Premium-Account.');

			return redirect('payment/prepare');
		} catch(\Exception $e) {
			$message = $e->getMessage();
			Log::info("[user download] [username:$username] [type:$downloadType] [filename:$filename] [Exception: {$message}]");
			Log::error($e);

			return redirect()->back()->withErrors(['Es konnte kein Link generiert werden. Versuche bitte später noch mal.']);
		}
	}


	public function hiveCoinRedirect($tv_program_id)
	{
		$key = str_random(40);

		\Session::put('open_downloads.'.$tv_program_id, $key);
		try {
			$client = new Client();
			$res = $client->post('https://api.coinhive.com/link/create', [
				'verify' => false,
				'form_params' => [
					'secret' => 'mx7qVmim0i2BxTCEvPiMdvHUzgqKk7ax',
					'url' => url('tvprogram/verify-view', ['key' => $key]),
					'hashes' => 1024,
				],
			]);
			$data = \GuzzleHttp\json_decode($res->getBody()->getContents());
			if (!$data->success) {
				Log::error(__METHOD__." [tv_program_id=$tv_program_id] Response=".$data->error);
				flash()->error('Es ist ein Fehler beim aufrufen eines externen Dienstes aufgetreten. Bitte versuche später noch mal.');
				return redirect('tvprogram/show/'.$tv_program_id);
			}
		} catch (\Exception $e) {
			Log::error($e);
			flash()->error('Es ist ein Fehler beim aufrufen eines externen Dienstes aufgetreten. Bitte versuche später noch mal.');
			return redirect('tvprogram/show/'.$tv_program_id);
		}

		Log::info(__METHOD__." [Key=$key] [tv-program-id=$tv_program_id] redirected");

		return \Redirect::away($data->url);
	}


	public function verifyDownloadView($key)
	{
		$list = (array) \Session::get('open_downloads');
		$tvProgramId = (int) array_search($key, $list, true);

		\Session::put('open_downloads.'.$tvProgramId, true);

		Log::info(__METHOD__." [Key=$key] [tv-program-id=$tvProgramId] verified");

		return redirect('tvprogram/show/'.$tvProgramId);
	}


	/**
	 * @param $user
	 * @param $token
	 * @param $filename
	 * @param DownloadService $downloadService
	 * @param StatService $statService
	 *
	 * @return array
	 */
	public function downloadLink($user, $token, $filename, DownloadService $downloadService, StatService $statService)
	{
		/* @var $user \App\User */
		$user = $user == 'guest' ? null : User::find($user);

		if($user && $user->isPremium()) {
			$downloadType = DownloadService::PREMIUM;
			$username = $user->name;
		} elseif($user) {
			$downloadType = DownloadService::REGISTERED;
			$username = $user->name;
		} else {
			$downloadType = DownloadService::GUEST;
			$username = 'guest' . \Session::getId();
		}

		if(!$downloadService->validateDownloadToken($token, $filename, $downloadType)) {
			return [
				'status' => 'NOK',
				"message"  => "Der Download Link ist ungültig. Möglicherweise ist er abgelaufen. Bitte versuche noch mal."
			];
		}

		$otrkeyFile = OtrkeyFile::where('name', '=', $filename)->first();
		if(!$otrkeyFile) {
			return [
				'status' => 'NOK',
				"message"  => "Datei $filename konnte leider nicht nicht gefunden werden."
			];
		}

		if(!Cache::get($token)) {
			$statService->trackDownload($otrkeyFile->id);
			Cache::put($token, 1, Carbon::now()->addHours(26));
		}

		try {
			$link = $downloadService->getDownloadLink($otrkeyFile, $downloadType);
			if($user) {
				$user->addTvProgramToList($otrkeyFile->tvProgram, User::WATCHED);
				$user->addTvProgramToList($otrkeyFile->tvProgram, User::DOWNLOADED);
			}

			Log::info("[user download] [username:$username] [type:$downloadType] [filename:$filename] [link:$link]");

			return ['status' => 'OK', 'link' => $link];
		} catch(NoCapacityDownloadException $e) {
			Log::info("[user download] [username:$username] [type:$downloadType] [filename:$filename] [NoCapacityDownloadException]");

			return ['status' => 'NOK', 'message' => 'Im Moment sind alle Server ausgelastet. Bitte versuche später noch mal oder kaufe ein Premium-Account.'];
		} catch(LimitExceededDownloadException $e) {
			Log::info("[user download] [username:$username] [type:$downloadType] [filename:$filename] [LimitExceededDownloadException]");

			// ToDo: Remove it
			return ['status' => 'NOK', 'message' => 'Der Limit ist für diesen Monat aufgebraucht. Bitte versuche später noch mal oder kaufe ein Premium-Account.'];
		} catch(QualityViolationDownloadException $e) {
			Log::info("[user download] [username:$username] [type:$downloadType] [filename:$filename] [QualityViolationDownloadException]");

			// ToDo: Remove it
			return ['status' => 'NOK', 'message' => 'Download in der ausgewählten Qualität ist nur mit Premium-Account möglich. Bitte kaufe ein Premium-Account.'];
		} catch(\Exception $e) {
			$message = $e->getMessage();
			Log::info("[user download] [username:$username] [type:$downloadType] [filename:$filename] [Exception: {$message}]");
			Log::error($e);

			return ['status' => 'NOK', 'message' => 'Es konnte kein Link generiert werden. Versuche bitte später noch mal.'];
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
		foreach($rows as $row) {
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
		foreach($files as $file) {
			if($file->isAvailable()) {
				try {
					$nodeService->deleteOtrkeyFile($file);
				} catch(\Exception $e) {
					Log::error($e);
					$errors[] = "Can't delete {$file->name}. " . $e->getMessage();
				}
			}
		}

		TvProgramsView::where('tv_program_id', '=', $tv_program_id)->delete();

		if(empty($errors)) flash('All files deleted.');

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
		if($film) {
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
		if(preg_match('/^tt[0-9]{7}$/', $film_id)) {
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
		if($tvProgram) {
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
		if($user) {
			// User list (favorites and watched)
			$lists = $user->getListsForTvPrograms($downloadsCurrentPage->lists('tv_program_id')->toArray());
		}

		return view('tvprogram/top100', compact('paginator', 'lists'));
	}


	public function table()
	{
		$date = Carbon::parse(Input::get('date', new Carbon()));
		$stationGroup = Input::get('station-group', 'public');

		switch ($stationGroup) {
			case 'public':
				$stations = ['ARTE','ARD','ZDF','ZDF NEO','3sat','ONE','BAY3','WDR','NDR','MDR','SWR','HR','RBB','ZDF INFO','ARDALPHA','KIKA','PHOENIX'];
				break;
			case 'others':
				$stations = ['ORF1','ORF2','ORF3','SF1','SF2','BIBELTV','FAMILYTV','NTV','SPORT1','VIVA'];
				break;
			default:
			case 'privat':
				$stations = ['PRO7','SAT1','RTL','VOX','TELE5','RTL2','PRO7MAXX','KABEL 1','RTLNITRO','DISNEY','SIXX','SRTL','3PLUS','4PLUS','DMAX','KABEL1DOKU','PULS8','NICKELODEON','RIC','RTLPLUS','SAT1GOLD','SERVUSTV','ZEEONE'];
				break;
		}


		$stations = ['PRO7','SAT1','RTL','VOX'];

		$data = \DB::table('tv_programs')
			->leftJoin('otrkey_files', 'tv_programs.id','=', 'otrkey_files.tv_program_id')
			->leftJoin('node_otrkeyfile', function($join) {
				$join->on('otrkey_files.id', '=', 'node_otrkeyfile.otrkeyfile_id')
					->where('node_otrkeyfile.status', '=', Node::STATUS_DOWNLOADED);
			})
			->leftJoin('tv_programs_view', 'tv_programs.id','=', 'tv_programs_view.tv_program_id')
			->whereIn('tv_programs.station', $stations)
			->where('tv_programs.start', '>=', $date->format('Y-m-d'))
			->where('tv_programs.start', '<', $date->copy()->addDay()->format('Y-m-d'))
			->groupBy('tv_programs.id')
			->orderBy('tv_programs.start')
			->select(
				\DB::raw('hour(tv_programs.start) as hour'),
				'tv_programs.id as tv_program_id',
				'tv_programs.start', 'tv_programs.title',
				'tv_programs.station',
				\DB::raw('max(node_otrkeyfile.node_id) as node_id'),
				\DB::raw("CASE WHEN GROUP_CONCAT(DISTINCT tv_programs_view.quality) LIKE '%mpg.HD.avi%' THEN 1 ELSE 0 END as hd"),
				'tv_programs_view.imdb_votes as imdb_votes',
				'tv_programs_view.imdb_rating as imdb_rating',
				'tv_programs_view.amazon_image as amazon_image'
			)
			->get();

		$hours = collect($data)->groupBy('hour')->keys()->toArray();
		$tvprogram = collect($data)->groupBy('station');
		//$stations = $tvprogram->keys()->toArray();

		foreach ($stations as $station) {
			if(!$tvprogram->has($station)) {
				$tvprogram->put($station, new Collection());
			}
			$prev[$station] = null;
		}
		//var_dump($tvprogram);exit;
		
		return view('tvprogram/table', compact('tvprogram', 'stations', 'date', 'hours', 'stationGroup', 'prev'));
	}


	public function tableData($station, $date)
	{
		$date = Carbon::parse($date);

		$data = \DB::table('tv_programs')
			->leftJoin('otrkey_files', 'tv_programs.id','=', 'otrkey_files.tv_program_id')
			->leftJoin('node_otrkeyfile', function($join) {
				$join->on('otrkey_files.id', '=', 'node_otrkeyfile.otrkeyfile_id')
					->where('node_otrkeyfile.status', '=', Node::STATUS_DOWNLOADED);
			})
			->leftJoin('tv_programs_view', 'tv_programs.id','=', 'tv_programs_view.tv_program_id')
			->where('tv_programs.station', '=', $station)
			->where('tv_programs.start', '>=', $date->format('Y-m-d'))
			->where('tv_programs.start', '<', $date->copy()->addDay()->format('Y-m-d'))
			->groupBy('tv_programs.id')
			->orderBy('tv_programs.start')
			->select(
				\DB::raw('hour(tv_programs.start) as hour'),
				'tv_programs.id as tv_program_id',
				'tv_programs.start', 'tv_programs.title',
				'tv_programs.station',
				\DB::raw('max(node_otrkeyfile.node_id) as node_id'),
				\DB::raw("CASE WHEN GROUP_CONCAT(DISTINCT tv_programs_view.quality) LIKE '%mpg.HD.avi%' THEN 1 ELSE 0 END as hd"),
				'tv_programs_view.imdb_votes as imdb_votes',
				'tv_programs_view.imdb_rating as imdb_rating',
				'tv_programs_view.amazon_image as amazon_image'
			)
			->get();

		$prevImgUrl = null;
		foreach ($data as &$rec) {
			$rec->hourFormated = date('H:i', strtotime($rec->start));
			$rec->link = url('tvprogram/show', ['id' => $rec->tv_program_id]);
			$rec->available = $rec->node_id > 0;

			if(!empty($rec->amazon_image)) {

				if(($rec->imdb_votes > 80000 || ($rec->imdb_votes > 24000 && $rec->imdb_rating > 5.7)) && $rec->amazon_image != $prevImgUrl) {
					$prevImgUrl = $rec->amazon_image;
				} else {
					$prevImgUrl = $rec->amazon_image;
					$rec->amazon_image = null;
				}
			}

			unset($rec->start);
			unset($rec->node_id);
			unset($rec->station);
			unset($rec->imdb_votes);
			unset($rec->imdb_rating);
			unset($rec->tv_program_id);

		}

		return $data;
	}


}
