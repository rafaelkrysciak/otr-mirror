<?php namespace App\Http\Controllers;

use App\Http\Requests;

use App\PaymentProduct;
use App\PaypalTransaction;
use Carbon\Carbon;
use Ignited\LaravelOmnipay\Facades\OmnipayFacade as Omnipay;
use \Auth;
use \Session;
use \URL;
use \Log;

class PaymentController extends Controller
{


    function __construct()
    {
        $this->middleware('admin', ['only' => [
            'verifyPendingTransactions',
            'refund',
            'allTransactions',
        ]]);
    }


    public function prepare()
    {
        $products = PaymentProduct::where('valid_until', '>', Carbon::now())->get();

        return view('payment.prepare', compact('products'));
    }


    public function purchase($product_id)
    {
        $user = Auth::user();
        if(is_null($user)) {
            flash('Du muss ein Konto haben und eingeloggt sein um Premium-Zugang zu kaufen.');
            return redirect('/auth/register');
        }

        $product = PaymentProduct::findOrFail($product_id);

        $params = [
            'cancelUrl'   => URL::to('payment/cancel'),
            'returnUrl'   => URL::to('payment/success'),
            'productId'   => $product->id,
            'name'        => $product->name,
            'description' => $product->description,
            'amount'      => $product->price,
            'currency'    => 'EUR',
            'noShipping'  => 1,
            'allowNote'   => 1,
        ];

        Session::put('params', $params);
        Session::save();

        $response = Omnipay::purchase($params)->send();
        Log::info($response->getData());

        if ($response->isSuccessful()) {
            $user = Auth::user();

            $transaction = PaypalTransaction::createFromDoExpressCheckoutPaymentResponse($response);
            $transaction->user_id = $user->id;
            $transaction->payment_product_id = $params['productId'];
            $transaction->save();

            if ($transaction->paymentstatus == 'Completed') {
                $product = PaymentProduct::find($params['productId']);
                $user->extendPremium($product->premium_months);
                $monat = $product->premium_months > 1 ? 'Monate' : 'Monat';
                flash()->success('Die Bestellung ist erfolgreich abgeschlossen. ' .
                    'Der Premium-Status wurde um ' . $product->premium_months . ' ' . $monat . ' verlängert.');

                return redirect('payment/transactions');
            } else {
                $errors = ['Transaktion ist noch nicht verifiziert'];

                return redirect('payment/transactions')->withErrors($errors);
            }
        } elseif ($response->isRedirect()) {
            // redirect to offsite payment gateway
            $response->redirect();
        } else {
            // payment failed: display message to customer
            $errors = ['Beim Bezahlvorgang ist ein Fehler aufgetreten (' . $response->getMessage() . ')'];

            return redirect('payment/prepare')->withErrors($errors);
        }
    }


    public function success()
    {
        $user = Auth::user();

        $params = Session::get('params');

        $completePurchaseRequest = Omnipay::completePurchase($params);
        //$completePurchaseRequest->setAmount(13112.00);
        $response = $completePurchaseRequest->send();
        Log::info($response->getData());

        if ($response->isSuccessful()) {
            $transaction = PaypalTransaction::createFromDoExpressCheckoutPaymentResponse($response);
            $transaction->user_id = $user->id;
            $transaction->payment_product_id = $params['productId'];
            $transaction->save();

            if ($transaction->paymentstatus == 'Completed') {
                $product = PaymentProduct::find($params['productId']);
                $user->extendPremium($product->premium_months);
                $monat = $product->premium_months > 1 ? 'Monate' : 'Monat';
                flash()->success('Die Bestellung ist erfolgreich abgeschlossen. ' .
                    'Der Premium-Status wurde um ' . $product->premium_months . ' ' . $monat . ' verlängert.');

                return redirect('payment/transactions');
            } else {
                $errors = ['Transaktion ist noch nicht verifiziert'];

                return redirect('payment/transactions')->withErrors($errors);
            }
        } else {
            // payment failed: display message to customer
            $errors = ['Beim Bezahlvorgang ist ein Fehler aufgetreten (' . $response->getMessage() . ')'];

            return redirect('payment/prepare')->withErrors($errors);
        }
    }


    public function cancel()
    {
        return redirect('payment/prepare')->withErrors(['Transaktion abgebrochen']);
    }


    public function verifyPendingTransactions()
    {
        $transactions = PaypalTransaction::where('paymentstatus', '=', 'Pending')
            ->where('updated_at', '<', Carbon::now()->subHours(2))
            ->get();

        $count = 0;
        foreach ($transactions as $transaction) {
            $transactionDetails = Omnipay::fetchTransaction(['transactionReference' => $transaction->transactionid])->send();
            $data = $transactionDetails->getData();
            Log::info($data);

            if ($data['PAYMENTSTATUS'] != $transaction->paymentstatus) {
                $transaction->paymentstatus = $data['PAYMENTSTATUS'];
                if ($transaction->paymentstatus == 'Completed') {
                    $product = $transaction->product;
                    $user = $transaction->user;
                    $user->extendPremium($product->premium_months);
                    $count++;
                }
                $transaction->save();
            } else {
                $transaction->touch();
            }
        }
        flash("$count transaction verified");

        return redirect()->back();
    }


    public function transactions()
    {
        $user = Auth::user();
        $transactions = $user->paypalTransactions()->with('product')->get();

        return view('payment.transactions', compact('transactions', 'user'));
    }


    public function allTransactions()
    {
        $default = config('laravel-omnipay.default');
        $paypalDomain = config("laravel-omnipay.gateways.{$default}.options.testMode") ?
            'www.sandbox.paypal.com' : 'www.paypal.com';

        $transactions = PaypalTransaction::with('user', 'product')->orderBy('ordertime', 'desc')->paginate(50);

        return view('payment.all_transactions', compact('transactions', 'paypalDomain'));
    }


    public function refund($transaction_id, $free = null)
    {

        $transaction = PaypalTransaction::findOrFail($transaction_id);

        $response = Omnipay::refund(['transactionReference' => $transaction->transactionid])->send();
        if ($response->isSuccessful() || $response->getMessage() == "This transaction has already been fully refunded") {
            $transaction->paymentstatus = 'Refunded';
            $transaction->save();
            if ($free != 'free') {
                $transaction->user->extendPremium($transaction->product->premium_months * -1);
            }
            flash()->success('Transaction successfully refunded');
        } else {
            flash()->error('Refund failed (' . $response->getMessage() . ')');
        }

        return redirect('payment/all-transactions');
    }
}
