<?php namespace App\Services;


use App\Distro;
use App\OtrkeyFile;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Vinelab\Http\Client as HttpClient;


class DistroService {

    protected $httpClient;
    protected $otrkeyFileService;

    public function __construct(HttpClient $httpClient, OtrkeyFileService $otrkeyFileService)
    {
        $this->httpClient = $httpClient;
        $this->otrkeyFileService = $otrkeyFileService;
    }


    /**
     * @param Distro $distro
     * @param $filename
     *
     * @return string
     */
    public function generateDownloadLink(Distro $distro, $filename)
    {
        if($distro->type == 'ftp') {
            return $this->buildUrl([
                'scheme' => "ftp",
                'host' => $distro->host,
                'port' => $distro->port,
                'user' => $distro->username,
                'pass' => $distro->password,
                'path' => $filename,
            ]);
        }
    }

    /**
     * Download the file index data from the distro server
     *
     * @param string $url URL od the index
     * @return array rows
     */
    public function getIndexData($url)
    {
        $response = $this->httpClient->get($url);
        $rows =  explode("\n", $response->content());

        Log::info('[Distro sync] ['.$url.'] Row count: '.count($rows));

        $fields = [
            'name',
            'distro_size',
            'mtime',
            'distro_checksum'
        ];

        $fileRows = [];
        foreach($rows as $row)
        {
            $fileData = str_getcsv(trim($row), ';');

            if(count($fileData) != 4) {
                Log::debug('[Distro sync] ['.$url.'] Data error: '.implode(',', $fileData));
                continue;
            }
            $fileData = array_combine($fields, array_slice($fileData, 0, 17));
            $fileRows[] = $fileData;
        }

        return $fileRows;
    }


    public function getListingData($url)
    {
        $response = $this->httpClient->get($url);
        $rows =  explode("\n", $response->content());

        $fileRows = [];
        foreach($rows as $row) {
            $matches = [];
            if(preg_match('/ (\d{3,}) (.* \d{2}:\d{2}) (.*otrkey)/', $row, $matches)) {
                $fileRows[] = [
                    'distro_size' => $matches[1],
                    'mtime' => $matches[2],
                    'name' => $matches[3]
                ];
            }
        }

        Log::info('[Distro sync] ['.$url.'] Row count: '.count($fileRows));

        return $fileRows;
    }

    /**
     * Import the File data to the Database
     *
     * @param Distro $distro
     * @return int imported rows
     */
    public function fillDatabase(Distro $distro)
    {
        $rows = collect($this->getIndexData($distro->index_url))->keyBy('name');

        if(!empty($distro->listing_url)) {
            $listRows = $this->getListingData($distro->listing_url);
            foreach($listRows as $row) {
                if(!$rows->offsetExists($row['name'])) {
                    Log::info('[Distro sync] ['.$distro->host.'] Added from listing: '.$row['name']);
                    $rows->push($row);
                }
            }
        }

        $count = 0;
        $otrkeyFileIds = [];
        $now = new Carbon();
        foreach($rows as $fileData) {
            $count++;
            $fileData += $this->otrkeyFileService->parseFilename($fileData['name']);
            $fileData['mtime'] = Carbon::parse($fileData['mtime']);

            try {
                $otrkeyFile = OtrkeyFile::updateOrCreate(['name' => $fileData['name']], $fileData);
                $otrkeyFileIds[] = $otrkeyFile->id;

                if($otrkeyFile->created_at->diffInMinutes($now) < 10) {
                    Log::info('[Distro sync] ['.$distro->host.'] New file: '.$fileData['name']);
                }
            } catch(QueryException $e) {
                Log::info($distro->host.': '.$e->getMessage());
            }
        }
        $distro->otrkeyFiles()->sync($otrkeyFileIds);

        Log::info('[Distro sync] ['.$distro->host.'] File count: '.$count);

        return $count;
    }


    protected function buildUrl($parts)
    {
        $scheme   = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';
        $host     = isset($parts['host']) ? $parts['host'] : '';
        $port     = isset($parts['port']) ? ':' . $parts['port'] : '';
        $user     = isset($parts['user']) ? $parts['user'] : '';
        $pass     = isset($parts['pass']) ? ':' . $parts['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parts['path']) ? $parts['path'] : '';
        $path     = $path[0] == '/' ? $path : '/'.$path;
        $query    = isset($parts['query']) ? '?' . $parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

}