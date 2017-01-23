<?php namespace App\Services;


use App\Node;
use App\OtrkeyFile;
use App\TvProgramsView;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use JsonRPC\Client;
use Log;
use DB;


/**
 * Class NodeService
 * @package App\Services
 */
class NodeService
{

	/**
	 * @var OtrkeyFileService
	 */
	protected $otrkeyFileService;


	/**
	 * @param OtrkeyFileService $otrkeyFileService
	 */
	function __construct(OtrkeyFileService $otrkeyFileService)
	{
		$this->otrkeyFileService = $otrkeyFileService;
	}


	/**
	 * @param bool $activeOnly
	 * @return mixed
	 */
	public function getTotalFreeDiskSpace($activeOnly = true)
	{
		if($activeOnly) {
			return Node::active()->sum('free_disk_space');
		} else {
			return Node::sum('free_disk_space');
		}
	}


	/**
	 * @param bool $activeOnly
	 * @return float
	 */
	public function getAverageFreeDiskSpace($activeOnly = true)
	{

		if($activeOnly) {
			return Node::active()->sum('free_disk_space') / Node::active()->count();
		} else {
			return Node::sum('free_disk_space') / Node::count();
		}

	}


	/**
	 * @param Node $node
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function getNodeStatus(Node $node)
	{
		$result = $this->execOrFail($node, 'nodeStatus', [], "Can't get status from node");

		return $result['data'];
	}


	/**
	 * @param Node $node
	 * @param $function
	 * @param array $params
	 * @param null $message
	 * @param int $timeout
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	protected function execOrFail(Node $node, $function, $params = [], $message = null, $timeout = 30)
	{
		$result = $this->exec($node, $function, $params, $message, $timeout);
		if (!is_array($result)) {
			throw new \Exception($node->short_name . ': ' . $message);
		}

		if ($result['status'] != 'OK') {
			$message = array_key_exists('message', $result) ? $result['message'] : $message;
			throw new \Exception($node->short_name . ': ' . $message);
		}

		return $result;
	}


	/**
	 * @param Node $node
	 * @param $function
	 * @param array $params
	 * @param null $message
	 *
	 * @return mixed
	 */
	protected function exec(Node $node, $function, $params = [], $message = null, $timeout = 30)
	{
		$client = new Client($node->url);
		$client->getHttpClient()->withHeaders(['X-Auth: ' . $node->key]);
		$client->getHttpClient()->withTimeout($timeout);

		return $client->execute($function, $params);
	}


	/**
	 * @param Node $node
	 * @param string $filename
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function fileExists(Node $node, $filename)
	{
		$result = $this->execOrFail($node, 'fileExists', [$filename], "File status unknown");
		return $result['data'];
	}


	/**
	 * @param StatService $statService
	 * @param int $count
	 */
	public function deleteOldFiles(StatService $statService, $count = 100)
	{
		$files = $statService->getFilesForDelete($count);
		foreach ($files as $file) {
			try {
				$this->deleteOtrkeyFile($file);
			} catch (\Exception $e) {
				Log::error($e);
			}
		}
	}


	/**
	 * @param OtrkeyFile $file
	 */
	public function deleteOtrkeyFile(OtrkeyFile $file)
	{
		foreach ($file->availableFiles as $node) {
			Log::info('Delete file '.$file->id.':'.$file->name.' from '.$node->short_name);
			$this->deleteFile($node, $file->name);
			$node->pivot->status = Node::STATUS_DELETED;
			$node->pivot->save();
		}
	}


	/**
	 * @param Node $node
	 * @param $filename
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function deleteFile(Node $node, $filename)
	{
		$result = $this->execOrFail($node, 'deleteFile', [$filename], "Can't delete file");

		return true;
	}


	/**
	 * @param Node $node
	 * @param $filename
	 * @param $url
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function ftpUpload(Node $node, $filename, $url)
	{
		$result = $this->execOrFail($node, 'ftpUpload', [$filename, $url], "Problem during upload");

		return $result;
	}


	/**
	 * @param Node $node
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function listFiles(Node $node)
	{
		return $this->execOrFail($node, 'listFiles', [], "Can't get file list from node");
	}

	/**
	 * Read files from node an update database status
	 *
	 * @param Node $node
	 *
	 * @return int
	 * @throws \Exception
	 */
	public function refreshDatabase(Node $node)
	{
		$keepFileIds = $node->otrkeyFiles()->wherePivot('status', '!=', 'downloaded')->lists('otrkeyfile_id')->toArray();

		$result = $this->execOrFail($node, 'listFiles', [], "Can't get file list from node");

		$files = [];
		foreach ($result['data'] as $data) {
			try {
				$file = OtrkeyFile::where('name', $data['filename'])->first();

				if (is_null($file)) {
					Log::info(__METHOD__.' File:'.$data['filename'].' not found.');
					$file = $this->otrkeyFileService->createByFilename($data['filename']);
					Log::info(__METHOD__.' File:'.$data['filename'].' record created (id:'.$file->id.')');
				}

				$file->size = $data['size'];
				$file->checksum = $data['md5sum'];
				$file->save();

				$files[$file->id] = ['status' => 'downloaded'];

				if (in_array($file->id, $keepFileIds)) {
					unset($keepFileIds[array_search($file->id, $keepFileIds)]);
				}
			} catch (\Exception $e) {
				Log::error($e->getMessage());
			}
		}

		foreach ($keepFileIds as $id) {
			$files[] = $id;
		}

		$node->otrkeyFiles()->sync($files);

		return count($result['data']);
	}


