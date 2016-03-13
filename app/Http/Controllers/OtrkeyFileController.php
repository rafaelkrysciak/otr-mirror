<?php namespace App\Http\Controllers;

use App\AwsOtrkeyFile;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\TvProgramsView;
use Illuminate\Http\Request;

class OtrkeyFileController extends Controller {


    public function plainList()
    {
        $awsFiles = AwsOtrkeyFile::lists('otrkeyfile_id')->toArray();

        $files = TvProgramsView::with('node')
            ->orderBy('start', 'desc')
            ->orderBy('name')
            ->get(['otrkeyfile_id','name','node_id']);

        $fileList = [];
        foreach($files as $file) {
            if(in_array($file->otrkeyfile_id, $awsFiles)) {
                $status = 0;
            } else {
                $status = floor($file->node->busy_workers/180);
                $status = $status > 3 ? 3 : $status;
            }

            if(!array_key_exists($file->name, $fileList) || $fileList[$file->name] > $status) {
                $fileList[$file->name] = $status;
            }
        }

        $content = "";
        foreach($fileList as $filename => $status) {
            $content .= $filename.' '.$status."\n";
        }

        return response($content, 200)
            ->header('Content-Type', 'text/plain');
	}

}
