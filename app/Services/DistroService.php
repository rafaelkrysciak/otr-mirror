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
    public function downloadIndexData($url)
    {
        $response = $this->httpClient->get($url);
        return explode("\n", $response->content());
    }


    /**
     * Import the File data to the Database
     *
     * @param Carbon $date
     * @return int imported rows
     */
    public function fillDatabase(Distro $distro)
    {
        $rows = $this->downloadIndexData($distro->index_url);

        $fields = [
            'name',
            'distro_size',
            'mtime',
            'distro_checksum'
        ];

        $count = 0;
        $otrkeyFileIds = [];
        foreach($rows as $row) {
            $fileData = str_getcsv(trim($row), ';');
            if(count($fileData) != 4) {
                Log::debug('Distro data error: '.implode(',', $fileData));
                continue;
            }
            $count++;
            $fileData = array_combine($fields, array_slice($fileData, 0, 17));
            $fileData += $this->otrkeyFileService->parseFilename($fileData['name']);

            $fileData['mtime'] = Carbon::parse($fileData['mtime']);

            try {
                $otrkeyFile = OtrkeyFile::updateOrCreate(['name' => $fileData['name']], $fileData);
                $otrkeyFileIds[] = $otrkeyFile->id;
            } catch(QueryException $e) {
                Log::info($e);
            }
        }
        $distro->otrkeyFiles()->sync($otrkeyFileIds);
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