<?php namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Omnipay\PayPal\Message\Response;

class PaypalTransaction extends Model {

    protected $fillable = [
        'email',
        'payerid',
        'firstname,',
        'lastname',
        'countrycode',
        'payerstatus',
        'transactionid',
        'transactiontype',
        'paymenttype',
        'ordertime',
        'amt',
        'feeamt',
        'currencycode',
        'paymentstatus',
        'pendingreason',
    ];

    protected $dates = ['ordertime'];

    public static function createFromDoExpressCheckoutPaymentResponse($data)
    {
        if($data instanceof Response) {
            $data = $data->getData();
        }

        $transaction = new static;
        $transaction->transactionid = $data['PAYMENTINFO_0_TRANSACTIONID'];
        $transaction->transactiontype = $data['PAYMENTINFO_0_TRANSACTIONTYPE'];
        $transaction->paymenttype = $data['PAYMENTINFO_0_PAYMENTTYPE'];
        $transaction->ordertime = Carbon::parse($data['PAYMENTINFO_0_ORDERTIME']);
        $transaction->amt = $data['PAYMENTINFO_0_AMT'];
        $transaction->feeamt = $data['PAYMENTINFO_0_FEEAMT'];
        $transaction->currencycode = $data['PAYMENTINFO_0_CURRENCYCODE'];
        $transaction->paymentstatus = $data['PAYMENTINFO_0_PAYMENTSTATUS'];
        $transaction->pendingreason = $data['PAYMENTINFO_0_PENDINGREASON'];

        return $transaction;
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function product()
    {
        return $this->belongsTo('App\PaymentProduct', 'payment_product_id');
    }
}
