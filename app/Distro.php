<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Distro extends Model {

	//


    /**
     * Get otrkey files available on this distro server
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function otrkeyFiles()
    {
        return $this->belongsToMany('App\OtrkeyFile', 'distro_otrkeyfile', 'distro_id', 'otrkeyfile_id')
            ->withTimestamps();
    }

}
