<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Node;
use App\OtrkeyFile;
use App\PaypalTransaction;
use App\Services\StatService;
use App\Stat;
use App\StatDownload;
use App\TvProgramsView;
use App\User;
use Carbon\Carbon;

/**
 * Class StatsController
 * @package App\Http\Controllers
 */
class StatsController extends Controller
{


	/**
	 *
	 */
	public function __construct()
	{
		$this->middleware('admin');
	}


	/**
	 * @param StatService $statService
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function getIndex(StatService $statService)
	{
		$days = 60;

		$viewStatsData = $statService->viewsPerDay($days);
		$downloadStatsData = $statService->downloadsPerDay($days);
		$topStatsions = $statService->topStationsByDownloads(30, 20);

		return view('stats.index', compact('viewStatsData', 'downloadStatsData', 'topStatsions'));
	}


	/**
	 * @return array
	 */
	public function getPayments()
	{
		$data = PaypalTransaction::select(\DB::raw("date_format(ordertime, '%Y/%m') as month"), \DB::raw('SUM(amt) as amount'), \DB::raw('COUNT(*) as count'))
			->groupBy(\DB::raw("date_format(ordertime, '%Y%m')"))
			->orderBy(\DB::raw("date_format(ordertime, '%Y%m')"))
			->get();

		$payments = [];
		foreach($data as $payment) {
			$payments[] = [$payment['month'], $payment['amount'], $payment['count']];
		}

		return $payments;
	}


	/**
	 * @return array
	 */
	public function getRegistrations()
	{
		$data = User::select(
			\DB::raw("date_format(created_at, '%Y/%m') as month"),
			\DB::raw('count(CASE WHEN confirmed = 1 then 1 ELSE NULL END) as confirmed'),
			\DB::raw('count(CASE WHEN confirmed = 1 then NULL ELSE 1 END) as notconfirmed'))
			->groupBy(\DB::raw("date_format(created_at, '%Y%m')"))
			->orderBy(\DB::raw("date_format(created_at, '%Y%m')"))
			->get();

		$registrations = [];
		foreach($data as $registration) {
			$registrations[] = [$registration['month'], $registration['confirmed'], $registration['notconfirmed']];
		}

		return $registrations;

	}


	/**
	 * @param StatService $statService
	 *
	 * @return array
	 */
	public function getViewsAndDownloads(StatService $statService)
	{
		$days = 60;
		$statsData = $statService->viewsAndDownloadsPerDay($days);

		return $statsData;
	}


	/**
	 * @param StatService $statService
	 *
	 * @return array
	 */
	public function getTopStations(StatService $statService)
	{
		$topStatsions = $statService->topStationsByDownloads(30, 30);

		return $topStatsions;
	}


	/**
	 * @param StatService $statService
	 *
	 * @return array
	 */
	public function getTopStationsByAvgDownload(StatService $statService)
	{
		$topStatsions = $statService->topStationsByAvgDownloads(30, 30);

		return $topStatsions;
	}


	/**
	 * @return mixed
	 */
	public function getNodeStats()
	{
		return Node::orderBy('short_name')
			->get(['short_name', 'free_disk_space', 'busy_workers', 'updated_at'])
			->toArray();
	}


	/**
	 * @return array
	 */
	public function getDownloadsByQuality()
	{
		$rows = Stat::period(7)
			->groupBy('quality')
			->orderBy('downloads')
			->get(['quality', \DB::raw("sum(downloads) as downloads")])
			->toArray();

		$quality = [];
		foreach($rows as $row) {
			$quality[] = [$row['quality'], (int) $row['downloads']];
		}

		return $quality;
	}


	/**
	 * @param $tvProgramId
	 * @return array
	 */
	public function getDownloadsByTvProgramId($tvProgramId)
	{
		$rows = StatDownload::whereIn('otrkey_file_id', function($query) use ($tvProgramId) {
			$query->select(['id'])
				->from('otrkey_files')
				->where('tv_program_id','=',$tvProgramId);
		})
			->where('event_date','>',Carbon::now()->subDays(60))
			->orderBy('event_date')
			->groupBy('event_date')
			->get(['event_date',\DB::raw('sum(downloads) as downloads')]);

		$data = [];
		foreach($rows as $row) {
			$data[] = [strtotime($row['event_date'])*1000, (int) $row['downloads']];
		}


		return $data;
	}


	/**
	 * @return array
	 */
	public function getDownloadsByLanguage()
	{
		$rows = Stat::period(7)
			->groupBy('language')
			->orderBy('downloads')
			->get(['language', \DB::raw("sum(downloads) as downloads")])
			->toArray();

		$data = [];
		foreach($rows as $row) {
			$data[] = [$row['language'], (int) $row['downloads']];
		}

		return $data;
	}


	/**
	 * @return array
	 */
	public function getContentSizeByLanguage()
	{
		$rows = TvProgramsView::groupBy('language')
			->orderBy('size')
			->get(['language', \DB::raw("sum(size) as size")])
			->toArray();

		$data = [];
		foreach($rows as $row) {
			$lang = is_null($row['language']) ? 'Unknown' : $row['language'];
			$data[] = [$lang, ceil(((float) $row['size']) / pow(1024, 3))];
		}

		return $data;
	}


	/**
	 * @return array
	 */
	public function getContentSizeByQuality()
	{
		$rows = TvProgramsView::groupBy('quality')
			->orderBy('size')
			->get(['quality', \DB::raw("sum(size) as size")])
			->toArray();

		$data = [];
		foreach($rows as $row) {
			$lang = is_null($row['quality']) ? 'Unknown' : $row['quality'];
			$data[] = [$lang, ceil(((float) $row['size']) / pow(1024, 3))];
		}

		return $data;
	}


	public function getFileCountByDay()
	{
		set_time_limit(600);

		$data = \DB::table('otrkey_files')
			->where('start', '>', Carbon::now()->subDay(60)->setTime(0,0,0))
			->whereIn('id', function ($query) {
				$query->select(['otrkeyfile_id'])
					->from('node_otrkeyfile')
					->where('status', '=', Node::STATUS_DOWNLOADED);
			})
			->groupBy(\DB::raw('1'))
			->orderBy(\DB::raw('1'))
			->select([\DB::raw("date(start) as date"), \DB::raw("count(*) as count")])
			->get();

		return $data;
	}

	/**
	 *
	 */
	public function getStatsUpdate()
	{
		Stat::populateFromStatDownloads();
	}
}
