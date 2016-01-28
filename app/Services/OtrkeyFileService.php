<?php namespace App\Services;


use App\OtrkeyFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OtrkeyFileService {

    /**
     * @var TvProgramService
     */
    protected $tvProgramService;

    function __construct(TvProgramService $tvProgramService)
    {
        $this->tvProgramService = $tvProgramService;
    }


    /**
     * crate a OtrkeyFile based on the filename
     *
     * @param string $filename
     * @return OtrkeyFile
     */
    public function createByFilename($filename)
    {
        $data = $this->parseFilename($filename);
        return new OtrkeyFile($data);
    }


    /**
     * parse additional information from the otrkey file name
     *
     * @param $filename
     * @return array
     */
    public function parseFilename($filename)
    {
        $parts = [];
        if (preg_match('/(.*)_(\d\d)\.(\d\d)\.(\d\d)_(\d\d)-(\d\d)_(.*)_(\d{1,4})_TVOON_(..)\.(.*)\.otrkey/', $filename, $matches)) {
            $parts = [
                'name' => $filename,
                'title' => str_replace('_', ' ', $matches[1]),
                'start' => Carbon::create($matches[2] + 2000, $matches[3], $matches[4], $matches[5], $matches[6], 0),
                'station' => str_replace('_', ' ', $matches[7]),
                'duration' => $matches[8],
                'language' => $matches[9],
                'quality' => $matches[10],
            ];
        }

        if (preg_match('/S(\d{2,})E(\d{2,})/', $filename, $matches)) {
            $parts['season'] = (int) $matches[1];
            $parts['episode'] = (int) $matches[2];
        }

        return $parts;
    }


    /**
     * match OtrkeyFiles to the TvPrograms
     */
    public function matchTvPrograms()
    {
        $rows = DB::table('otrkey_files')
            ->leftJoin('stations', 'otrkey_files.station', '=', 'stations.otrkeyfile_name')
            ->leftJoin('tv_programs', 'otrkey_files.start', '=', 'tv_programs.start')
            ->whereRaw('tv_programs.station = stations.tvprogram_name')
            ->whereNull('otrkey_files.tv_program_id')
            ->select('otrkey_files.id as otrkey_file_id', 'tv_programs.id as tv_program_id')
            ->get();

        foreach($rows as $row) {
            OtrkeyFile::where('id','=', $row->otrkey_file_id)->update(['tv_program_id' => $row->tv_program_id]);
        }

        $rows = OtrkeyFile::whereNull('tv_program_id')->limit(5000)->get();
        $createdTvProgramms = [];
        foreach($rows as $row) {
            try {
                $key = $row->start->format('YmdHis').$row->station;
                if(!array_key_exists($key, $createdTvProgramms)) {
                    $tvProgram = $this->tvProgramService->createFromOtrkeyFile($row);
                    $createdTvProgramms[$key] = $tvProgram;
                } else {
                    $tvProgram = $createdTvProgramms[$key];
                }
                $row->tv_program_id = $tvProgram->id;
                $row->save();

            } catch(\Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }

}