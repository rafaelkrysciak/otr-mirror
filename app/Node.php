<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Node extends Model {

    const STATUS_REQUESTED = 'requested';
    const STATUS_DOWNLOADED = 'downloaded';
    const STATUS_DELETED = 'deleted';


    /**
     * Get otrkey files available on this node
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function requested()
    {
        return $this->otrkeyFiles()->wherePivot('status', '=', self::STATUS_REQUESTED);
    }


	/**
     * Filter active nodes
     *
     * @param $query
     */
    public function scopeActive($query)
    {
        $query->where('status', '=', 'active');
    }


    public function scopeScrapped($query)
    {
        $query->where('status', '=', 'scrap');
    }


    /**
     * Get otrkey files available on this node
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function available()
    {
        return $this->otrkeyFiles()->wherePivot('status', '=', self::STATUS_DOWNLOADED);
    }


    /**
     * Get otrkey files related to the node
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function otrkeyFiles()
    {
        return $this->belongsToMany('App\OtrkeyFile', 'node_otrkeyfile', 'node_id', 'otrkeyfile_id')
            ->withPivot('status')
            ->withTimestamps();
    }

}
