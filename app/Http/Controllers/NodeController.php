<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Node;
use App\OtrkeyFile;
use App\Services\NodeService;
use App\Services\StatService;
use App\TvProgramsView;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use \Log;


class NodeController extends Controller
{

    function __construct()
    {
        $this->middleware('admin');
    }


    /**
     * Get the download running on the nodes
     *
     * @return \Illuminate\View\View
     */
    public function nodesDownloads(NodeService $nodeService)
    {
        set_time_limit(600);

        $nodes = Node::get();

        $downloads = [];
        $errors = [];
        foreach ($nodes as $node) {
            try {
                $runningDownloads = $nodeService->getRunningDownloads($node);

                foreach ($runningDownloads as $row) {

                    $starttime = Carbon::createFromTimestamp($row['starttime']);
                    $endtime = $row['endtime'] ? Carbon::createFromTimestamp($row['endtime']) : null;
                    $lastupdate = $row['lastupdate'] ? Carbon::createFromTimestamp($row['lastupdate']) : Carbon::now();

                    $additionlaData = [
                        'node'       => $node,
                        'progress'   => number_format(($row['downloaded'] / $row['size']) * 100, 0, ',', '.'),
                        'starttime'  => $starttime,
                        'endtime'    => $endtime,
                        'lastupdate' => $lastupdate,
                        'duration'   => $starttime->diffForHumans($endtime ? $endtime : Carbon::now(), true)
                    ];

                    $downloads[$row['starttime'] . $node->id] = array_merge($row, $additionlaData);
                }
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        krsort($downloads);

        return view('system.downloads', compact('downloads'))
            ->withErrors($errors);
    }


    /**
     * Send download abort request to a node
     *
     * @param $nodeId
     * @param $downloadId
     *
     * @return array
     */
    public function abortDownload($nodeId, $downloadId, NodeService $nodeService)
    {
        $node = Node::findOrFail($nodeId);
        try {
            $nodeService->abortDownload($node, $downloadId);
        } catch(\Exception $e) {
            Log::error($e);
            return ['status' => 'NOK', 'message' => $e->getMessage()];
        }

        return ['status' => 'OK'];
    }


    public function nodeStatusPartial($nodeid, NodeService $nodeService)
    {
        $node = Node::findOrFail($nodeid);

        try {
            $status = $nodeService->getNodeStatus($node);
            $node->free_disk_space = $status['freeDiskspace'];
            $node->busy_workers = $status['BusyWorkers'];
            $node->save();
        } catch (\Exception $e) {
            $errors[] = $node->short_name . ': ' . $e->getMessage();
        }

        return view('system.nodes_status_partial', compact('status', 'node'));

    }


    /**
     * Get the status of all nodes
     *
     * @param NodeService $nodeService
     *
     * @return \Illuminate\View\View
     */
    public function nodesStatus(NodeService $nodeService)
    {
        $nodes = Node::get();

        return view('system.nodes_status', compact('nodes'));
    }


    /**
     * Perform copy of a file from one node to another
     *
     * @param NodeService $nodeService
     *
     * @return $this
     * @throws \Exception
     */
    public function doCopyFile(NodeService $nodeService)
    {
        list($srcNodeId, $file) = explode(':', Input::get('file'));
        $nodeId = Input::get('node_id');

        if ($srcNodeId == $nodeId) {
            return redirect('node/add-file')
                ->withErrors(["Source and Destination can't be the same"], 'default');
        }

        $srcNode = Node::findOrFail($srcNodeId);
        $url = $nodeService->generateDownloadLink($srcNode, $file, 'premium');

        if ($nodeId == 'all') {
            $nodes = Node::where('id', '!=', $srcNodeId)->get();
        } elseif ($nodeId == 'auto') {
            $nodes = Node::where('id', '!=', $srcNodeId)
                ->orderBy('free_disk_space', 'desc')->limit(1)->get();
        } else {
            $nodes = Node::where('id', '!=', $srcNodeId)
                ->where('id', '=', $nodeId)->get();
        }

        $errors = [];
        $message = 'File added to ';
        foreach ($nodes as $node) {
            try {
                $nodeService->fetchFile($node, $url);
                $message .= $node->short_name . ', ';
            } catch (\Exception $e) {
                Log::error($e);
                $errors[] = $node->short_name . ': ' . $e->getMessage();
            }
        }

        flash($message);

        return redirect('node/copy-file')
            ->withErrors($errors, 'default');
    }


    public function getFiles()
    {
        $q = Input::get('q');
        $page = Input::get('page', 1);

        $tvPrograms = TvProgramsView::with('node')
            ->where('name', 'like', "%$q%")
            ->forPage($page, 30)->get();

        $files = [];
        foreach ($tvPrograms as $tvProgram) {
            $files[] = [
                'text' => $tvProgram->node->short_name . ': ' . $tvProgram->name,
                'id'   => $tvProgram->node_id . ':' . $tvProgram->name,
            ];
        }

        $count = TvProgramsView::where('name', 'like', "%$q%")->count();

        return [
            'incomplete_resulte' => $count > count($files),
            'total_count'        => $count,
            'items'              => $files
        ];
    }


    public function copyFile()
    {
        $rows = Node::all();
        $nodes = [];
        foreach ($rows as $row) {
            $nodes[$row->id] = $row->short_name . ' (Free: ' . byteToSize($row->free_disk_space) . ')';
        }
        $nodes += [
            'all'  => 'All nodes',
            'auto' => 'Auto',
        ];

        return view('node.copyfile', compact('nodes'));
    }


    public function addFile()
    {
        $rows = Node::all();
        $nodes = [];
        foreach ($rows as $row) {
            $nodes[$row->id] = $row->short_name . ' (Free: ' . byteToSize($row->free_disk_space) . ')';
        }
        $nodes += [
            'all'  => 'All nodes',
            'auto' => 'Auto',
        ];

        return view('node.addfile', compact('nodes'));
    }


    public function pushFile(NodeService $nodeService, Request $request)
    {
        $url = Input::get('url');
        $nodeId = Input::get('node_id');

        if ($nodeId == 'all') {
            $nodes = Node::all();
        } elseif ($nodeId == 'auto') {
            $nodes = Node::orderBy('free_disk_space', 'desc')->limit(1)->get();
        } else {
            $nodes = Node::where('id', '=', $nodeId)->get();
        }

        $errors = [];
        foreach ($nodes as $node) {
            try {
                $nodeService->fetchFile($node, $url);
            } catch (\Exception $e) {
                Log::error($e);
                $errors[] = $node->short_name . ': ' . $e->getMessage();
            }
        }

        if (count($errors) == 0) {
            flash('File added');
        }


        return redirect('node/add-file')
            ->withInput($request->input())
            ->withErrors($errors, 'default');
    }


    public function plannedDownloads()
    {
        $files = OtrkeyFile::with('distros')
            ->forDownload()
            ->orderBy('start')
            ->paginate(50);

        $totalSize = OtrkeyFile::forDownload()->sum('distro_size');

        return view('node.planned_downloads', compact('files', 'totalSize'));
    }


    public function deletePlan(StatService $statService)
    {
        $files = $statService->getFilesForDelete(100);

        return view('system.delete_plan', compact('files'));
    }

}
