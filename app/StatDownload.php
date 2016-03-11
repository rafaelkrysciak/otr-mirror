<?php namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class StatDownload extends Model {

    protected $fillable = ['event_date', 'otrkey_file_id', 'downloads', 'total_downloads', 'aws_downloads', 'aws_total_downloads'];
    protected $dates = ['event_date'];
    public $timestamps = false;


    public function otrkeyFile()
    {
        return $this->belongsTo('App\OtrkeyFile');
    }
}
