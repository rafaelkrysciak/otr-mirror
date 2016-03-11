<?php namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model {

    protected $fillable = ['event_date', 'language', 'station', 'quality', 'downloads', 'size',];
    public $timestamps = false;

    public function scopePeriod($query, $days = 7)
    {
        $query->where('event_date','>=', Carbon::now()->subDays($days));
    }

    public static function populateFromStatDownloads()
    {
        $date = Stat::orderBy('event_date','desc')->first();
        $date = is_null($date) ? '2015-01-01' : $date->event_date;

        Stat::where('event_date','>=',$date)->delete();

        $sql = "insert into stats (event_date, language, station, quality, downloads, size)
            select
                stat_downloads.event_date,
                stations.language_short as language,
                stations.tvprogram_name as station,
                otrkey_files.quality,
                sum(stat_downloads.downloads) as downloads,
                sum(stat_downloads.downloads * otrkey_files.size) as size
            from stat_downloads
                left join otrkey_files on stat_downloads.otrkey_file_id = otrkey_files.id
                left join stations on otrkey_files.station = stations.otrkeyfile_name
            where event_date >= ?
            group by 1,2,3,4
            order by 1,2,3,4";
        \DB::affectingStatement($sql, [$date]);
    }

}
