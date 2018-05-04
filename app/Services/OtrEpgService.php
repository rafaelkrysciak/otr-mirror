<?php namespace App\Services;


use App\Station;
use Carbon\Carbon;
use App\TvProgram;
use Illuminate\Database\QueryException;
use \DB;
use \Log;
use Vinelab\Http\Client as HttpClient;

class OtrEpgService
{

    protected $httpClient;


    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }


    public function loadDays($days, $start = 0)
    {
        $count = 0;
        for ($day = $start; $day < $start+$days; $day++) {
            $date = Carbon::now()->addDays($day);
            try {
                $count += $this->fillDatabase($date);
            } catch (\Exception $e) {
                Log::error($e);
            }
        }

        Log::info("$count EPG records read");

        return $count;
    }


    /**
     * Download the EPG data from the onlinetvrecorder site
     *
     * @param Carbon $date
     *
     * @return array rows
     */
    public function downloadEpgData(Carbon $date)
    {
        $url = 'http://www.onlinetvrecorder.com/epg/csv/epg_' . $date->format('Y_m_d') . '.csv';
        $response = $this->httpClient->get($url);

        return explode("\n", $response->content());
    }


    /**
     * Import the EPG data to the Database
     *
     * @param Carbon $date
     *
     * @return int imported rows
     */
    public function fillDatabase(Carbon $date)
    {
        $rows = $this->downloadEpgData($date);

        $fields = [
            'otr_epg_id',
            'start',
            'end',
            'length',
            'station',
            'title',
            'type',
            'description',
            'genre_id',
            'fsk',
            'language',
            'weekday',
            'addition',
            'rerun',
            'downloadlink',
            'infolink',
            'programlink',
        ];

        $languages = Station::groupBy('tvprogram_name')->lists('language_short', 'tvprogram_name')->toArray();

        $count = 0;
        foreach ($rows as $row) {
            $csvData = str_getcsv(trim($row), ';');
            if (!is_numeric($csvData[0]) || count($csvData) < 17) {
                Log::debug('EPG data error: ' . implode(',', $csvData));
                continue;
            }
            $count++;
            $epgData = array_combine($fields, array_slice($csvData, 0, 17));

            if (strpos($epgData['start'], '%') !== false) {
                array_walk($epgData, function (&$value, $key) {
                    $value = urldecode($value);
                });
            }

            if (empty($epgData['language']) && array_key_exists($epgData['station'], $languages)) {
                $epgData['language'] = $languages[$epgData['station']];
            }

            $epgData['start'] = Carbon::parse($epgData['start']);
            $epgData['end'] = Carbon::parse($epgData['end']);

            $epgData['org_title'] = $epgData['title'];

            try {

                $tvProgramExists = TvProgram::where([
                    'start'   => DB::raw("CAST('" . $epgData['start']->format('Y-m-d H:i:s') . "' as DATETIME)"),
                    'station' => $epgData['station']
                ])->exists();
                if (!$tvProgramExists) {
                    TvProgram::create($epgData);
                }
//                TvProgram::updateOrCreate([
//                    'start' => DB::raw("CAST('".$epgData['start']->format('Y-m-d H:i:s')."' as DATETIME)"),
//                    'station' => $epgData['station']
//                ], $epgData);
//                try {
//                    TvProgram::create($epgData);
//                } catch(QueryException $e) {
//                    if($e->getCode() == "23000") {
//                        TvProgram::where(['start' => $epgData['start'], 'station' => $epgData['station']])->update($epgData);
//                    } else {
//                        throw $e;
//                    }
//                }
            } catch (QueryException $e) {
                Log::info($e);
            }
        }

        return $count;
    }


    public function consolidateTvPrograms()
    {
        // update language from stations
        $this->consolidateLanguage();

        // update season and episode from otrkeyfiles
        $this->consolidateSeasonEpisodeFromOtrkeyFiles();

        // update season and episode from xmltv data
        $this->consolidateSeasonEpisodeFromXmlTvData();

        // update director and year from xmltv data
        $this->consolidateYearDirectorFromXmlTvData();
    }


    protected function consolidateLanguage()
    {
		$count = 0;
        $sql = "update `tv_programs`
                inner join `stations` on `tv_programs`.`station` = `stations`.`tvprogram_name`
            set
                `tv_programs`.`language` = stations.language_short,
                `tv_programs`.`updated_at` = current_timestamp
            where
                `tv_programs`.`language` != stations.language_short";
		try {
			$count += DB::affectingStatement($sql);
		} catch(QueryException $e) {
			\Log::error($e);
		}

        return $count;
    }

    protected function consolidateSeasonEpisodeFromOtrkeyFiles()
    {
        $count = 0;
        $sql = "update `tv_programs`
                inner join `otrkey_files` on `tv_programs`.`id` = `otrkey_files`.`tv_program_id`
            set
                `tv_programs`.`season` = `otrkey_files`.`season`,
                `tv_programs`.`episode` = `otrkey_files`.`episode`,
                `tv_programs`.`updated_at` = current_timestamp
            where
                `tv_programs`.`season` IS NULL
                and `tv_programs`.`episode`  IS NULL
                and `otrkey_files`.`season` > 0
                and `otrkey_files`.`episode` > 0";
		try {
			$count += DB::affectingStatement($sql);
		} catch(QueryException $e) {
			\Log::error($e);
		}

        $sql = "update `tv_programs`
                inner join `otrkey_files` on `tv_programs`.`id` = `otrkey_files`.`tv_program_id`
            set
                `tv_programs`.`episode` = `otrkey_files`.`episode`,
                `tv_programs`.`updated_at` = current_timestamp
            where
                `tv_programs`.`episode`  IS NULL
                and `otrkey_files`.`episode` > 0";
		try {
			$count += DB::affectingStatement($sql);
		} catch(QueryException $e) {
			\Log::error($e);
		}

        return $count;
    }

    protected function consolidateSeasonEpisodeFromXmlTvData()
    {
        $count = 0;
        $sql = "update `tv_programs`
                inner join `epg_programs` on `tv_programs`.`id` = `epg_programs`.`tv_program_id`
            set
                `tv_programs`.`season` = `epg_programs`.`season`,
                `tv_programs`.`episode` = `epg_programs`.`episode`,
                `tv_programs`.`updated_at` = current_timestamp
            where
                `tv_programs`.`season` IS NULL
                and `tv_programs`.`episode`  IS NULL
                and `epg_programs`.`season` > 0
                and `epg_programs`.`episode` > 0";
        
		try {
			$count += DB::affectingStatement($sql);
		} catch(QueryException $e) {
			\Log::error($e);
		}
		
        $sql = "update `tv_programs`
                inner join `epg_programs` on `tv_programs`.`id` = `epg_programs`.`tv_program_id`
            set
                `tv_programs`.`episode` = `epg_programs`.`episode`,
                `tv_programs`.`updated_at` = current_timestamp
            where
                `tv_programs`.`episode`  IS NULL
                and `epg_programs`.`episode` > 0";
		try {
			$count += DB::affectingStatement($sql);
		} catch(QueryException $e) {
			\Log::error($e);
		}

        return $count;
    }

    protected function consolidateYearDirectorFromXmlTvData()
    {
        $count = 0;
		try {
			$count += DB::affectingStatement("update tv_programs
					join epg_programs on epg_programs.tv_program_id = tv_programs.id
				set tv_programs.year = epg_programs.date
				where epg_programs.date > 0 and tv_programs.year < 1");
		} catch(QueryException $e) {
			\Log::error($e);
		}
		
		try {
			$count += DB::affectingStatement("update tv_programs
					join epg_programs on epg_programs.tv_program_id = tv_programs.id
				set tv_programs.director = epg_programs.directors
				where epg_programs.directors != '' and (tv_programs.director is null or tv_programs.director = '')");
		} catch(QueryException $e) {
			\Log::error($e);
		}      

        return $count;
    }

}