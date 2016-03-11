<?php namespace App\Services;


use App\FilmMapper;
use App\TvProgram;
use Carbon\Carbon;
use \DB;

class FilmMapperService
{

    public function unmap(FilmMapper $filmMapper, $updateExisting = false)
    {
        $query = TvProgram::where('film_mapper_id', '=', $filmMapper->id);

        if (!$updateExisting) {
            $query->where('start', '>', Carbon::now());
        }

        $count = $query->update([
            'film_id'          => DB::raw('null'),
            'proposed_film_id' => DB::raw('null'),
            'film_mapper_id'   => 0,
            'title'            => DB::raw('org_title')
        ]);

        return $count;
    }


    public function map(FilmMapper $filmMapper, $updateExisting = false)
    {
        $query = TvProgram::where('org_title', '=', $filmMapper->org_title)
            ->where('language', '=', $filmMapper->language);

        if ($updateExisting) {
            $this->unmap($filmMapper, $updateExisting);
        } else {
            $query->whereNull('film_id');
        }

        if ($filmMapper->min_length > 0) {
            $query->where('length', '>=', $filmMapper->min_length);
        }

        if ($filmMapper->max_length > 0) {
            $query->where('length', '<=', $filmMapper->max_length);
        }

        if ($filmMapper->year > 1900) {
            $query->where('year', '=', $filmMapper->year);
        }

        if (!empty($filmMapper->director)) {
            $query->where('director', '=', $filmMapper->director);
        }

        if ($filmMapper->verified) {
            $query->update([
                'title'            => $filmMapper->new_title,
                'film_id'          => $filmMapper->film_id,
                'proposed_film_id' => 0,
                'film_mapper_id'   => $filmMapper->id,
            ]);
        } else {
            $query->update([
                'proposed_film_id' => $filmMapper->film_id,
                'film_mapper_id'   => $filmMapper->id,
            ]);
        }
    }


    public function mapAll($updateExisting = false)
    {
        $this->updateOrgTitle();
        $this->syncYearAndDirectorWithEpgPrograms();

        $query = "UPDATE tv_programs
                        INNER JOIN film_mappers ON (
                            tv_programs.title = film_mappers.org_title
                            AND (tv_programs.length > film_mappers.min_length OR film_mappers.min_length = 0)
                            AND (tv_programs.length < film_mappers.max_length OR film_mappers.max_length = 0)
                            AND (tv_programs.year = film_mappers.year OR film_mappers.year = 0)
                            AND (tv_programs.director = film_mappers.director OR film_mappers.director = '')
                            AND (tv_programs.language = film_mappers.language)
                        )
                    SET
                        tv_programs.title = film_mappers.new_title,
                        tv_programs.film_id = film_mappers.film_id,
                        tv_programs.film_mapper_id = film_mappers.id
                    WHERE
                      film_mappers.verified = 1";

        if (!$updateExisting) {
            $query .= " AND tv_programs.film_id IS NULL";
        }

        $affected = DB::affectingStatement($query);

        $query = "UPDATE tv_programs
                        INNER JOIN film_mappers ON (
                            tv_programs.title = film_mappers.org_title
                            AND (tv_programs.length > film_mappers.min_length OR film_mappers.min_length = 0)
                            AND (tv_programs.length < film_mappers.max_length OR film_mappers.max_length = 0)
                            AND (tv_programs.year = film_mappers.year OR film_mappers.year = 0)
                            AND (tv_programs.director = film_mappers.director OR film_mappers.director = '')
                            AND (tv_programs.language = film_mappers.language)
                        )
                    SET
                        tv_programs.proposed_film_id = film_mappers.film_id,
                        tv_programs.film_mapper_id = film_mappers.id
                    WHERE
                      film_mappers.verified = 0";

        if (!$updateExisting) {
            $query .= " AND tv_programs.film_id IS NULL";
        }

        $affected += DB::affectingStatement($query);

        return $affected;
    }


    public function updateOrgTitle()
    {
        TvProgram::whereNull('org_title')
            ->orWhere('org_title', '=', '')
            ->update([
                'org_title' => \DB::raw('title')
            ]);
    }


    /**
     * ToDo: Wrong place for this method
     * @return int
     */
    protected function syncYearAndDirectorWithEpgPrograms()
    {
        $count = 0;
        $count += DB::affectingStatement("update tv_programs
                join epg_programs on epg_programs.tv_program_id = tv_programs.id
            set tv_programs.year = epg_programs.date
            where epg_programs.date > 0 and tv_programs.year < 1");

        $count += DB::affectingStatement("update tv_programs
                join epg_programs on epg_programs.tv_program_id = tv_programs.id
            set tv_programs.director = epg_programs.directors
            where epg_programs.directors != '' and (tv_programs.director is null or tv_programs.director = '')");

        return $count;
    }


    public function cleanMapperRules()
    {
        $doubleTitles = FilmMapper::groupBy('org_title')
            ->havingRaw('count(*) > 1')
            ->lists('org_title');


        foreach ($doubleTitles as $doubleTitle) {
            $mappers = FilmMapper::where('org_title', '=', $doubleTitle)
                ->orderBy('org_title', 'asc')
                ->orderBy('year', 'desc')
                ->orderBy('min_length', 'asc')
                ->orderBy('max_length', 'asc')
                ->get();

            $min = null;
            $max = null;
            $year = null;
            $channel = null;
            foreach ($mappers as $mapper) {

                $mapper->max_length = $mapper->max_length == 0 ? 9999 : $mapper->max_length;

                if (is_null($year)) {
                    $year = $mapper->year;
                    $channel = $mapper->channel;
                    $min = $mapper->min_length;
                    $max = $mapper->max_length;
                    continue;
                }

                if (($mapper->year != 0 && $year != $mapper->year) || (!empty($mapper->channel) && $channel != $mapper->channel)) {
                    $year = $mapper->year;
                    $channel = $mapper->channel;
                    $min = $mapper->min_length;
                    $max = $mapper->max_length;
                    continue;
                }


                if ($min <= $mapper->min_length && $max >= $mapper->max_length) {
                    $mapper->delete();
                    continue;
                }

                $year = $mapper->year;
                $channel = $mapper->channel;
                $min = $mapper->min_length < $min ? $mapper->min_length : $min;
                $max = $mapper->max_length > $max ? $mapper->max_length : $max;
            }
        }
    }


    public function cleanUpDoubles()
    {
        $mappers = FilmMapper::groupBy('org_title', 'language')
            ->havingRaw('count(*) > 1')
            ->get(['org_title', 'language']);
        foreach ($mappers as $mapper) {
            $this->cleanUpDouble($mapper->org_title, $mapper->language);
        }
    }


    protected function cleanUpDouble($orgTitle, $language)
    {
        $mappers = FilmMapper::where('org_title', '=', $orgTitle)
            ->where('language', '=', $language)
            ->orderBy('year')
            ->orderBy('director')
            ->orderBy('max_length')
            ->orderBy('min_length')
            ->orderBy('channel')
            ->get();

        $current = null;
        foreach ($mappers as $mapper) {
            if (!is_null($current)
                && $current->max_length == $mapper->max_length
                && $current->min_length == $mapper->min_length
                && $current->year == $mapper->year
                && $current->director == $mapper->director
                && $current->channel == $mapper->channel) {

                $mapper->delete();

            } else{
                $current = $mapper;
            }
        }

    }
}