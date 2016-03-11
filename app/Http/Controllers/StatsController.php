<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Node;
use App\Services\StatService;
use App\Stat;
use App\StatDownload;
use App\StatView;
use App\TvProgramsView;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatsController extends Controller
{


    public function __construct()
    {
        $this->middleware('admin');
    }


    public function getIndex(StatService $statService)
    {
        $days = 60;

        $viewStatsData = $statService->viewsPerDay($days);
        $downloadStatsData = $statService->downloadsPerDay($days);
        $topStatsions = $statService->topStationsByDownloads(30, 20);

        return view('stats.index', compact('viewStatsData', 'downloadStatsData', 'topStatsions'));
    }


    public function getViewsAndDownloads(StatService $statService)
    {
        $days = 60;
        $statsData = $statService->viewsAndDownloadsPerDay($days);

        return $statsData;
    }


    public function getTopStations(StatService $statService)
    {
        $topStatsions = $statService->topStationsByDownloads(30, 30);

        return $topStatsions;
    }


    public function getTopStationsByAvgDownload(StatService $statService)
    {
        $topStatsions = $statService->topStationsByAvgDownloads(30, 30);

        return $topStatsions;
    }


    public function getNodeStats()
    {
        return Node::orderBy('short_name')
            ->get(['short_name', 'free_disk_space', 'busy_workers', 'updated_at'])
            ->toArray();
    }


    public function getDownloadsByQuality()
    {
        $rows = Stat::period(7)
            ->groupBy('quality')
            ->orderBy('downloads')
            ->get(['quality', \DB::raw("sum(downloads) as downloads")])
            ->toArray();

        $quality = [];
        foreach ($rows as $row) {
            $quality[] = [$row['quality'], (int) $row['downloads']];
        }
        return $quality;
    }

    public function getDownloadsByLanguage()
    {
        $rows = Stat::period(7)
            ->groupBy('language')
            ->orderBy('downloads')
            ->get(['language', \DB::raw("sum(downloads) as downloads")])
            ->toArray();

        $data = [];
        foreach ($rows as $row) {
            $data[] = [$row['language'], (int) $row['downloads']];
        }
        return $data;
    }


    public function getContentSizeByLanguage()
    {
        $rows = TvProgramsView::groupBy('language')
            ->orderBy('size')
            ->get(['language', \DB::raw("sum(size) as size")])
            ->toArray();

        $data = [];
        foreach ($rows as $row) {
            $lang = is_null($row['language']) ? 'Unknown' : $row['language'];
            $data[] = [$lang, ceil(((float)$row['size'])/pow(1024,3))];
        }
        return $data;
    }


    public function getContentSizeByQuality()
    {
        $rows = TvProgramsView::groupBy('quality')
            ->orderBy('size')
            ->get(['quality', \DB::raw("sum(size) as size")])
            ->toArray();

        $data = [];
        foreach ($rows as $row) {
            $lang = is_null($row['quality']) ? 'Unknown' : $row['quality'];
            $data[] = [$lang, ceil(((float)$row['size'])/pow(1024,3))];
        }
        return $data;
    }


    public function getStatsUpdate()
    {
        Stat::populateFromStatDownloads();
    }
}
