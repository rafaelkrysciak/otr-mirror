<?php namespace App\Services;

use App\EpgProgram;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Vinelab\Http\Client as HttpClient;

class XmltvService
{

    protected $httpClient;
    protected $url = 'http://s01.hq-mirror.de/guide.xml';


    function __construct(HttpClient $client, $url = null)
    {
        $this->httpClient = $client;
        if (!is_null($url)) {
            $this->url = $url;
        }
    }


    protected function loadGuidXmlFile()
    {
        $response = $this->httpClient->get($this->url);

        return $response->content();
    }


    protected function parseXmltv($xmlStr)
    {
        $tv = new \SimpleXMLElement($xmlStr);

        foreach ($tv->programme as $programme) {
            $data = [];

            $data['channel'] = $programme['channel'];
            $data['start'] = Carbon::parse($programme['start']);
            $data['stop'] = Carbon::parse($programme['stop']);
            $data['desc'] = (string)$programme->desc;
            $data['date'] = (string)$programme->date;

            foreach ($programme->title as $title) {
                if ($title['lang'] == 'de') {
                    $data['title_de'] = (string)$title;
                }
                if ($title['lang'] == 'xx') {
                    $data['title_xx'] = (string)$title;
                }
            }

            if ($programme->{'sub-title'}) {
                $data['sub_title'] = (string)$programme->{'sub-title'};
            }

            $data['directors'] = [];
            if ($programme->credits->director) {
                foreach ($programme->credits->director as $director) {
                    $data['directors'][] = (string)$director;
                }
            }

            if ($programme->credits->actor) {
                foreach ($programme->credits->actor as $actor) {
                    $data['actors'][] = (string)$actor;
                }
            }
            if ($programme->category) {
                foreach ($programme->category as $category) {
                    $categories = explode(',', (string)$category);
                    foreach ($categories as $name) {
                        $name = trim($name, ', ');
                        $data['category'][] = $name;
                    }
                }
                $data['category'] = array_unique($data['category']);

                $data += $this->parseEpisode($programme->{'episode-num'});
            }

            try {
                EpgProgram::create($data);
            } catch (QueryException $e) {
                // doubles
                if ($e->getCode() != 23000) {
                    \Log::error($e);
                }
            }

        }

    }


    public function load()
    {
        $xmlStr = $this->loadGuidXmlFile();
        $this->parseXmltv($xmlStr);
    }


    protected function parseEpisode($value)
    {
        if (strpos($value, '/') !== false) {
            list($seasonEpisode, $episodes_total) = explode('/', $value);
        } else {
            $seasonEpisode = $value;
            $episodes_total = 0;
        }

        if (strpos($seasonEpisode, '.') !== false) {
            list($season, $episode) = explode('.', $seasonEpisode);
            $season = $season == '' ? 0 : $season + 1;
            $episode = $episode == '' ? 0 : $episode + 1;
        } else {
            $season = $episode = 0;
        }


        return [
            'episode_total' => (int)$episodes_total,
            'episode'       => (int)$episode,
            'season'        => (int)$season,
        ];
    }


    public function matchTvPrograms()
    {
        $affectedRows = 0;

        // Exact time matcht
        $query = "update epg_programs
	                join tv_programs on epg_programs.channel = tv_programs.station
	                  and epg_programs.`start` = tv_programs.`start`
                  set epg_programs.tv_program_id = tv_programs.id
                  where epg_programs.tv_program_id = 0";
        $affectedRows += \DB::affectingStatement($query);

        $affectedRows += $this->matchTvProgramsWithTimeshift('-', 5);
        $affectedRows += $this->matchTvProgramsWithTimeshift('+', 5);
        $affectedRows += $this->matchTvProgramsWithTimeshift('-', 10);
        $affectedRows += $this->matchTvProgramsWithTimeshift('+', 10);
        $affectedRows += $this->matchTvProgramsWithTimeshift('-', 15);
        $affectedRows += $this->matchTvProgramsWithTimeshift('+', 15);

        return $affectedRows;
    }


    protected function matchTvProgramsWithTimeshift($operator, $minutes)
    {
        $sql = "select epg_programs.id as epg_program_id, tv_programs.id as tv_program_id
            from epg_programs
                join tv_programs on epg_programs.channel = tv_programs.station and epg_programs.`start` = tv_programs.`start` $operator interval $minutes minute
            where epg_programs.tv_program_id = 0
                and tv_programs.id not in (select tv_program_id from epg_programs)";
        $data = \DB::select($sql);

        $count = 0;
        foreach ($data as $row) {
            $count += \DB::table('epg_programs')
                ->where('id', '=', $row->epg_program_id)
                ->update(['tv_program_id' => $row->tv_program_id]);
        }

        return $count;

    }
}