<?php namespace App\Http\Controllers;

use App\TvProgramsView;
use \App;
use App\Services\TvProgramService;

class SystemController extends Controller
{


    function __construct()
    {
        $this->middleware('admin');
    }


    public function refreshTvProgramView(TvProgramService $tvProgramService)
    {
        set_time_limit(600);
        $tvProgramService->createView();
        $tvProgramService->createFilmView();
        flash('TV-Program and Film view refreshed');

        return redirect()->back();
    }


    public function generateSitemap()
    {
        /**
         * @var $sitemap \Roumen\Sitemap\Sitemap
         */
        $sitemap = App::make("sitemap");
        $sitemap->setCache('laravel.sitemap', 3600);

        if (!$sitemap->isCached()) {
            $tvPrograms = TvProgramsView::orderBy('start', 'desc')->groupBy('tv_program_id')->get();
            foreach ($tvPrograms as $tvProgram) {
                $sitemap->add('tvprogram/show/' . $tvProgram->tv_program_id, $tvProgram->start, '1.0', 'weekly');
            }
        }

        return $sitemap->render('xml');
    }
}