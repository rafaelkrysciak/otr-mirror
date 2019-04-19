<?php namespace App\Http\Controllers;

use App\OtrkeyFile;
use App\Promotion;
use App\Services\StatService;
use App\StatDownload;
use \Cache;
use \Input;

class HomeController extends Controller {

	/**
	 * Show the application dashboard to the user.
	 *
	 * @param StatService $statService
	 *
	 * @return Response
	 */
	public function index(StatService $statService)
	{
		if(Input::has('file')) {
			$filename = Input::get('file');
			$file = OtrkeyFile::filename($filename)->first();
			if($file) {
				return redirect('tvprogram/show/'.$file->tv_program_id);
			} else {
				flash()->warning("Datei {$filename} nicht gefunden");
			}
		}

		$views = Cache::remember('HomeTopViews', 60, function() use ($statService) {
			return $statService->topViews();
		});

		$downloads = Cache::remember('HomeTopDownloads', 60, function() use ($statService) {
			return $statService->topDownloads();
		});

		$promotions = Promotion::with('tvProgram')->active()->orderBy('position')->get();

		return view('home', compact('promotions', 'views', 'downloads'));
	}
	
	
	public function impressum()
	{
		return view('impressum');
	}
	
	
	public function privacyPolicy()
	{
		return view('privacy_policy');
	}
	

	public function faq()
	{
		$contact = ['name' => '', 'email' => ''];
		if(\Auth::user()) {
			$contact = [
				'name' => \Auth::user()->name,
				'email' => \Auth::user()->email
			];
		}

		return view('faq', compact('contact'));
	}


	public function news()
	{
		\Session::set('news_seen', true);
		return view('news');
	}
}
