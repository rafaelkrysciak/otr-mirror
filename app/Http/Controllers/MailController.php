<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use Illuminate\Http\Request;
use \Input;
use \Mail;

class MailController extends Controller {


    function __construct()
    {
        $this->middleware('admin');
    }


    public function compose()
    {
        return view('mail.compose');
	}


    public function send()
    {
        set_time_limit(1200);

        $recipients_type = Input::get('recipients_type');
        $recipients = Input::get('recipients');
        $subject = Input::get('subject');
        $body = nl2br(Input::get('body'));

        if($recipients_type == 'individual' || $recipients_type == 'both') {
            Mail::send('emails.standard', compact('subject', 'body'), function($message) use ($recipients, $subject)
            {
                $message->to($recipients)
                    ->subject($subject);
            });
            \Log::info('Sending mail '.$subject.' to '.$recipients.' succesful');
        }

        if($recipients_type == 'all') {
            $emails = User::where('confirmed','=','1')
            	->where('premium_valid_until','=','2015-07-13 23:59:59')
                ->orderBy('created_at')
                ->get(['email', 'name']);

            foreach($emails as $email) {
                try {
                    Mail::send('emails.standard', compact('subject', 'body'), function ($message) use ($email, $subject) {
                        $message->to($email->email, $email->name)
                            ->subject($subject);
                    });
                    \Log::info('Sending mail '.$subject.' to '.$email->email.' successful');
                } catch(\Exception $e) {
                    \Log::error('Sending mail '.$subject.' to '.$email->email.' failed '.$e->getMessage());
                }
                // SES limitation 14 Mails per Second
                usleep(75000);
            }
        }

        flash('Mail sent');
        return redirect()->back();
    }

}
