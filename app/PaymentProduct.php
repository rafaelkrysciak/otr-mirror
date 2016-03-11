<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentProduct extends Model {

    protected $fillable = [
        'name',
        'description',
        'price',
        'premium_months',
        'valid_until',
    ];

    protected $dates = ['valid_until'];

    public function paypalTransactions()
    {
        return $this->hasMany('App\PaypalTransactions');
    }

}
