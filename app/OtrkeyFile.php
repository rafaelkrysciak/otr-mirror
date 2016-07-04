<?php namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;

class OtrkeyFile extends Model {

    protected $fillable = [
        'name',
        'size',
        'checksum',
        'distro_size',
        'distro_checksum',
        'title',
        'start',
        'mtime',
        'station',
        'duration',
        'language',
        'quality',
        'season',
        'episode',
    ];

    protected $dates = ['start', 'mtime'];


    /**
     * get the base part of the filename
     *
     * @return string
     */
    public function getBaseName()
    {
        $matches = null;
        $returnValue = preg_match('/(.*TVOON[^\\.]*)/', $this->name, $matches);
        return $returnValue ? $matches[0] : '';
    }

    public function scopeNotDownloadedOnAnyNode($query)
    {
        $query->whereNotIn('otrkey_files.id', function ($query) {
            $query->select('otrkeyfile_id')
                ->from('node_otrkeyfile')
                ->whereIn('status', [Node::STATUS_DOWNLOADED]);

        });
    }

    public function scopeNotRequestedOnAnyNodeFor($minutes)
    {
        $query->whereNotIn('otrkey_files.id', function ($query) {
            $query->select('otrkeyfile_id')
                ->from('node_otrkeyfile')
                ->whereIn('status', [Node::STATUS_REQUESTED])
                ->where('updated_at', '>', Carbon::now()->addMinutes(-30));

        });
    }

    public function scopeOlderThen($query, Carbon $time)
    {
        $query->where('otrkey_files.start', '<', $time);
    }

    public function scopeFilename($query, $filename)
    {
        $query->where('otrkey_files.name', '=', $filename);
    }

    public function scopeForDownload($query)
    {
        $this->scopeAvailableInHq($query);
        $this->scopeAvailableOnDistro($query);
        $this->scopeNotOlderThen($query, Carbon::now()->subWeeks(config('hqm.download_files_not_older_then_days', 7)));

        // only files that were not downloaded yet
        // and they were requested more then 30 minutes ago
        $query->whereNotIn('otrkey_files.id', function ($query) {
            $query->select('otrkeyfile_id')
                ->from('node_otrkeyfile')
                ->whereIn('status', [Node::STATUS_DOWNLOADED, Node::STATUS_DELETED])
                ->orWhere(function ($query) {
                    $query->where('status', '=', Node::STATUS_REQUESTED)
                        ->where('updated_at', '>', Carbon::now()->subMinutes(config('retry_download_after_minutes', 30)));
                });
        });

        // Exclude Spanish stations
        $query->whereNotIn('otrkey_files.station', function ($query) {
            $query->select('otrkeyfile_name')
                ->from('stations')
                ->where('language_short','=','es');
        });
    }

    public function scopeAvailableInHq($query)
    {
        $query->whereIn('otrkey_files.tv_program_id', function ($query) {
            $query->select('tv_program_id')
                ->from('otrkey_files')
                ->whereIn('quality', ['mpg.HD.avi', 'mpg.HQ.avi']);
        });
    }

    public function scopeAvailableOnDistro($query)
    {
        $query->whereIn('otrkey_files.id', function ($query) {
            $query->select('otrkeyfile_id')->from('distro_otrkeyfile');
        });
    }

    public function scopeNotOlderThen($query, Carbon $time)
    {
        $query->where('otrkey_files.start', '>', $time);
    }

    /**
     * Get distros where the file is available
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function distros()
    {
        return $this->belongsToMany('App\Distro', 'distro_otrkeyfile', 'otrkeyfile_id', 'distro_id')
            ->withTimestamps();
    }

    public function isAvailable()
    {
        return $this->nodes()
            ->wherePivot('status','=',Node::STATUS_DOWNLOADED)
            ->count() > 0;
    }

    /**
     * Get nodes the files are on
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function nodes()
    {
        return $this->belongsToMany('App\Node', 'node_otrkeyfile', 'otrkeyfile_id', 'node_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function availableFiles()
    {
        return $this->nodes()->wherePivot('status', Node::STATUS_DOWNLOADED);
    }

    public function requestedFiles()
    {
        return $this->nodes()->wherePivot('status', Node::STATUS_REQUESTED);
    }

    public function deletedFiles()
    {
        return $this->nodes()->wherePivot('status', Node::STATUS_DELETED);
    }

    public function isUsersFavorite(User $user)
    {
        return $this->users()
            ->wherePivot('type','=',User::FAVORITE)
            ->wherePivot('user_id','=',$user->id)
            ->count() > 0;
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'otrkeyfile_user', 'otrkeyfile_id', 'user_id')
            ->withPivot('type')
            ->withTimestamps();
    }

    public function isUsersWatched(User $user)
    {
        return $this->users()
            ->wherePivot('type','=',User::WATCHED)
            ->wherePivot('user_id','=',$user->id)
            ->count() > 0;
    }

    public function isUsersDownload(User $user)
    {
        return $this->users()
            ->wherePivot('type','=',User::DOWNLOADED)
            ->wherePivot('user_id','=',$user->id)
            ->count() > 0;
    }


    public function tvProgram()
    {
        return $this->belongsTo('App\TvProgram');
    }

    public function awsOtrkeyFile()
    {
        return $this->hasOne('App\AwsOtrkeyFile', 'otrkeyfile_id', 'id');
    }

}
