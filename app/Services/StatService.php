<?php namespace App\Services;

use App\Node;
use App\OtrkeyFile;
use App\StatDownload;
use App\StatView;
use App\TvProgram;
use App\TvProgramsView;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Session;
use \DB;
use \Cache;

class StatService
{

    /**
     * @param int $limit
     * @param int $days
     *
     * @return Collection
     */
    public function topDownloads($limit = 10, $days = 7)
    {
        $tvProgramIds = DB::table('stat_downloads')
            ->leftJoin('tv_programs_view', 'stat_downloads.otrkey_file_id', '=', 'tv_programs_view.otrkeyfile_id')
            ->where('stat_downloads.event_date', '>', Carbon::now()->subDays($days))
            ->whereNotNull('tv_programs_view.tv_program_id')
            ->groupBy('tv_programs_view.tv_program_id')
            ->orderByRaw('sum(downloads) desc')
            ->limit($limit)
            ->lists('tv_programs_view.tv_program_id');

        $collection = new Collection();

        foreach ($tvProgramIds as $tvProgramId) {
            $item = TvProgramsView::where('tv_program_id', $tvProgramId)->first();
            $collection->add($item);
        }

        return $collection;
    }


    /**
     * @param int $limit
     * @param int $days
     *
     * @return Collection
     */
    public function topViews($limit = 10, $days = 7)
    {
        $tvProgramIds = DB::table('stat_views')
            ->where('event_date', '>', Carbon::now()->subDays($days))
            ->whereIn('tv_program_id', function ($query) {
                $query->select('tv_program_id')->from('tv_programs_view');
            })
            ->groupBy('tv_program_id')
            ->orderByRaw('sum(views) desc')
            ->limit($limit)
            ->lists('tv_program_id');

        $collection = new Collection();

        foreach ($tvProgramIds as $tvProgramId) {
            $item = TvProgramsView::where('tv_program_id', $tvProgramId)->first();
            $collection->add($item);
        }

        return $collection;
    }


    /**
     * Get list of OtrkeyFiles order by download count
     *
     * @return Collection of OtrkeyFile
     */
    public function getFilesForDelete($limit = 100)
    {
        $files = OtrkeyFile::leftJoin('stat_downloads', 'stat_downloads.otrkey_file_id', '=', 'otrkey_files.id')
            ->whereIn('otrkey_files.id', function ($query) {
                $query->select('otrkeyfile_id')
                    ->from('node_otrkeyfile')
                    ->where('status', '=', Node::STATUS_DOWNLOADED);
            })
            ->where('start', '<', Carbon::now()->subDays(14))
            ->where(function($query) {
                $query->whereNull('stat_downloads.event_date')
                    ->orWhere('stat_downloads.event_date','>',Carbon::now()->subMonths(2));
            })
            ->groupBy('otrkey_files.id')
            ->orderByRaw('SUM(downloads) + SUM(aws_downloads), otrkey_files.start')
            ->limit($limit)
            ->get(['otrkey_files.*']);

        return $files;
    }


    /**
     * @param $tv_program_id
     */
    public function trackView($tv_program_id)
    {
        $views = Session::get('views', []);

        $stat = $this->getStatViewRecord($tv_program_id);

        $stat->total_views++;

        if (!in_array($tv_program_id, $views)) {
            $stat->views++;
            Session::push('views', $tv_program_id);
        }

        $stat->save();

    }


    /**
     * @param $otrkey_file_id
     */
    public function trackDownload($otrkey_file_id)
    {
        $downloads = Session::get('downloads', []);

        $stat = $this->getStatDownloadRecord($otrkey_file_id);

        $stat->total_downloads++;

        if (!in_array($otrkey_file_id, $downloads)) {
            $stat->downloads++;
            Session::push('downloads', $otrkey_file_id);
        }

        $stat->save();
    }


    /**
     * @param $otrkey_file_id
     */
    public function trackAwsDownload($otrkey_file_id)
    {
        $downloads = Session::get('downloads', []);

        $stat = $this->getStatDownloadRecord($otrkey_file_id);

        $stat->aws_total_downloads++;

        if (!in_array($otrkey_file_id, $downloads)) {
            $stat->aws_downloads++;
            $downloads[] = $otrkey_file_id;
            Session::push('downloads', $otrkey_file_id);
        }

        $stat->save();
    }