	/**
	 * @param $token
	 * @param $filename
	 * @param $downloadType
	 *
	 * @return bool
	 */
	public function validateDownloadToken($token, $filename, $downloadType)
	{
		for ($hour = 0; $hour < 26; $hour++) {
			$time = Carbon::now()->addHours($hour);
			if ($token == $this->generateDownloadToken($filename, $downloadType, $time)) {
				return true;
			}
		}

		return false;
	}


	/**
	 * @param $filename
	 * @param $downloadType
	 * @param Carbon $time
	 *
	 * @return string
	 */
	public function generateDownloadToken($filename, $downloadType, Carbon $time = null)
	{
		$time = $time ?: Carbon::now();
		$key = $downloadType . $time->format('YmdH') . $filename . Config::get('app.key');

		return substr(md5($key), 2, 10);
	}


	/**
	 * @param Node $node
	 * @param $downloadId
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function abortDownload(Node $node, $downloadId)
	{

		$downloads = $this->getRunningDownloads($node);

		foreach ($downloads as $download) {
			if ($download['id'] == $downloadId) break;
		}

		$this->execOrFail($node, 'breakAddFile', [$download['url']], "Can't abort the download");

		return true;
	}


	/**
	 * @param Node $node
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function getRunningDownloads(Node $node)
	{
		$result = $this->execOrFail($node, 'listDownloads', [], "Can't get downloads from node");

		return $result['data'];
	}


	/**
	 * @param Node $node
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function clean(Node $node)
	{
		$result = $this->execOrFail($node, 'clean', [], "Can't exec clean on node");

		return $result['data'];
	}


	protected function distributeFilesAmongNodes()
	{
		$nodes = Node::active()->get();

		$files = OtrkeyFile::with('nodes')
			->join('node_otrkeyfile', 'id', '=', 'otrkeyfile_id')
			->join('stations', 'otrkey_files.station', '=', 'stations.otrkeyfile_name')
			->join('tv_programs', 'otrkey_files.tv_program_id', '=', 'tv_programs.id')
			->availableInHq()
			->notOlderThen(Carbon::now()->subDays(config('hqm.keep_files_on_all_nodes_days', 4)))
			->where('node_otrkeyfile.status', '=', Node::STATUS_DOWNLOADED)
			->where('stations.language_short', '=', 'de')
			->where('tv_programs.highlight', '=', '1')
			->groupBy('otrkey_files.id')
			->having(DB::raw('count(*)'), '<', $nodes->count())
			->limit(50)
			->get(['otrkey_files.*']);

		foreach ($files as $file) {
			try {
				$url = $this->generateDownloadLink($file->availableFiles->random(), $file->name, DownloadService::PREMIUM);
				$missingNodes = $nodes->diff($file->nodes);
				foreach ($missingNodes as $node) {
					if (!config('app.debug')) {
						$this->fetchFile($node, $url, 2);
					}
					Log::debug("[rebalance] Download to {$node->short_name} : {$url}");
				}
			} catch (\Exception $e) {
				Log::error($e->getMessage());
			}
		}
	}


	protected function fillUpNode()
	{
		$nodes = Node::active()->get();

		// copy random files to a node with a lot of free space
		// usually a new node without much files
		// this prevent to have much files from the same period on one node
		$node = $nodes->sortByDesc('free_disk_space')->first();
		if ($node->free_disk_space > 100 * pow(1024, 3) && $this->getAverageFreeDiskSpace() < 100 * pow(1024, 3)) {
			$files = TvProgramsView::with('node')
				->where('start', '<', Carbon::now()->subDays(config('hqm.keep_files_on_all_nodes_days', 4)))
				->where('node_id', '!=', $node->id)
				->orderBy(DB::raw('rand()'))
				->limit(50)
				->get();
			foreach ($files as $file) {
				try {
					$url = $this->generateDownloadLink($file->node, $file->name, DownloadService::PREMIUM);
					if (!config('app.debug')) {
						$this->fetchFile($node, $url, 2);
					}
					Log::debug("[rebalance] Filling free node {$node->short_name} : {$url}");
				} catch(\Exception $e) {
					Log::error($e);
				}
			}
		}
	}


	protected function deleteDistributedFiles()
	{
		$files = OtrkeyFile::rightJoin('node_otrkeyfile', function($join) {
			$join->on('id', '=', 'otrkeyfile_id');
			$join->on('node_otrkeyfile.status', '=', DB::raw("'" . \App\Node::STATUS_DOWNLOADED . "'"));
		})
			->olderThen(Carbon::now()->subDays(config('hqm.keep_files_on_all_nodes_days', 4)))
			->groupBy('otrkey_files.id')
			->having(DB::raw('count(*)'), '>', 1)
			->limit(100)
			->get(['otrkey_files.*']);

		foreach ($files as $file) {
			$toDelete = $file->nodes
				->filter(function($value, $key) {
					return $value->pivot->status == Node::STATUS_DOWNLOADED;
				})
				->sortBy('free_disk_space');

			if($toDelete->count() == 1) {
				continue;
			}

			$toDelete = $toDelete->take($toDelete->count() - 1);

			try {

				// Race Condition: Deleted files are updated to Downloaded short after delete
				// Check if all the files that assumed as Downloaded really exists
				foreach ($toDelete as $node) {
					if($this->fileExists($node, $file->name) !== true) {
						Log::debug("[rebalance] Delete aborted {$file->id}:{$file->name}. File do not exist on node ".$node->short_name);
						$node->pivot->status = Node::STATUS_DELETED;
						$node->pivot->save();
						continue 2;
					}
				}

				foreach ($toDelete as $node) {
					if (!config('app.debug')) {

						$this->deleteFile($node, $file->name);

						$node->pivot->status = Node::STATUS_DELETED;
						$node->pivot->save();

						$node->free_disk_space = $node->free_disk_space + $file->size;
						$node->save();

					}

					Log::debug("[rebalance] Delete {$file->id}:{$file->name} from {$node->short_name}");
				}
			} catch (\Exception $e) {
				Log::error($e);
			}
		}
	}


	protected function unloadScrappedNodes()
	{
		$scrappedNodes = Node::scrapped()->get();

		if($scrappedNodes->count() == 0) {
			return;
		}

		$activeNodes = Node::active()->get()->getIterator();

		foreach($scrappedNodes as $scrappedNode) {
			// delete files which are available on other nodes
			//select otrkey_files.*
			//from node_otrkeyfile
			//	left join otrkey_files on node_otrkeyfile.otrkeyfile_id = otrkey_files.id and node_otrkeyfile.`status` = 'downloaded'
			//group by otrkey_files.id
			//having count(*) > 1

			$doubleFiles = OtrkeyFile::distributed()
				->join('node_otrkeyfile', 'id', '=', 'otrkeyfile_id')
				->where('node_otrkeyfile.node_id', $scrappedNode->id)
				->get(['otrkey_files.*']);

			foreach($doubleFiles as $doubleFile) {
				Log::info('[unloadScrappedNodes] [try to delete file] [File:'.$scrappedNode->short_name.':'.$doubleFile->name.']');
				$fileNode = $doubleFile->nodes->where('id',$scrappedNode->id)->first();
				if($fileNode and $this->fileExists($scrappedNode, $doubleFile->name) == true) {
					$this->deleteFile($scrappedNode, $doubleFile->name);
					$fileNode->pivot->status = Node::STATUS_DELETED;
					$fileNode->pivot->save();
					Log::info('[unloadScrappedNodes] [deleted file] [File:'.$scrappedNode->short_name.':'.$doubleFile->name.']');
				}
			}


			// move files to other nodes
			$filesToUnload = $scrappedNode->available()->take(50)->get();
			foreach($filesToUnload as $fileToUnload) {
				$node = next($activeNodes) ?: reset($activeNodes);
				try {
					$url = $this->generateDownloadLink($scrappedNode, $fileToUnload->name, DownloadService::PREMIUM);
					$this->fetchFile($node, $url, 2);
					Log::info('[unloadScrappedNodes] [Source:'.$scrappedNode->short_name.'] [Dest:'.$node->short_name.'] [File:'.$fileToUnload->name.']');
				} catch(\Exception $e) {
					Log::notice($e);
				}
			}
		}
	}
	

	/**
	 *
	 */
	public function rebalance()
	{
		// $this->distributeFilesAmongNodes();

		$this->fillUpNode();
		$this->unloadScrappedNodes();

		// delete files just in the night
		if (date('G') >= 23 || date('G') <= 5) {
		//if (true) {
			$this->deleteDistributedFiles();
		}
	}


	/**
	 * @param Node $node
	 * @param $filename
	 * @param string $userStatus
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function generateDownloadLink(Node $node, $filename, $userStatus = DownloadService::GUEST)
	{
		$result = $this->execOrFail($node, 'downloadLink', [$filename, $userStatus], "Can't get download link from node");

		return $result['url'];
	}


	/**
	 * @param Node $node
	 * @param string $url
	 * @param int $timeout
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function fetchFile(Node $node, $url, $timeout = 30)
	{
		return $this->execOrFail($node, 'addFile', [$url], "Download failed", $timeout);
	}
}