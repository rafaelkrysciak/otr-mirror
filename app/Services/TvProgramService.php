<?php namespace App\Services;

use App\OtrkeyFile;
use App\Station;
use App\TvProgram;
use Illuminate\Support\Facades\DB;

class TvProgramService {


    public function createView()
    {
        DB::statement("DROP TABLE IF EXISTS tv_programs_view_new");
        DB::statement("CREATE TABLE tv_programs_view_new
                (
                    INDEX (node_id),
                    INDEX (film_id),
                    INDEX (otrkeyfile_id),
                    INDEX (tv_program_id),
                    INDEX (start),
                    FULLTEXT(title,description,name),
                    FULLTEXT(title),
                    FULLTEXT(description),
                    FULLTEXT(name)
                ) ENGINE=MyISAM
            SELECT
                films.id as film_id,
                films.year,
                films.country,
                films.genre,
                films.amazon_image,
                films.fsk,
                films.imdb_rating,
                films.imdb_votes,
                films.tvseries,
                node_otrkeyfile.node_id,
                node_otrkeyfile.otrkeyfile_id,
                otrkey_files.tv_program_id,
                tv_programs.otr_epg_id,
                tv_programs.title,
                tv_programs.start,
                tv_programs.length,
                tv_programs.description,
                tv_programs.station,
                stations.language,
                otrkey_files.name,
                otrkey_files.episode,
                otrkey_files.season,
                otrkey_files.size,
                otrkey_files.quality
            FROM node_otrkeyfile
                LEFT JOIN otrkey_files ON node_otrkeyfile.otrkeyfile_id = otrkey_files.id
                LEFT JOIN tv_programs ON otrkey_files.tv_program_id = tv_programs.id
                LEFT JOIN stations ON tv_programs.station = stations.tvprogram_name
                LEFT JOIN films ON tv_programs.film_id = films.id
            WHERE
                node_otrkeyfile.status = 'downloaded'
                AND tv_programs.id IS NOT NULL");
        DB::statement("DROP TABLE IF EXISTS tv_programs_view_old");
        DB::statement("CREATE TABLE IF NOT EXISTS tv_programs_view (id INT(10))");
        DB::statement("RENAME TABLE tv_programs_view TO tv_programs_view_old, tv_programs_view_new TO tv_programs_view");
    }


    public function createFilmView()
    {

        DB::statement("DROP TABLE IF EXISTS film_view_new");
        DB::statement("
            CREATE TABLE film_view_new
                (
                    INDEX (year),
                    INDEX (country),
                    INDEX (genre),
                    INDEX (actor(800)),
                    INDEX (director),
                    INDEX (fsk),
                    INDEX (imdb_rating),
                    INDEX (imdb_votes),
                    INDEX (start),
                    INDEX (languages(25)),
                    INDEX (qualities(50)),
                    INDEX (downloads),
                    FULLTEXT(actor,director,title,country,genre,original_title)
                ) ENGINE=MyISAM
            SELECT
                films.id as film_id,
                films.title,
                films.original_title,
                films.year,
                films.country,
                films.genre,
                group_concat(distinct filmstars.star) as actor,
                films.director,
                films.amazon_image,
                films.fsk,
                films.imdb_rating,
                films.imdb_votes,
                films.tvseries,
                max(tv_programs.start) as start,
                group_concat(distinct tv_programs.station) as stations,
                group_concat(distinct stations.language) as languages,
                group_concat(distinct otrkey_files.quality) as qualities,
                sum(stat_downloads.downloads) as downloads
            FROM node_otrkeyfile
                LEFT JOIN otrkey_files ON node_otrkeyfile.otrkeyfile_id = otrkey_files.id
                LEFT JOIN stat_downloads ON stat_downloads.otrkey_file_id = otrkey_files.id AND stat_downloads.event_date > CURRENT_DATE - INTERVAL 2 WEEK
                LEFT JOIN tv_programs ON otrkey_files.tv_program_id = tv_programs.id
                LEFT JOIN stations ON tv_programs.station = stations.tvprogram_name
                LEFT JOIN films ON tv_programs.film_id = films.id
                LEFT JOIN filmstars ON films.id = filmstars.film_id
            WHERE
                node_otrkeyfile.status = 'downloaded'
                AND tv_programs.id IS NOT NULL
                AND films.id > 0
         	GROUP BY films.id");
        DB::statement("DROP TABLE IF EXISTS film_view_old");
        DB::statement("CREATE TABLE IF NOT EXISTS film_view (id INT(10))");
        DB::statement("RENAME TABLE film_view TO film_view_old, film_view_new TO film_view");
    }

    /**
     * @param OtrkeyFile $file
     * @return TvProgram
     * @throws \Exception
     */
    public function createFromOtrkeyFile(OtrkeyFile $file)
    {
        $station = Station::where('otrkeyfile_name','=',$file->station)->first();
        if(is_null($station)) {
            throw new \Exception('Unknown station '.$file->station.' from file '.$file->name);
        }

        $end = $file->start;
        $end->addMinutes($file->duration);

        $data = [
            'start' => $file->start,
            'end' => $end,
            'length' => $file->duration,
            'title' => $file->title,
            'station' => $station->tvprogram_name,
            'language' => $file->language,
            'weekday' => $file->start->format('D'),
        ];

        return TvProgram::create($data);
    }

}