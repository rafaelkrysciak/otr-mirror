<?php namespace App\Http\Controllers;

use App\Distro;
use App\Film;
use App\FilmMapper;
use App\Filmstar;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Node;
use App\OtrkeyFile;
use App\Services\AwsS3Service;
use App\Services\CleanUpDatabase;
use App\Services\DistroService;
use App\Services\FilmMapperService;
use App\Services\ImdbService;
use App\Services\NodeService;
use App\Services\OtrEpgService;
use App\Services\OtrkeyFileService;
use App\Services\StatService;
use App\Services\TvProgramService;
use App\Services\XmltvService;
use App\Stat;
use App\TvProgram;
use App\TvProgramsView;
use Carbon\Carbon;
use Illuminate\Http\Request;
use \Log;
use Vinelab\Rss\Rss;

class CronController extends Controller
{


    /**
     *
     */
    function __construct()
    {
        set_time_limit(600);

        $this->middleware('auth.api');
        $this->middleware('singleton');
    }


    /**
     * @param NodeService $nodeService
     *
     * @return array
     */
    public function nodeStatus(NodeService $nodeService)
    {
        $nodes = Node::all();

        foreach ($nodes as $node) {
            try {
                $status = $nodeService->getNodeStatus($node);
                $node->free_disk_space = $status['freeDiskspace'];
                $node->busy_workers = $status['BusyWorkers'];
                $node->save();
            } catch (\Exception $e) {
                Log::error($e);
            }
        }

        return ['status' => 'OK'];

    }


    /**
     * @param DistroService $distroService
     * @param NodeService $nodeService
     *
     * @return array
     */
    public function nodeStartDownloads(DistroService $distroService, NodeService $nodeService)
    {
        $nodes = Node::active()->get()->getIterator();
        Log::info(__METHOD__." selected nodes");

        $files = OtrkeyFile::select('otrkey_files.*')
            ->with('distros')
            ->leftJoin('tv_programs', 'otrkey_files.tv_program_id', '=', 'tv_programs.id')
            ->forDownload()
            ->orderBy('tv_programs.highlight', 'desc')
            ->orderBy('otrkey_files.start', 'desc')
            ->limit($nodes->count() * 6)
            ->get();
        
        Log::info(__METHOD__." selected files");

        foreach ($files as $file) {
            Log::info(__METHOD__." Start processing file {$file->name}");

            $node = next($nodes) ?: reset($nodes);
            Log::info(__METHOD__." Node Selected: {$node->short_name}");

            $file->nodes()->detach($node);
            Log::info(__METHOD__." Detach file from node: {$node->short_name} {$file->name}");

            $file->nodes()->attach($node, ['status' => Node::STATUS_REQUESTED]);
            Log::info(__METHOD__." Attach file to node: {$node->short_name} {$file->name}");

            $distros = $file->distros->shuffle();

            foreach($distros as $distro) {
                $url = $distroService->generateDownloadLink($distro, $file->name);
                Log::info(__METHOD__." generateDownloadLink: $url");

                try {
                    $nodeService->fetchFile($node, $url, 2);
                    Log::info(__METHOD__." Download request to {$node->short_name} - download $url");
                    break;
                } catch (\Exception $e) {
                    Log::error(__METHOD__.' '.$e->getMessage());
                }
            }
        }

        return ['status' => 'OK'];
    }