    /**
     * get the stats db record
     *
     * @param $otrkey_file_id
     *
     * @return \App\StatDownload
     */
    protected function getStatDownloadRecord($otrkey_file_id)
    {
        return StatDownload::firstOrCreate([
            'event_date'     => Carbon::now()->format('Y-m-d'),
            'otrkey_file_id' => $otrkey_file_id
        ]);
    }


    /**
     * @param $tv_program_id
     *
     * @return \App\StatView
     */
    protected function getStatViewRecord($tv_program_id)
    {
        return StatView::firstOrCreate([
            'event_date'    => Carbon::now()->format('Y-m-d'),
            'tv_program_id' => $tv_program_id
        ]);
    }


    /**
     * @param int $days
     *
     * @return array
     */
    public function viewsPerDay($days = 30)
    {
        $viewStats = StatView::selectRaw('event_date, SUM(views) as sum')
            ->groupBy('event_date')
            ->orderBy('event_date', 'desc')
            ->limit($days)
            ->lists('sum', 'event_date');

        $viewStats = array_reverse($viewStats);

        return $viewStats;
    }


    /**
     * @param int $days
     *
     * @return array
     */
    public function downloadsPerDay($days = 30)
    {
        $downloadStats = StatDownload::selectRaw('event_date, SUM(downloads) as sum')
            ->groupBy('event_date')
            ->orderBy('event_date', 'desc')
            ->limit($days)
            ->lists('sum', 'event_date');

        $downloadStats = array_reverse($downloadStats);

        return $downloadStats;
    }


    /**
     * @param int $days
     *
     * @return array
     */
    public function viewsAndDownloadsPerDay($days = 30)
    {
        $downloads = $this->downloadsPerDay($days);
        $views = $this->viewsPerDay($days);


        $data = [];

        foreach ($downloads as $date => $value) {
            $row = [strtotime($date) * 1000, (int)$value];

            if (array_key_exists($date, $views)) {
                $row[] = (int)$views[$date];
            } else {
                $row[] = null;
            }

            $data[$date] = $row;
        }


        foreach ($views as $date => $value) {
            if (!array_key_exists($date, $data)) {
                $data[$date] = [strtotime($date) * 1000, null, (int)$value];
            }
        }

        ksort($data);

        return array_values($data);
    }


    /**
     * @param int $days
     * @param int $limit
     *
     * @return array
     */
    public function topStationsByDownloads($days = 7, $limit = 10)
    {
        $cacheKey = __METHOD__ . ':' . $days . ':' . $limit;

        $topStations = Cache::remember($cacheKey, 60, function () use ($days, $limit) {

            return StatDownload::leftJoin('otrkey_files', 'stat_downloads.otrkey_file_id', '=', 'otrkey_files.id')
                ->leftJoin('stations', 'otrkey_files.station', '=', 'stations.otrkeyfile_name')
                ->where('stat_downloads.event_date', '>', Carbon::now()->subDays($days))
                ->groupBy('stations.tvprogram_name')
                ->orderByRaw('sum(stat_downloads.downloads)+sum(stat_downloads.aws_downloads) DESC')
                ->limit($limit)
                ->get(['stations.tvprogram_name', DB::raw('sum(stat_downloads.downloads)+sum(stat_downloads.aws_downloads) as sum')])
                ->toArray();
        });

        $topStationsData = [];
        foreach ($topStations as $station) {
            $topStationsData[] = [$station['tvprogram_name'], (int)$station['sum']];
        }

        return $topStationsData;
    }


    /**
     * @param int $days
     * @param int $limit
     *
     * @return array
     */
    public function topStationsByAvgDownloads($days = 7, $limit = 10)
    {
        $cacheKey = __METHOD__ . ':' . $days . ':' . $limit;

        $topStations = Cache::remember($cacheKey, 60, function () use ($days, $limit) {

            return StatDownload::leftJoin('otrkey_files', 'stat_downloads.otrkey_file_id', '=', 'otrkey_files.id')
                ->leftJoin('stations', 'otrkey_files.station', '=', 'stations.otrkeyfile_name')
                ->where('stat_downloads.event_date', '>', Carbon::now()->subDays($days))
                ->groupBy('stations.tvprogram_name')
                ->orderByRaw('avg(stat_downloads.downloads+stat_downloads.aws_downloads) DESC')
                ->limit($limit)
                ->get(['stations.tvprogram_name', DB::raw('avg(stat_downloads.downloads+stat_downloads.aws_downloads) as avg')])
                ->toArray();
        });

        $topStationsData = [];
        foreach ($topStations as $station) {
            $topStationsData[] = [$station['tvprogram_name'], (float)$station['avg']];
        }

        return $topStationsData;
    }


}