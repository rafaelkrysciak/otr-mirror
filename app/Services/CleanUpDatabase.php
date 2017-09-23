<?php  namespace App\Services; 

use \DB;

class CleanUpDatabase {

    /**
     * Delete old TvProgram Records
     *
     * @return int
     */
    public function cleanTvProgramTable()
    {
        // delete old tvprograms
        $sql = "DELETE
            FROM tv_programs
            WHERE
                start < DATE_SUB(CURRENT_DATE, INTERVAL ".config('hqm.database_cleanup_older_then', '2 WEEK').")
                AND id NOT IN (SELECT DISTINCT tv_program_id FROM otrkey_files WHERE tv_program_id > 0)";

        return  DB::delete($sql);
    }


    /**
     * Delete old otrkey_files records
     *
     * @return int
     */
    public function cleanOtrkeyFilesRecords()
    {
        // delete old otrkeyfiles
        $sql = "DELETE
            FROM otrkey_files
            WHERE
                start < DATE_SUB(CURDATE(),INTERVAL ".config('hqm.database_cleanup_older_then', '2 WEEK').") AND
                id NOT IN (SELECT otrkeyfile_id FROM node_otrkeyfile WHERE status = 'downloaded') AND
                id NOT IN (SELECT otrkeyfile_id FROM distro_otrkeyfile) AND
                id NOT IN (SELECT otrkeyfile_id FROM aws_otrkey_files)";

        return DB::delete($sql);
    }


    public function cleanOtrkeyFileNodeRelation()
    {
        $sql = "DELETE FROM node_otrkeyfile
            WHERE
              status IN ('deleted','requested')
              AND created_at < current_timestamp - interval 1 Month";

        return DB::delete($sql);
    }


	public function cleanEpgProgramTable()
	{
		$sql = "DELETE FROM epg_programs
            WHERE
              start < DATE_SUB(CURDATE(),INTERVAL 6 WEEK)";

		return DB::delete($sql);
	}

}