    /**
     * @param OtrkeyFileService $otrkeyFileService
     * @param NodeService $nodeService
     * @param TvProgramService $tvProgramService
     *
     * @return array
     */
    public function nodeSyncFiles(OtrkeyFileService $otrkeyFileService, NodeService $nodeService, TvProgramService $tvProgramService)
    {
		set_time_limit(2400);
		
        $nodes = Node::get();
		// @ToDo: Workaround becaouse 500 Internal Server Error
        //$node = Node::get()->random();
		//$nodes = [$node];

        $startTotal = microtime(true);

        $count = 0;
        foreach ($nodes as $node) {
            try {
                $start = microtime(true);
                $count += $nodeService->refreshDatabase($node);
                Log::info(__METHOD__.' '.$node->short_name.' refreshDatabase done '.(microtime(true) - $start).'ms');
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        try {
            $start = microtime(true);
            $otrkeyFileService->matchTvPrograms();
            Log::info(__METHOD__.' matchTvPrograms done '.(microtime(true) - $start).'ms');
        } catch (\Exception $e) {
            Log::error($e);
        }

        try {
            $start = microtime(true);
            $tvProgramService->createView();
            Log::info(__METHOD__.' createView done '.(microtime(true) - $start).'ms');

            $start = microtime(true);
            $tvProgramService->createFilmView();
            Log::info(__METHOD__.' createFilmView done '.(microtime(true) - $start).'ms');
        } catch (\Exception $e) {
            Log::error($e);
        }

        Log::info(__METHOD__.' done total '.(microtime(true) - $startTotal).'ms');

        return ['status' => 'OK', 'node' => $node->short_name];
    }


    /**
     * @param StatService $statService
     * @param NodeService $nodeService
     *
     * @return array
     */
    public function nodeDeleteOldFiles(StatService $statService, NodeService $nodeService)
    {
        if ($nodeService->getAverageFreeDiskSpace() > 30 * 1024 * 1024 * 1024) {
            Log::notice('[nodeDeleteOldFiles] the average free disk space over 30GB. Delete aborted');
            return ['status' => 'OK', 'message' => 'The average free disk space over 30GB. Delete aborted.'];
        }

        $nodeService->deleteOldFiles($statService, 100);

        return ['status' => 'OK'];
    }


    /**
     * @param NodeService $nodeService
     *
     * @return array
     */
    public function nodeRebalance(NodeService $nodeService)
    {
        $nodeService->rebalance();
        return ['status' => 'OK'];
    }


    /**
     * @param OtrEpgService $epgService
     *
     * @param FilmMapperService $filmMapperService
     * @param XmltvService $xmltvService
     *
     * @return array
     */
    public function readEpgData(OtrEpgService $epgService, FilmMapperService $filmMapperService, XmltvService $xmltvService)
    {
        set_time_limit(2400);

        $count = $epgService->loadDays(9);
        Log::info($count . ' EPG Rows loaded');

        // Read guide.xml data
        $xmltvService->load();
        $xmltvService->matchTvPrograms();

        $epgService->consolidateTvPrograms();

        // Map TvPrograms to Films
        $numRows = $filmMapperService->mapAll();
        Log::info($numRows . ' Tv-Programs mapped');

        return ['status' => 'OK'];
    }


    /**
     * @param DistroService $distroService
     *
     * @return array
     */
    public function distroSyncFiles(DistroService $distroService)
    {
        set_time_limit(1200);

        $distros = Distro::all();

        $count = 0;
        foreach ($distros as $distro) {
            try {
                $count += $distroService->fillDatabase($distro);
            } catch (\Exception $e) {
                Log::error('[distroSyncFiles] Fail to sync with '.$distro->host.' Error:'.$e->getMessage());
                Log::debug($e);
            }
        }

        Log::info("$count files updated in ");

        return ['status' => 'OK'];
    }


    /**
     * @param AwsS3Service $awsS3Service
     *
     * @return array
     */
    public function awsSyncFiles(AwsS3Service $awsS3Service)
    {
        $count = $awsS3Service->syncFiles();
        Log::info("$count files found on AWS");

        return ['status' => 'OK'];
    }


    /**
     * @param AwsS3Service $awsS3Service
     *
     * @return array
     */
    public function awsDeleteOldFiles(AwsS3Service $awsS3Service)
    {
        $count = $awsS3Service->deleteOldFiles();
        Log::info("$count files deleted from AWS:S3");

        return ['status' => 'OK'];
    }


    /**
     * Delete old database records
     *
     * @param CleanUpDatabase $cleanUpDatabase
     *
     * @return array
     */
    public function cleanDatabase(CleanUpDatabase $cleanUpDatabase, NodeService $nodeService)
    {
        try {
            $deleted = $cleanUpDatabase->cleanOtrkeyFilesRecords();
            Log::info("$deleted OtrkeyFiles deleted.");
        } catch (\Exception $e) {
            Log::error($e);
        }

        try {
            $deleted = $cleanUpDatabase->cleanTvProgramTable();
            Log::info("$deleted TvPrograms deleted.");
        } catch (\Exception $e) {
            Log::error($e);
        }

        try {
            $deleted = $cleanUpDatabase->cleanOtrkeyFileNodeRelation();
            Log::info("$deleted Node Relations deleted.");
        } catch (\Exception $e) {
            Log::error($e);
        }

	    try {
		    $deleted = $cleanUpDatabase->cleanEpgProgramTable();
		    Log::info("$deleted EPG-programs deleted.");
	    } catch (\Exception $e) {
		    Log::error($e);
	    }

        $nodes = Node::get();
        foreach ($nodes as $node) {
            try {
                $result = $nodeService->clean($node);
                Log::info($result);
            } catch (\Exception $e) {
                Log::error($e);
            }
        }

        Stat::populateFromStatDownloads();

        return ['status' => 'OK'];
    }


    public function refreshImdbData(ImdbService $imdbService)
    {
        set_time_limit(1200);
        $films = Film::select('films.*')
            ->join('tv_programs', 'films.id', '=', 'tv_programs.film_id')
            ->join('otrkey_files', 'otrkey_files.tv_program_id', '=', 'tv_programs.id')
            ->join('node_otrkeyfile', 'otrkey_files.id', '=', 'node_otrkeyfile.otrkeyfile_id')
            ->where('node_otrkeyfile.status', '=', 'downloaded')
            ->where('films.imdb_last_update', '<', Carbon::now()->subDays(14))
            ->groupBy('films.id')
            ->orderBy('imdb_last_update')
            ->limit(200)
            ->get();

        foreach ($films as $film) {
            try {
                $imdbData = $imdbService->getImdbData($film->imdb_id);
                $updateData = [
                    'imdb_rating'      => $imdbData['imdb_rating'],
                    'imdb_votes'       => $imdbData['imdb_votes'],
                    'imdb_last_update' => Carbon::now(),
                ];

                if (empty($film->imdb_image)) {
                    $updateData['imdb_image'] = $imdbData['imdb_image'];
                }

                if (empty($film->year) && !empty($imdbData['year'])) {
                    $updateData['year'] = $imdbData['year'];
                }

                if (empty($film->country) && !empty($imdbData['country'])) {
                    $updateData['country'] = $imdbData['country'];
                }

                if (empty($film->director) && !empty($imdbData['director'])) {
                    $updateData['director'] = $imdbData['director'];
                }

                if (empty($film->genre) && !empty($imdbData['genre'])) {
                    $updateData['genre'] = $imdbData['genre'];
                }

                if (empty($film->fsk)) {
                    $updateData['fsk'] = $imdbData['fsk'];
                }
                if (empty($film->original_title)) {
                    $updateData['original_title'] = $imdbData['original_title'];
                }

                $film->update($updateData);
            } catch (\Exception $e) {
                \Log::error($e);
            }
        }

        return ['status' => 'OK'];
    }


    public function findMapperRules(ImdbService $imdbService, FilmMapperService $filmMapperService)
    {
        set_time_limit(1200);

        $filmMapperService->mapAll();

        $tvProgramViewRecords = TvProgramsView::whereNull('tv_programs.film_id')
            ->join('tv_programs', 'tv_programs_view.tv_program_id','=','tv_programs.id')
            ->whereNull('tv_programs.proposed_film_id')
            ->whereIn('tv_programs_view.language', ['Deutsch', 'Englisch'])
            ->where('tv_programs_view.length', '>', 15)
            ->where('tv_programs_view.start', '>', Carbon::now()->subDays(10))
            //->where('tv_programs_view.length', '>', 75)
            ->groupBy('tv_programs.org_title')
            ->orderBy('tv_programs_view.start', 'desc')
            ->limit(100)
            ->lists('tv_program_id');

        foreach ($tvProgramViewRecords as $tvProgramId) {
            try {
                $tvProgram = TvProgram::findOrFail($tvProgramId);
                $q = $tvProgram->title . ' ' . $tvProgram->director;
                $q .= $tvProgram->year > 0 ? ' ' . $tvProgram->year : '';
                $imdbId = $imdbService->searchWithGoogle($q, $tvProgram->language);
                if ($imdbId) {
                    $imdbId = $imdbService->getSeriesIdIfEpisode($imdbId);
                    $film = Film::byImdbId($imdbId)->first();
                    if (!$film) { // create

                        $film = Film::create($imdbService->getImdbData($imdbId));
                        Filmstar::createCast($film, $imdbService->cast($imdbId, 20));
                    }
                    $mapper = FilmMapper::createFromTvProgram($tvProgram, $film);
                    $filmMapperService->map($mapper);
                } else {
                    $tvProgram->film_id = 0;
                    $tvProgram->save();
                }
            } catch (\Exception $e) {
                \Log::error($e);
            }
        }
        return ['status' => 'OK'];
    }


    public function generateSitemap()
    {
        /**
         * @var $sitemap \Roumen\Sitemap\Sitemap
         */
        $sitemap = \App::make("sitemap");

        $tvPrograms = TvProgramsView::orderBy('start', 'desc')->groupBy('tv_program_id')->get();

        foreach ($tvPrograms as $tvProgram) {
            $sitemap->add(\URL::to('tvprogram/show/' . $tvProgram->tv_program_id), $tvProgram->start, '1.0', 'weekly');
        }

        $sitemap->store('xml', 'sitemap');

        return ['status' => 'OK'];
    }


    public function readHighlights(Rss $rss)
    {
        $feed = $rss->feed('http://www.onlinetvrecorder.com/rss/highlights_future.php');
        foreach ($feed->articles() as $article) {
            try {
                TvProgram::where('otr_epg_id','=',$article->epg_id)
                    ->update(['highlight' => true]);
            } catch(\Exception $e) {
                \Log::error($e);
            }
        }
        return ['status' => 'OK'];
    }


	/**
	 * Create the TNT search index by calling artisan command
	 */
	public function createSearchIndex()
	{
		set_time_limit(2400);
		\Artisan::call('index:tvprograms');
    }
}
