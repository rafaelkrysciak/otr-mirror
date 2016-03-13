<?php namespace App\Services;


use App\Node;
use App\OtrkeyFile;
use App\TvProgramsView;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use JsonRPC\Client;
use \DB;
use \Log;


class NodeService
{

    protected $otrkeyFileService;


    function __construct(OtrkeyFileService $otrkeyFileService)
    {
        $this->otrkeyFileService = $otrkeyFileService;
    }


    public function getTotalFreeDiskSpace()
    {
        return Node::sum('free_disk_space');
    }


    public function getAverageFreeDiskSpace()
    {
        return Node::sum('free_disk_space') / Node::count();
    }


    public function getNodeStatus(Node $node)
    {
        $result = $this->execOrFail($node, 'nodeStatus', [], "Can't get status from node");

        return $result['data'];
    }


    protected function execOrFail(Node $node, $function, $params = [], $message = null)
    {
        $result = $this->exec($node, $function, $params);
        if (!is_array($result)) {
            throw new \Exception($node->short_name . ': ' . $message);
        }

        if ($result['status'] != 'OK') {
            $message = array_key_exists('message', $result) ? $result['message'] : $message;
            throw new \Exception($node->short_name . ': ' . $message);
        }

        return $result;
    }


    protected function exec(Node $node, $function, $params = [], $message = null)
    {
        $client = new Client($node->url, 30, ['X-Auth: ' . $node->key]);

        return $client->execute($function, $params);
    }


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


    public function deleteOtrkeyFile(OtrkeyFile $file)
    {
        foreach ($file->nodes as $node) {
            $this->deleteFile($node, $file->name);
            $node->pivot->status = Node::STATUS_DELETED;
            $node->pivot->save();
        }
    }


    public function deleteFile(Node $node, $filename)
    {
        $result = $this->execOrFail($node, 'deleteFile', [$filename], "Can't delete file");

        return true;
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
            $file = OtrkeyFile::where('name', $data['filename'])->first();

            if (is_null($file)) {
                $file = $this->otrkeyFileService->createByFilename($data['filename']);
            }

            $file->size = $data['size'];
            $file->checksum = $data['md5sum'];
            $file->save();

            $files[$file->id] = ['status' => 'downloaded'];

            if (in_array($file->id, $keepFileIds)) {
                unset($keepFileIds[array_search($file->id, $keepFileIds)]);
            }
        }

        foreach ($keepFileIds as $id) {
            $files[] = $id;
        }

        $node->otrkeyFiles()->sync($files);

        return count($result['data']);
    }


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


    public function generateDownloadToken($filename, $downloadType, Carbon $time = null)
    {
        $time = $time ?: Carbon::now();
        $key = $downloadType . $time->format('YmdH') . $filename . Config::get('app.key');

        return substr(md5($key), 2, 10);
    }


    public function abortDownload(Node $node, $downloadId)
    {

        $downloads = $this->getRunningDownloads($node);

        foreach ($downloads as $download) {
            if ($download['id'] == $downloadId) break;
        }

        $this->execOrFail($node, 'breakAddFile', [$download['url']], "Can't abort the download");

        return true;
    }


    public function getRunningDownloads(Node $node)
    {
        $result = $this->execOrFail($node, 'listDownloads', [], "Can't get downloads from node");

        return $result['data'];
    }


    public function clean(Node $node)
    {
        $result = $this->execOrFail($node, 'clean', [], "Can't exec clean on node");

        return $result['data'];
    }


    public function rebalance()
    {
        $nodes = Node::all();

        $files = OtrkeyFile::with('nodes')
            ->join('node_otrkeyfile', 'id', '=', 'otrkeyfile_id')
            ->join('stations', 'otrkey_files.station','=','stations.otrkeyfile_name')
            ->availableInHq()
            ->notOlderThen(Carbon::now()->subDays(config('keep_files_on_all_nodes_days', 4)))
            ->where('node_otrkeyfile.status','=',Node::STATUS_DOWNLOADED)
            ->where('stations.language_short','=','de')
            ->groupBy('otrkey_files.id')
            ->having(DB::raw('count(*)'), '<', $nodes->count())
            ->limit(50)
            ->get(['otrkey_files.*']);

        foreach ($files as $file) {
            try {
                $url = $this->generateDownloadLink($file->availableFiles->first(), $file->name, DownloadService::PREMIUM);
                $missingNodes = $nodes->diff($file->nodes);
                foreach ($missingNodes as $node) {
                    if(!config('app.debug')) {
                        $this->fetchFile($node, $url);
                    }
                    Log::debug("[rebalance] Download to {$node->short_name} : {$url}");
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }

        // copy random files to a node with a lot of free space
        // usually a new node without much files
        // this prevent to have much files from the same period on one node
        $node = $nodes->sortByDesc('free_disk_space')->first();
        if ($node->free_disk_space > 150 * pow(1024, 3)) {
            $files = TvProgramsView::with('node')
                ->where('start', '<', Carbon::now()->subDays(config('keep_files_on_all_nodes_days', 4)))
                ->where('node_id', '!=', $node->id)
                ->orderBy(DB::raw('rand()'))
                ->limit(50)
                ->get();
            foreach ($files as $file) {
                $url = $this->generateDownloadLink($file->node, $file->name, DownloadService::PREMIUM);
                if(!config('app.debug')) {
                    $this->fetchFile($node, $url);
                }
                Log::debug("[rebalance] Filling free node {$node->short_name} : {$url}");
            }
        }

        // delete files just in the night
        if (date('G') >= 1 && date('G') <= 6) {
            $files = OtrkeyFile::rightJoin('node_otrkeyfile', 'id', '=', 'otrkeyfile_id')
                ->where('node_otrkeyfile.status', '=', Node::STATUS_DOWNLOADED)
                ->olderThen(Carbon::now()->subDays(config('keep_files_on_all_nodes_days', 4)))
                ->groupBy('otrkey_files.id')
                ->having(DB::raw('count(*)'), '>', 1)
                ->limit(100)
                ->get(['otrkey_files.*']);

            foreach ($files as $file) {
                $toDelete = $file->nodes
                    ->sortBy('free_disk_space')
                    ->take($file->nodes->count() - 1);

                try {
                    foreach ($toDelete as $node) {
                        if(!config('app.debug')) {

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
    }


    public function generateDownloadLink(Node $node, $filename, $userStatus = DownloadService::GUEST)
    {
        $result = $this->execOrFail($node, 'downloadLink', [$filename, $userStatus], "Can't get download link from node");

        return $result['url'];
    }


    public function fetchFile(Node $node, $url)
    {
        return $this->execOrFail($node, 'addFile', [$url], "Download failed");
    }
}