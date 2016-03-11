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
        $nodes = Node::all()->getIterator();

        $files = OtrkeyFile::with('distros')
            ->forDownload()
            ->orderBy('start')
            ->limit($nodes->count() * 5)
            ->get();

        foreach ($files as $file) {
            $node = next($nodes) ?: reset($nodes);

            $file->nodes()->detach($node);
            $file->nodes()->attach($node, ['status' => Node::STATUS_REQUESTED]);

            $url = $distroService->generateDownloadLink($file->distros->first(), $file->name);

            try {
                $nodeService->fetchFile($node, $url);
                Log::info("Download request to {$node->short_name} - download $url");
            } catch (\Exception $e) {
                Log::error($e);
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
		
        //$nodes = Node::get();
		// @ToDo: Workaround becaouse 500 Internal Server Error
		$nodeIds = [1,2,3,4];
		$key = array_rand($nodeIds);
		$node = Node::find($nodeIds[$key]);
		$nodes = [$node];

        $count = 0;
        foreach ($nodes as $node) {
            try {
                $count += $nodeService->refreshDatabase($node);
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        try {
            $otrkeyFileService->matchTvPrograms();
        } catch (\Exception $e) {
            Log::error($e);
        }

        try {
            $tvProgramService->createView();
            $tvProgramService->createFilmView();
        } catch (\Exception $e) {
            Log::error($e);
        }

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
            Log::notice('[nodeDeleteOldFiles] the avarage free disk space over 30GB. Delete aborted');
            return ['status' => 'OK'];
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
                Log::notice($e);
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

                if (empty($film->year)) {
                    $updateData['year'] = $imdbData['year'];
                }

                if (empty($film->country)) {
                    $updateData['country'] = $imdbData['country'];
                }

                if (empty($film->director)) {
                    $updateData['director'] = $imdbData['director'];
                }

                if (empty($film->genre)) {
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
                $imdbId = $imdbService->getSeriesIdIfEpisode($imdbId);
                if ($imdbId) {
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

}
