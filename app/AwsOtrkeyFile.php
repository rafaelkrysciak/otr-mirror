<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class AwsOtrkeyFile extends Model {

    protected $fillable = ['otrkeyfile_id', 'last_modified', 'size', 'checksum'];
    protected $dates = ['last_modified'];


    public function otrkeyFile()
    {
        return $this->belongsTo('App\OtrkeyFile', 'otrkeyfile_id', 'id');
    }

}
